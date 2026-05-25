@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('breadcrumb')
    <span>Administration</span>
    <span class="mx-1">›</span>
    <span class="text-gray-700 font-medium">Users</span>
@endsection

@section('content')
<div x-data="usersPage()">

    {{-- Header bar --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Manage all system users, their roles and territory assignments.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Add User
        </a>
    </div>

    {{-- Stats bar --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        @php
            $statCounts = $users->groupBy('role.name');
        @endphp
        <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 shadow-sm">
            <p class="text-xs text-gray-500 font-medium">Total Users</p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $users->total() }}</p>
        </div>
        @foreach([['Sales Director','sales_director','purple'],['Zone Manager','zone_manager','blue'],['BDM','bdm','indigo'],['BDE','bde','yellow']] as [$label, $key, $color])
        <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 shadow-sm">
            <p class="text-xs text-gray-500 font-medium">{{ $label }}</p>
            <p class="text-2xl font-bold text-{{ $color }}-600 mt-0.5">
                {{ \App\Models\User::byRole($key)->count() }}
            </p>
        </div>
        @endforeach
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.users.index') }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name, email, employee ID…"
                       class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <select name="role_id"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->display_name }}
                    </option>
                @endforeach
            </select>

            <select name="state_id"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>

            <select name="status"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                Filter
            </button>

            @if(request()->hasAny(['search','role_id','state_id','status']))
            <a href="{{ route('admin.users.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                Clear
            </a>
            @endif
        </div>
    </form>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Territory</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Manager</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-semibold text-sm shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="font-medium text-gray-900 hover:text-yellow-600 transition">
                                        {{ $user->name }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    @if($user->employee_id)
                                    <p class="text-xs text-gray-400">{{ $user->employee_id }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $roleColors = [
                                    'super_admin'    => 'bg-red-100 text-red-700',
                                    'sales_director' => 'bg-purple-100 text-purple-700',
                                    'zone_manager'   => 'bg-blue-100 text-blue-700',
                                    'bdm'            => 'bg-indigo-100 text-indigo-700',
                                    'bde'            => 'bg-yellow-100 text-yellow-700',
                                ];
                                $roleClass = $roleColors[$user->role?->name] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="badge {{ $roleClass }} px-2.5 py-1">
                                {{ $user->role?->display_name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-gray-700">{{ $user->getTerritoryLabel() }}</p>
                            @if($user->city)
                                <p class="text-xs text-gray-400">{{ $user->district?->name }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ $user->manager?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $statusColors = ['active' => 'bg-green-100 text-green-700', 'inactive' => 'bg-gray-100 text-gray-500', 'suspended' => 'bg-red-100 text-red-700'];
                            @endphp
                            <span class="badge {{ $statusColors[$user->status] ?? '' }} px-2.5 py-1">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-sm">
                            {{ $user->date_of_joining?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                   title="View">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition"
                                   title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Remove {{ $user->name }} from the system? This action is reversible.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Remove">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                <i data-lucide="users" class="w-10 h-10 opacity-40"></i>
                                <p class="font-medium">No users found</p>
                                <p class="text-sm">Try adjusting your filters or add a new user.</p>
                                <a href="{{ route('admin.users.create') }}"
                                   class="mt-2 text-yellow-600 hover:text-yellow-700 text-sm font-medium">
                                    + Add first user
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="border-t border-gray-100 px-5 py-3 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
            </p>
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function usersPage() {
    return {};
}
lucide.createIcons();
</script>
@endpush
