@extends('layouts.app')
@section('title','Audit Logs')
@section('page-title','Audit Logs')

@section('content')

{{-- Filters --}}
<form method="GET" class="card p-4 mb-6">
    <div class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search action, model, user…"
               class="flex-1 min-w-48 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">

        <select name="action" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">All Actions</option>
            @foreach($actions as $action)
            <option value="{{ $action }}" {{ request('action')===$action?'selected':'' }}>
                {{ ucwords(str_replace('_',' ',$action)) }}
            </option>
            @endforeach
        </select>

        <select name="user_id" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">All Users</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
            @endforeach
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">

        <button type="submit" class="px-5 py-2.5 text-white text-sm font-medium rounded-xl"
                style="background:linear-gradient(135deg,#1A1A2E,#252542)">Apply</button>
        @if(request()->hasAny(['search','action','user_id','date_from','date_to']))
        <a href="{{ route('admin.audit-logs.index') }}"
           class="px-4 py-2.5 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50">Clear</a>
        @endif
    </div>
</form>

{{-- Log Table --}}
<div class="card overflow-hidden">
    <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-500 font-medium">{{ $logs->total() }} log entries</p>
        <p class="text-xs text-gray-400">Showing latest first</p>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Model</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">IP Address</th>
                <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Details</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($logs as $log)
            @php
                $actionColors = [
                    'login'          => 'bg-blue-100 text-blue-700',
                    'logout'         => 'bg-gray-100 text-gray-600',
                    'login_failed'   => 'bg-red-100 text-red-700',
                    'create'         => 'bg-green-100 text-green-700',
                    'update'         => 'bg-yellow-100 text-yellow-700',
                    'delete'         => 'bg-red-100 text-red-700',
                    'approve'        => 'bg-green-100 text-green-700',
                    'reject'         => 'bg-red-100 text-red-700',
                    'submit_for_approval' => 'bg-purple-100 text-purple-700',
                    'password_changed'    => 'bg-orange-100 text-orange-700',
                    'password_reset'      => 'bg-orange-100 text-orange-700',
                    'mark_won'       => 'bg-green-100 text-green-700',
                    'revision_requested'  => 'bg-orange-100 text-orange-700',
                ];
                $actionClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <tr class="hover:bg-gray-50 transition group">
                <td class="px-5 py-3.5">
                    <p class="text-sm text-gray-800 font-medium">{{ $log->created_at->format('d M Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $log->created_at->format('h:i:s A') }}</p>
                </td>
                <td class="px-5 py-3.5">
                    @if($log->user)
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0"
                             style="background:linear-gradient(135deg,#B8960C,#8B6914)">
                            {{ strtoupper(substr($log->user->name,0,1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $log->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $log->user->role?->display_name }}</p>
                        </div>
                    </div>
                    @else
                    <span class="text-gray-400 text-sm">System</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <span class="badge {{ $actionClass }}">
                        {{ ucwords(str_replace('_',' ',$log->action)) }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-gray-600">
                    @if($log->model_type)
                    <p class="text-sm font-medium text-gray-700">{{ $log->model_type }}</p>
                    @if($log->model_id)
                    <p class="text-xs text-gray-400">ID: {{ $log->model_id }}</p>
                    @endif
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <p class="text-sm text-gray-600 font-mono text-xs">{{ $log->ip_address ?? '—' }}</p>
                </td>
                <td class="px-5 py-3.5 text-right">
                    @if($log->old_values || $log->new_values)
                    <button onclick="document.getElementById('log-{{ $log->id }}').classList.toggle('hidden')"
                            class="text-xs text-yellow-600 hover:underline font-medium">
                        View changes
                    </button>
                    @else
                    <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
            </tr>
            {{-- Expandable details row --}}
            @if($log->old_values || $log->new_values)
            <tr id="log-{{ $log->id }}" class="hidden bg-gray-50">
                <td colspan="6" class="px-5 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($log->old_values)
                        <div>
                            <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">Before</p>
                            <div class="bg-red-50 border border-red-100 rounded-xl p-3 text-xs font-mono text-red-800 overflow-auto max-h-40">
                                @foreach(array_slice($log->old_values, 0, 10) as $key => $val)
                                @if(!in_array($key, ['password','remember_token','updated_at']))
                                <div class="flex gap-2 py-0.5 border-b border-red-100 last:border-0">
                                    <span class="text-red-400 shrink-0 w-28">{{ $key }}</span>
                                    <span class="text-red-700 truncate">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($log->new_values)
                        <div>
                            <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">After</p>
                            <div class="bg-green-50 border border-green-100 rounded-xl p-3 text-xs font-mono text-green-800 overflow-auto max-h-40">
                                @foreach(array_slice($log->new_values, 0, 10) as $key => $val)
                                @if(!in_array($key, ['password','remember_token','updated_at']))
                                <div class="flex gap-2 py-0.5 border-b border-green-100 last:border-0">
                                    <span class="text-green-400 shrink-0 w-28">{{ $key }}</span>
                                    <span class="text-green-700 truncate">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            @endif

            @empty
            <tr>
                <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                    <i data-lucide="shield-check" class="w-12 h-12 mx-auto opacity-20 mb-3"></i>
                    <p class="font-medium text-gray-500">No audit logs found</p>
                    <p class="text-sm mt-1">Try adjusting your filters.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($logs->hasPages())
    <div class="border-t border-gray-100 px-5 py-3.5 flex items-center justify-between">
        <p class="text-sm text-gray-400">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} entries</p>
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
