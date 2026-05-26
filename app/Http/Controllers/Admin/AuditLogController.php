<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller {
    public function index(Request $request) {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('action','like',"%$s%")
                  ->orWhere('model_type','like',"%$s%")
                  ->orWhereHas('user', fn($q2) => $q2->where('name','like',"%$s%"))
            );
        }
        if ($request->filled('action'))  $query->where('action', $request->action);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        $logs  = $query->paginate(30)->withQueryString();
        $users = User::active()->orderBy('name')->get();
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs.index', compact('logs','users','actions'));
    }
}
