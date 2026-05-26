@extends('layouts.app')
@section('title', $lead->customer?->name)
@section('page-title', $lead->customer?->name)
@section('breadcrumb')
<a href="{{ route('leads.index') }}" class="hover:underline">Leads</a> <span class="mx-1">›</span>
<span class="text-gray-700 font-medium">{{ $lead->customer?->name }}</span>
@endsection

@section('content')
@php
$stageColors = ['new'=>'gray','contacted'=>'blue','site_visit_scheduled'=>'purple','quotation_sent'=>'yellow','negotiation'=>'orange','won'=>'green','lost'=>'red','on_hold'=>'gray'];
$c = $stageColors[$lead->stage] ?? 'gray';
@endphp

<div class="max-w-5xl">

{{-- Header --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6 flex flex-wrap items-start justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-xl shrink-0">
            {{ strtoupper(substr($lead->customer?->name ?? 'X', 0, 1)) }}
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $lead->customer?->name }}</h2>
            <p class="text-gray-500 text-sm">{{ $lead->customer?->phone }} @if($lead->customer?->email)· {{ $lead->customer->email }}@endif</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700">{{ $lead->stage_label }}</span>
                @if($lead->isOverdue())
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">⚠ Follow-up Overdue</span>
                @endif
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('quotations.create', ['lead_id'=>$lead->id]) }}"
           class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i data-lucide="file-plus" class="w-4 h-4"></i> New Quotation
        </a>
        <a href="{{ route('leads.edit', $lead) }}"
           class="flex items-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition">
            <i data-lucide="pencil" class="w-4 h-4"></i> Update Stage
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Details --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Property Details</h3>
            <dl class="space-y-3">
                @foreach([
                    ['Type', ucfirst($lead->customer?->property_type ?? '—')],
                    ['Floors', $lead->customer?->num_floors ?? '—'],
                    ['Budget', $lead->customer?->budget_range ?? '—'],
                    ['Location', $lead->customer?->city?->name ?? $lead->customer?->state?->name ?? '—'],
                    ['Source', ucfirst(str_replace('_',' ',$lead->customer?->source ?? '—'))],
                    ['Franchise', $lead->customer?->franchise?->company_name ?? '—'],
                    ['Assigned To', $lead->assignedTo?->name ?? '—'],
                ] as [$label, $value])
                <div class="flex justify-between gap-2">
                    <dt class="text-sm text-gray-500 shrink-0">{{ $label }}</dt>
                    <dd class="text-sm font-medium text-gray-800 text-right">{{ $value }}</dd>
                </div>
                @endforeach
            </dl>
        </div>

        @if($lead->follow_up_at || $lead->site_visit_date || $lead->notes)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Follow-up & Notes</h3>
            @if($lead->follow_up_at)
            <p class="text-sm {{ $lead->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-700' }} mb-2">
                📅 Follow-up: {{ $lead->follow_up_at->format('d M Y, h:i A') }}
            </p>
            @endif
            @if($lead->site_visit_date)
            <p class="text-sm text-gray-700 mb-2">🏠 Site Visit: {{ $lead->site_visit_date->format('d M Y') }}</p>
            @endif
            @if($lead->notes)
            <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3 mt-2">{{ $lead->notes }}</p>
            @endif
        </div>
        @endif
    </div>

    {{-- Quotations --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Quotations ({{ $lead->quotations->count() }})</h3>
                <a href="{{ route('quotations.create', ['lead_id'=>$lead->id]) }}"
                   class="text-xs text-yellow-600 hover:underline font-medium">+ New Quote</a>
            </div>
            @forelse($lead->quotations as $q)
            @php $qc = \App\Models\Quotation::STATUS_COLORS[$q->status] ?? 'gray'; @endphp
            <div class="border border-gray-100 rounded-xl p-4 mb-3 hover:border-yellow-200 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $q->quote_number }}</p>
                        <p class="text-sm text-gray-500">{{ $q->product?->variant }} · Rev {{ $q->version }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">₹{{ number_format($q->total, 0) }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $qc }}-100 text-{{ $qc }}-700">
                            {{ \App\Models\Quotation::STATUS_LABELS[$q->status] ?? $q->status }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <p class="text-xs text-gray-400">Created {{ $q->created_at->diffForHumans() }}</p>
                    <a href="{{ route('quotations.show', $q) }}" class="text-xs text-yellow-600 hover:underline font-medium">View →</a>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <i data-lucide="file-text" class="w-10 h-10 mx-auto opacity-30 mb-2"></i>
                <p class="text-sm">No quotations yet.</p>
                <a href="{{ route('quotations.create', ['lead_id'=>$lead->id]) }}" class="text-yellow-600 text-sm mt-1 inline-block hover:underline">Create first quotation →</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
