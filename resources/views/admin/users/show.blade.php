@extends('layouts.app')
@section('title', $user->name)
@section('page-title', $user->name)
@section('breadcrumb')
    <span>Admin</span> <span class="mx-1">›</span>
    <a href="{{ route('admin.users.index') }}" class="hover:underline">Users</a> <span class="mx-1">›</span>
    <span class="text-gray-700 font-medium">{{ $user->name }}</span>
@endsection

@section('content')
<div class="max-w-4xl">
    {{-- Header card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6 flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-2xl">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    @php
                        $roleColors = ['super_admin'=>'bg-red-100 text-red-700','sales_director'=>'bg-purple-100 text-purple-700','zone_manager'=>'bg-blue-100 text-blue-700','bdm'=>'bg-indigo-100 text-indigo-700','bde'=>'bg-yellow-100 text-yellow-700'];
                        $statusColors = ['active'=>'bg-green-100 text-green-700','inactive'=>'bg-gray-100 text-gray-500','suspended'=>'bg-red-100 text-red-700'];
                    @endphp
                    <span class="badge {{ $roleColors[$user->role?->name] ?? 'bg-gray-100 text-gray-600' }} px-2.5 py-1 text-xs font-semibold rounded-full">
                        {{ $user->role?->display_name }}
                    </span>
                    <span class="badge {{ $statusColors[$user->status] ?? '' }} px-2.5 py-1 text-xs font-semibold rounded-full">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user) }}"
               class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i data-lucide="pencil" class="w-4 h-4"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                @csrf
                <button type="submit" onclick="return confirm('Send password reset to {{ $user->email }}?')"
                        class="flex items-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition">
                    <i data-lucide="key" class="w-4 h-4"></i> Reset Password
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Details --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">User Details</h3>
            <dl class="space-y-3">
                @foreach([
                    ['Employee ID', $user->employee_id ?? '—'],
                    ['Phone', $user->phone ?? '—'],
                    ['Territory', $user->getTerritoryLabel()],
                    ['State', $user->state?->name ?? '—'],
                    ['District', $user->district?->name ?? '—'],
                    ['City', $user->city?->name ?? '—'],
                    ['Manager', $user->manager?->name ?? '—'],
                    ['Joined', $user->date_of_joining?->format('d M Y') ?? '—'],
                ] as [$label, $value])
                <div class="flex items-start justify-between gap-4">
                    <dt class="text-sm text-gray-500 shrink-0">{{ $label }}</dt>
                    <dd class="text-sm font-medium text-gray-800 text-right">{{ $value }}</dd>
                </div>
                @endforeach
            </dl>
        </div>

        {{-- Recent Audit Log --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Recent Activity</h3>
            @forelse($recentLogs as $log)
            <div class="flex items-start gap-3 py-2 border-b border-gray-50 last:border-0">
                <div class="w-2 h-2 rounded-full bg-yellow-400 mt-1.5 shrink-0"></div>
                <div>
                    <p class="text-sm text-gray-700 font-medium">{{ ucwords(str_replace('_',' ',$log->action)) }}</p>
                    <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }} · {{ $log->ip_address }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 py-4 text-center">No activity recorded yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Subordinates --}}
    @if($user->subordinates->count())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mt-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">
            Team ({{ $user->subordinates->count() }} members)
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($user->subordinates as $sub)
            <a href="{{ route('admin.users.show', $sub) }}"
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition border border-gray-100">
                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-semibold shrink-0">
                    {{ strtoupper(substr($sub->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $sub->name }}</p>
                    <p class="text-xs text-gray-500">{{ $sub->role?->display_name }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
