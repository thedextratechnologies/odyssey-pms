<?php
namespace App\Http\Controllers\Territory;
use App\Http\Controllers\Controller;
use App\Models\Territory;
use Illuminate\Http\Request;

class TerritoryController extends Controller {
    public function index(Request $request) {
        $type   = $request->get('type', 'state');
        $search = $request->get('search');

        $query = Territory::with('parent')->where('type', $type);
        if ($search) $query->where('name', 'like', "%$search%");
        $territories = $query->orderBy('name')->paginate(30)->withQueryString();

        $counts = [
            'state'    => Territory::where('type','state')->count(),
            'district' => Territory::where('type','district')->count(),
            'city'     => Territory::where('type','city')->count(),
        ];

        $states    = Territory::states();
        $districts = $request->state_id ? Territory::districtsFor($request->state_id) : collect();

        return view('territories.index', compact('territories','type','counts','states','districts'));
    }

    public function store(Request $request) {
        $v = $request->validate([
            'type'      => 'required|in:state,district,city',
            'name'      => 'required|string|max:100',
            'parent_id' => 'nullable|exists:territories,id',
        ]);
        Territory::create(array_merge($v, ['is_active' => true]));
        return back()->with('success', ucfirst($v['type']).' added successfully.');
    }

    public function update(Request $request, Territory $territory) {
        $v = $request->validate([
            'name'      => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);
        $territory->update($v);
        return back()->with('success', 'Territory updated.');
    }

    public function destroy(Territory $territory) {
        if ($territory->children()->count() > 0) {
            return back()->with('error', 'Cannot delete — this territory has sub-territories. Remove children first.');
        }
        $territory->delete();
        return back()->with('success', ucfirst($territory->type).' deleted.');
    }

    public function toggle(Territory $territory) {
        $territory->update(['is_active' => !$territory->is_active]);
        return back()->with('success', 'Territory '.($territory->is_active ? 'deactivated' : 'activated').'.');
    }

    public function getDistricts(Request $request) {
        return response()->json(Territory::districtsFor($request->state_id));
    }
}
