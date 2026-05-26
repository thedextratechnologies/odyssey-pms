<?php
namespace App\Http\Controllers\Quotation;
use App\Http\Controllers\Controller;
use App\Models\{Quotation, QuotationItem, Lead, Product, ProductAddon, Approval, Notification, AuditLog, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller {
    public function index(Request $request) {
        $user  = Auth::user();
        $query = Quotation::with(['customer','product','createdBy'])->visibleTo($user);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q)=>$q->where('quote_number','like',"%$s%")->orWhereHas('customer',fn($q2)=>$q2->where('name','like',"%$s%")));
        }
        $quotations = $query->latest()->paginate(20)->withQueryString();
        return view('quotations.index', compact('quotations'));
    }

    public function create(Request $request) {
        $leads    = Lead::with('customer')->visibleTo(Auth::user())->whereNotIn('stage',['won','lost'])->get();
        $products = Product::where('is_active', true)->orderBy('family')->get();
        $selectedLead = $request->lead_id ? Lead::with('customer')->find($request->lead_id) : null;
        return view('quotations.create', compact('leads','products','selectedLead'));
    }

    public function store(Request $request) {
        $v = $request->validate([
            'lead_id'       => 'required|exists:leads,id',
            'product_id'    => 'required|exists:products,id',
            'notes'         => 'nullable|string',
            'valid_days'    => 'required|integer|min:7|max:90',
            'items'         => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.category'    => 'required|string',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($v, $request) {
            $lead     = Lead::findOrFail($v['lead_id']);
            $subtotal = collect($v['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $gstRate  = 18;
            $gstAmt   = round($subtotal * $gstRate / 100, 2);
            $total    = $subtotal + $gstAmt;

            $q = Quotation::create([
                'quote_number'  => Quotation::generateNumber(),
                'lead_id'       => $v['lead_id'],
                'customer_id'   => $lead->customer_id,
                'created_by'    => Auth::id(),
                'product_id'    => $v['product_id'],
                'subtotal'      => $subtotal,
                'gst_rate'      => $gstRate,
                'gst_amount'    => $gstAmt,
                'total'         => $total,
                'status'        => 'draft',
                'valid_until'   => now()->addDays($v['valid_days']),
                'notes'         => $v['notes'] ?? null,
            ]);

            foreach ($v['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $q->id,
                    'description'  => $item['description'],
                    'category'     => $item['category'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'amount'       => $item['quantity'] * $item['unit_price'],
                ]);
            }

            AuditLog::record('create', $q);
            $this->quotation = $q;
        });

        return redirect()->route('quotations.show', $this->quotation ?? Quotation::latest()->first())
            ->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation) {
        $quotation->load(['customer','product','createdBy','items','approvals.approver','lead']);
        $canApprove = $quotation->canBeApprovedBy(Auth::user());
        return view('quotations.show', compact('quotation','canApprove'));
    }

    public function submit(Quotation $quotation) {
        if ($quotation->status !== 'draft' && $quotation->status !== 'revision_requested') {
            return back()->with('error', 'Only draft or revision-requested quotes can be submitted.');
        }
        // Route to correct approval stage based on value
        $total = $quotation->total;
        if ($total < 500000) {
            $nextStatus = 'pending_bdm';
            $nextRole   = 'bdm';
        } elseif ($total < 1500000) {
            $nextStatus = 'pending_zm';
            $nextRole   = 'zone_manager';
        } else {
            $nextStatus = 'pending_sd';
            $nextRole   = 'sales_director';
        }

        $quotation->update(['status' => $nextStatus]);
        Approval::create(['quotation_id'=>$quotation->id,'role_level'=>$nextRole,'status'=>'pending']);

        // Update lead stage
        $quotation->lead->update(['stage' => 'quotation_sent']);
        AuditLog::record('submit_for_approval', $quotation);

        return back()->with('success', 'Quotation submitted for approval.');
    }

    public function approve(Request $request, Quotation $quotation) {
        if (!$quotation->canBeApprovedBy(Auth::user())) abort(403);
        $request->validate(['comment' => 'nullable|string']);

        // Determine if needs further approval or is fully approved
        $user = Auth::user();
        $nextStatus = 'approved';
        if ($quotation->status === 'pending_bdm') {
            if (!$user->isZoneManager() && !$user->isSalesDirector() && !$user->isSuperAdmin()) {
                // BDM approved — check value for ZM
                if ($quotation->total >= 1500000) $nextStatus = 'pending_zm';
            }
        } elseif ($quotation->status === 'pending_zm') {
            if ($quotation->total >= 1500000 && !$user->isSalesDirector() && !$user->isSuperAdmin()) {
                $nextStatus = 'pending_sd';
            }
        }

        Approval::where('quotation_id', $quotation->id)->where('status','pending')
            ->update(['status'=>'approved','approver_id'=>Auth::id(),'comment'=>$request->comment,'actioned_at'=>now()]);

        $quotation->update(['status' => $nextStatus]);
        if ($nextStatus === 'approved') $quotation->lead->update(['stage' => 'negotiation']);
        AuditLog::record('approve', $quotation);

        return back()->with('success', $nextStatus === 'approved' ? 'Quotation fully approved!' : 'Approved and escalated to next level.');
    }

    public function reject(Request $request, Quotation $quotation) {
        if (!$quotation->canBeApprovedBy(Auth::user())) abort(403);
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        Approval::where('quotation_id', $quotation->id)->where('status','pending')
            ->update(['status'=>'rejected','approver_id'=>Auth::id(),'comment'=>$request->rejection_reason,'actioned_at'=>now()]);

        $quotation->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        AuditLog::record('reject', $quotation);

        return back()->with('success', 'Quotation rejected.');
    }

    public function requestRevision(Request $request, Quotation $quotation) {
        if (!$quotation->canBeApprovedBy(Auth::user())) abort(403);
        $request->validate(['comment' => 'required|string|max:500']);

        Approval::where('quotation_id', $quotation->id)->where('status','pending')
            ->update(['status'=>'revision_requested','approver_id'=>Auth::id(),'comment'=>$request->comment,'actioned_at'=>now()]);

        $quotation->update(['status' => 'revision_requested', 'rejection_reason' => $request->comment]);
        AuditLog::record('revision_requested', $quotation);

        return back()->with('success', 'Revision requested. BDE has been notified.');
    }

    public function markWon(Quotation $quotation) {
        $quotation->update(['status' => 'won']);
        $quotation->lead->update(['stage' => 'won']);
        AuditLog::record('mark_won', $quotation);
        return back()->with('success', '🎉 Quotation marked as Won!');
    }

    public function markLost(Request $request, Quotation $quotation) {
        $request->validate(['lost_reason' => 'required|string|max:255']);
        $quotation->update(['status' => 'lost']);
        $quotation->lead->update(['stage' => 'lost', 'lost_reason' => $request->lost_reason]);
        return back()->with('success', 'Quotation marked as lost.');
    }

    public function pendingApprovals() {
        $user  = Auth::user();
        $query = Quotation::with(['customer','product','createdBy','lead']);

        if ($user->isBDM()) $query->where('status','pending_bdm');
        elseif ($user->isZoneManager()) $query->whereIn('status',['pending_bdm','pending_zm']);
        elseif ($user->isSalesDirector() || $user->isSuperAdmin()) $query->whereIn('status',['pending_bdm','pending_zm','pending_sd']);
        else $query->whereRaw('1=0');

        $quotations = $query->latest()->paginate(20);
        return view('quotations.approvals', compact('quotations'));
    }
}
