<?php
namespace App\Http\Controllers\Lead;
use App\Http\Controllers\Controller;
use App\Models\{Lead, Customer, Franchise, Territory, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller {
    public function index(Request $request) {
        $user = Auth::user();
        $query = Lead::with(['customer','assignedTo','quotations'])->visibleTo($user);
        if ($request->filled('stage')) $query->where('stage', $request->stage);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('customer', fn($q)=>$q->where('name','like',"%$s%")->orWhere('phone','like',"%$s%"));
        }
        $leads = $query->latest()->paginate(20)->withQueryString();
        $stageCounts = [];
        foreach (Lead::STAGES as $key => $val) {
            $stageCounts[$key] = Lead::query()->visibleTo($user)->where('stage',$key)->count();
        }
        return view('leads.index', compact('leads','stageCounts'));
    }

    public function create() {
        $franchises = Franchise::where('status','active')->orderBy('company_name')->get();
        $states = Territory::states();
        return view('leads.create', compact('franchises','states'));
    }

    public function store(Request $request) {
        $v = $request->validate([
            'name'          => 'required|string|max:100',
            'phone'         => 'required|string|max:20',
            'email'         => 'nullable|email',
            'address'       => 'nullable|string',
            'state_id'      => 'nullable|exists:territories,id',
            'district_id'   => 'nullable|exists:territories,id',
            'city_id'       => 'nullable|exists:territories,id',
            'property_type' => 'required|in:villa,duplex,penthouse,apartment,bungalow,other',
            'num_floors'    => 'required|integer|min:1|max:20',
            'budget_range'  => 'nullable|string',
            'source'        => 'required|in:direct,franchise,referral,digital,walk_in,other',
            'franchise_id'  => 'nullable|exists:franchises,id',
            'notes'         => 'nullable|string',
        ]);
        $customer = Customer::firstOrCreate(['phone' => $v['phone']], array_merge($v, ['assigned_to' => Auth::id()]));
        $lead = Lead::create(['customer_id' => $customer->id, 'assigned_to' => Auth::id(), 'stage' => 'new', 'notes' => $v['notes'] ?? null]);
        AuditLog::record('create', $lead);
        return redirect()->route('leads.show', $lead)->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead) {
        $lead->load(['customer.franchise','customer.state','customer.city','assignedTo','quotations.product']);
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead) {
        return view('leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead) {
        $v = $request->validate([
            'stage'          => 'required|in:'.implode(',', array_keys(Lead::STAGES)),
            'follow_up_at'   => 'nullable|date',
            'site_visit_date'=> 'nullable|date',
            'lost_reason'    => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
        ]);
        $lead->update($v);
        AuditLog::record('update', $lead);
        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead) {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead removed.');
    }
}
