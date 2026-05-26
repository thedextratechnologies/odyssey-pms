@extends('layouts.app')
@section('title','Leads & Customers')
@section('page-title','Leads & Customers')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <p class="text-sm text-gray-500">Track all customer leads through the sales pipeline.</p>
    <a href="{{ route('leads.create') }}" class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
        <i data-lucide="user-plus" class="w-4 h-4"></i> New Lead
    </a>
</div>

{{-- Pipeline funnel --}}
<div class="grid grid-cols-4 lg:grid-cols-8 gap-2 mb-6">
    @foreach(App\Models\Lead::STAGES as $key => $stage)
    <a href="{{ route('leads.index', ['stage'=>$key]) }}"
       class="bg-white rounded-xl border-2 {{ request('stage')===$key ? 'border-yellow-400' : 'border-gray-100' }} p-3 text-center hover:border-yellow-300 transition shadow-sm">
        <p class="text-xl font-bold text-gray-900">{{ $stageCounts[$key] ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ $stage['label'] }}</p>
    </a>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or phone…"
               class="flex-1 min-w-48 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <select name="stage" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">All Stages</option>
            @foreach(App\Models\Lead::STAGES as $key=>$s)
            <option value="{{ $key }}" {{ request('stage')===$key?'selected':'' }}>{{ $s['label'] }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg">Filter</button>
        @if(request()->hasAny(['search','stage']))
        <a href="{{ route('leads.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-100">Clear</a>
        @endif
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Property</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Stage</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Assigned To</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Follow Up</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Quotes</th>
                <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($leads as $lead)
            @php
                $colors = ['new'=>'gray','contacted'=>'blue','site_visit_scheduled'=>'purple','quotation_sent'=>'yellow','negotiation'=>'orange','won'=>'green','lost'=>'red','on_hold'=>'gray'];
                $c = $colors[$lead->stage] ?? 'gray';
                $overdue = $lead->isOverdue();
            @endphp
            <tr class="hover:bg-gray-50 transition group {{ $overdue ? 'bg-red-50/30' : '' }}">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-semibold text-sm shrink-0">
                            {{ strtoupper(substr($lead->customer?->name ?? 'X', 0, 1)) }}
                        </div>
                        <div>
                            <a href="{{ route('leads.show', $lead) }}" class="font-medium text-gray-900 hover:text-yellow-600">{{ $lead->customer?->name }}</a>
                            <p class="text-xs text-gray-500">{{ $lead->customer?->phone }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-gray-600">
                    <p>{{ ucfirst($lead->customer?->property_type ?? '—') }}</p>
                    <p class="text-xs text-gray-400">{{ $lead->customer?->num_floors }} floors</p>
                </td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700">
                        {{ $lead->stage_label }}
                    </span>
                </td>
                <td class="px-5 py-4 text-gray-600 text-sm">{{ $lead->assignedTo?->name ?? '—' }}</td>
                <td class="px-5 py-4">
                    @if($lead->follow_up_at)
                        <span class="text-sm {{ $overdue ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                            {{ $overdue ? '⚠ ' : '' }}{{ $lead->follow_up_at->format('d M, h:i A') }}
                        </span>
                    @else
                        <span class="text-gray-400 text-sm">—</span>
                    @endif
                </td>
                <td class="px-5 py-4">
                    <span class="text-sm text-gray-600">{{ $lead->quotations->count() }}</span>
                </td>
                <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition">
                        <a href="{{ route('leads.show', $lead) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="View">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('quotations.create', ['lead_id'=>$lead->id]) }}" class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg" title="New Quote">
                            <i data-lucide="file-plus" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('leads.edit', $lead) }}" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg" title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">
                <i data-lucide="users" class="w-10 h-10 mx-auto opacity-30 mb-2"></i>
                <p class="font-medium">No leads found</p>
                <a href="{{ route('leads.create') }}" class="text-yellow-600 text-sm mt-2 inline-block">+ Create your first lead</a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    @if($leads->hasPages())
    <div class="border-t border-gray-100 px-5 py-3 flex items-center justify-between">
        <p class="text-sm text-gray-500">Showing {{ $leads->firstItem() }}–{{ $leads->lastItem() }} of {{ $leads->total() }}</p>
        {{ $leads->links() }}
    </div>
    @endif
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
