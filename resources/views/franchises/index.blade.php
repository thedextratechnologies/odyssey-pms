@extends('layouts.app')
@section('title','Franchise Partners')
@section('page-title','Franchise Partners')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage franchise partner companies that source customer leads.</p>
    <a href="{{ route('franchises.create') }}" class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
        <i data-lucide="building-2" class="w-4 h-4"></i> Add Franchise
    </a>
</div>

<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, owner, phone…"
               class="flex-1 min-w-48 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">All Status</option>
            <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
        </select>
        <select name="state_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">All States</option>
            @foreach($states as $s)
            <option value="{{ $s->id }}" {{ request('state_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Filter</button>
        @if(request()->hasAny(['search','status','state_id']))
        <a href="{{ route('franchises.index') }}" class="text-sm text-gray-500 px-3 py-2 rounded-lg hover:bg-gray-100">Clear</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Company</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Owner</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">State</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Manager</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="text-right px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($franchises as $f)
            <tr class="hover:bg-gray-50 transition group">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                            {{ strtoupper(substr($f->company_name, 0, 1)) }}
                        </div>
                        <div>
                            <a href="{{ route('franchises.show', $f) }}" class="font-medium text-gray-900 hover:text-yellow-600">{{ $f->company_name }}</a>
                            <p class="text-xs text-gray-500">{{ $f->phone }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-gray-600">{{ $f->owner_name }}</td>
                <td class="px-5 py-4 text-gray-600">{{ $f->state?->name }}</td>
                <td class="px-5 py-4 text-gray-600">{{ $f->manager?->name ?? '—' }}</td>
                <td class="px-5 py-4">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $f->status==='active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($f->status) }}
                    </span>
                </td>
                <td class="px-5 py-4 text-right opacity-0 group-hover:opacity-100 transition">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('franchises.show', $f) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg"><i data-lucide="eye" class="w-4 h-4"></i></a>
                        <a href="{{ route('franchises.edit', $f) }}" class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">
                <i data-lucide="building-2" class="w-10 h-10 mx-auto opacity-30 mb-2"></i>
                <p>No franchise partners yet.</p>
                <a href="{{ route('franchises.create') }}" class="text-yellow-600 text-sm mt-1 inline-block">Add first franchise →</a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    @if($franchises->hasPages())
    <div class="border-t border-gray-100 px-5 py-3">{{ $franchises->links() }}</div>
    @endif
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
