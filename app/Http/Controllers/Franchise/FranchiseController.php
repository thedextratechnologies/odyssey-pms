<?php
namespace App\Http\Controllers\Franchise;
use App\Http\Controllers\Controller;
use App\Models\{Franchise, Territory, User, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FranchiseController extends Controller {
    public function index(Request $request) {
        $query = Franchise::with(['state','manager']);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q)=>$q->where('company_name','like',"%$s%")->orWhere('owner_name','like',"%$s%")->orWhere('phone','like',"%$s%"));
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('state_id')) $query->where('state_id', $request->state_id);
        $franchises = $query->latest()->paginate(20)->withQueryString();
        $states = Territory::states();
        return view('franchises.index', compact('franchises','states'));
    }

    public function create() {
        $states = Territory::states();
        $managers = User::whereHas('role', fn($q)=>$q->whereIn('name',['bdm','zone_manager','sales_director']))->active()->orderBy('name')->get();
        return view('franchises.create', compact('states','managers'));
    }

    public function store(Request $request) {
        $v = $request->validate([
            'company_name'   => 'required|string|max:100',
            'owner_name'     => 'required|string|max:100',
            'phone'          => 'required|string|max:20|unique:franchises,phone',
            'email'          => 'nullable|email',
            'state_id'       => 'required|exists:territories,id',
            'district_id'    => 'nullable|exists:territories,id',
            'managed_by'     => 'nullable|exists:users,id',
            'agreement_date' => 'nullable|date',
            'status'         => 'required|in:active,inactive',
            'notes'          => 'nullable|string',
        ]);
        $franchise = Franchise::create($v);
        AuditLog::record('create', $franchise);
        return redirect()->route('franchises.index')->with('success', 'Franchise partner added.');
    }

    public function show(Franchise $franchise) {
        $franchise->load(['state','district','manager','customers.leads']);
        $totalLeads    = $franchise->customers()->withCount('leads')->get()->sum('leads_count');
        $wonLeads      = $franchise->customers()->whereHas('leads',fn($q)=>$q->where('stage','won'))->count();
        return view('franchises.show', compact('franchise','totalLeads','wonLeads'));
    }

    public function edit(Franchise $franchise) {
        $states   = Territory::states();
        $managers = User::whereHas('role', fn($q)=>$q->whereIn('name',['bdm','zone_manager','sales_director']))->active()->orderBy('name')->get();
        return view('franchises.edit', compact('franchise','states','managers'));
    }

    public function update(Request $request, Franchise $franchise) {
        $v = $request->validate([
            'company_name'   => 'required|string|max:100',
            'owner_name'     => 'required|string|max:100',
            'phone'          => 'required|string|max:20|unique:franchises,phone,'.$franchise->id,
            'email'          => 'nullable|email',
            'state_id'       => 'required|exists:territories,id',
            'district_id'    => 'nullable|exists:territories,id',
            'managed_by'     => 'nullable|exists:users,id',
            'agreement_date' => 'nullable|date',
            'status'         => 'required|in:active,inactive',
            'notes'          => 'nullable|string',
        ]);
        $franchise->update($v);
        AuditLog::record('update', $franchise);
        return redirect()->route('franchises.show', $franchise)->with('success', 'Franchise updated.');
    }

    public function destroy(Franchise $franchise) {
        $franchise->delete();
        return redirect()->route('franchises.index')->with('success', 'Franchise removed.');
    }
}
