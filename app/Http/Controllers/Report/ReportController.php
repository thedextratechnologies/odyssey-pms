<?php
namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;
use App\Models\{Lead, Quotation, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller {
    public function index() {
        $user = Auth::user();

        // Lead funnel
        $funnelData = [];
        foreach (Lead::STAGES as $key => $val) {
            $funnelData[$key] = [
                'label' => $val['label'],
                'color' => $val['color'],
                'count' => Lead::query()->visibleTo($user)->where('stage',$key)->count()
            ];
        }

        // Monthly quotations
        $monthlyQuotes = Quotation::query()->visibleTo($user)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total) as value')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')->orderBy('month')->get();

        // Product mix
        $productMix = Quotation::query()->visibleTo($user)
            ->join('products','quotations.product_id','=','products.id')
            ->selectRaw('products.family, COUNT(*) as count, SUM(quotations.total) as value')
            ->groupBy('products.family')->get();

        // Summary stats
        $stats = [
            'total_leads'     => Lead::query()->visibleTo($user)->count(),
            'won_leads'       => Lead::query()->visibleTo($user)->where('stage','won')->count(),
            'total_quotes'    => Quotation::query()->visibleTo($user)->count(),
            'approved_quotes' => Quotation::query()->visibleTo($user)->where('status','approved')->count(),
            'pipeline_value'  => Quotation::query()->visibleTo($user)->whereIn('status',['pending_bdm','pending_zm','pending_sd','approved'])->sum('total'),
            'won_value'       => Quotation::query()->visibleTo($user)->where('status','won')->sum('total'),
        ];

        return view('reports.index', compact('funnelData','monthlyQuotes','productMix','stats'));
    }

    public function quotations(Request $request) {
        $user  = Auth::user();
        $query = Quotation::with(['customer','product','createdBy'])->visibleTo($user);
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('created_at','>=',$request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at','<=',$request->date_to);
        $quotations = $query->latest()->paginate(50)->withQueryString();
        return view('reports.quotations', compact('quotations'));
    }
}
