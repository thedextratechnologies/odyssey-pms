@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

{{-- Welcome banner --}}
<div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 50%,#B8960C 0%,transparent 60%);"></div>
    <div class="relative">
        <p class="text-yellow-400 text-sm font-medium mb-1">{{ now()->format('l, d F Y') }}</p>
        <h2 class="text-2xl font-bold">Welcome back, {{ $user->name }} 👋</h2>
        <p class="text-gray-400 text-sm mt-1">{{ $user->role?->display_name }} · {{ $user->getTerritoryLabel() }}</p>
    </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @php
    $kpis = [
        ['Active Leads','active_leads','users','blue'],
        ['Open Quotes','open_quotes','file-text','yellow'],
        ['Pending Approvals','pending_approvals','clock','orange'],
        ['Won This Month','won_this_month','trophy','green'],
        ['Pipeline Value','pipeline_value','trending-up','purple'],
        ['Won Value (MTD)','won_value','indian-rupee','green'],
    ];
    $colorMap=['blue'=>'bg-blue-50 text-blue-600','yellow'=>'bg-yellow-50 text-yellow-600','orange'=>'bg-orange-50 text-orange-600','green'=>'bg-green-50 text-green-600','purple'=>'bg-purple-50 text-purple-600'];
    @endphp
    @foreach($kpis as [$label,$key,$icon,$color])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $colorMap[$color] }} flex items-center justify-center">
                <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
            </div>
            @if($key==='pending_approvals' && $stats[$key]>0)
            <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats[$key] }}</span>
            @endif
        </div>
        <p class="text-2xl font-bold text-gray-900">
            @if(in_array($key,['pipeline_value','won_value']))₹{{ number_format($stats[$key]/100000,1) }}L
            @else{{ $stats[$key] }}
            @endif
        </p>
        <p class="text-sm text-gray-500 mt-0.5">{{ $label }}</p>
    </div>
    @endforeach
</div>

@if($overdueFollowups > 0)
<div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-6 flex items-center gap-3">
    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 shrink-0"></i>
    <p class="text-sm text-red-700 font-medium">You have <strong>{{ $overdueFollowups }}</strong> overdue follow-ups.
    <a href="{{ route('leads.index') }}" class="underline">View leads →</a></p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Leads --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-yellow-500"></i> Recent Leads
            </h3>
            <a href="{{ route('leads.index') }}" class="text-sm text-yellow-600 hover:underline font-medium">View all →</a>
        </div>
        @forelse($recentLeads as $lead)
        @php $c=['new'=>'gray','contacted'=>'blue','site_visit_scheduled'=>'purple','quotation_sent'=>'yellow','negotiation'=>'orange','won'=>'green','lost'=>'red','on_hold'=>'gray'][$lead->stage]??'gray'; @endphp
        <a href="{{ route('leads.show', $lead) }}" class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg transition">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center text-xs font-bold shrink-0">
                    {{ strtoupper(substr($lead->customer?->name??'X',0,1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $lead->customer?->name }}</p>
                    <p class="text-xs text-gray-500">{{ $lead->customer?->phone }}</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700">{{ $lead->stage_label }}</span>
        </a>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i data-lucide="users" class="w-8 h-8 mx-auto opacity-30 mb-2"></i>
            <p class="text-sm">No leads yet. <a href="{{ route('leads.create') }}" class="text-yellow-600 hover:underline">Create one →</a></p>
        </div>
        @endforelse
    </div>

    {{-- Recent Quotes --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-yellow-500"></i> Recent Quotations
            </h3>
            <a href="{{ route('quotations.index') }}" class="text-sm text-yellow-600 hover:underline font-medium">View all →</a>
        </div>
        @forelse($recentQuotes as $q)
        @php $c=App\Models\Quotation::STATUS_COLORS[$q->status]??'gray'; @endphp
        <a href="{{ route('quotations.show', $q) }}" class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg transition">
            <div>
                <p class="text-sm font-semibold text-gray-800 font-mono">{{ $q->quote_number }}</p>
                <p class="text-xs text-gray-500">{{ $q->customer?->name }} · {{ $q->product?->variant }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-gray-900">₹{{ number_format($q->total,0) }}</p>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700">{{ $q->status_label }}</span>
            </div>
        </a>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i data-lucide="file-text" class="w-8 h-8 mx-auto opacity-30 mb-2"></i>
            <p class="text-sm">No quotations yet. <a href="{{ route('quotations.create') }}" class="text-yellow-600 hover:underline">Create one →</a></p>
        </div>
        @endforelse
    </div>
</div>

@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
