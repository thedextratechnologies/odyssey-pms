@extends('layouts.app')
@section('title','Reports')
@section('page-title','Reports & Analytics')

@section('content')

{{-- KPI Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @php
    $kpis = [
        ['Total Leads','active_leads','users','blue'],
        ['Won Leads','won_leads','trophy','green'],
        ['Total Quotes','total_quotes','file-text','yellow'],
        ['Approved Quotes','approved_quotes','check-circle','purple'],
        ['Pipeline Value','pipeline_value','trending-up','orange'],
        ['Won Value (MTD)','won_value','indian-rupee','green'],
    ];
    @endphp
    @foreach($kpis as [$label,$key,$icon,$color])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <div class="w-9 h-9 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center">
                <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">
            @if(in_array($key,['pipeline_value','won_value']))
                ₹{{ number_format($stats[$key]/100000, 1) }}L
            @else
                {{ $stats[$key] }}
            @endif
        </p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Lead Funnel --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
            <i data-lucide="filter" class="w-4 h-4 text-yellow-500"></i> Lead Pipeline Funnel
        </h3>
        @php $maxCount = max(array_column($funnelData,'count') ?: [1]); @endphp
        <div class="space-y-3">
            @foreach($funnelData as $key => $stage)
            @php
                $pct = $maxCount > 0 ? ($stage['count'] / $maxCount) * 100 : 0;
                $barColors = ['gray'=>'bg-gray-200','blue'=>'bg-blue-400','purple'=>'bg-purple-400','yellow'=>'bg-yellow-400','orange'=>'bg-orange-400','green'=>'bg-green-400','red'=>'bg-red-400'];
                $bc = $barColors[$stage['color']] ?? 'bg-gray-200';
            @endphp
            <div class="flex items-center gap-3">
                <div class="w-28 text-xs text-gray-500 text-right shrink-0">{{ $stage['label'] }}</div>
                <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                    <div class="{{ $bc }} h-6 rounded-full flex items-center px-2 transition-all duration-500"
                         style="width: {{ max($pct,2) }}%">
                        @if($stage['count'] > 0)
                        <span class="text-white text-xs font-semibold">{{ $stage['count'] }}</span>
                        @endif
                    </div>
                </div>
                <div class="w-8 text-xs text-gray-500 shrink-0">{{ $stage['count'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Product Mix --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
            <i data-lucide="pie-chart" class="w-4 h-4 text-yellow-500"></i> Product Mix
        </h3>
        @php $totalQ = $productMix->sum('count') ?: 1; @endphp
        <div class="space-y-4">
            @forelse($productMix as $pm)
            @php
                $pct2 = round(($pm->count / $totalQ) * 100);
                $fc2 = ['orbit'=>'blue','apex'=>'purple','nova'=>'yellow'][$pm->family] ?? 'gray';
            @endphp
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 capitalize">{{ $pm->family }}</span>
                    <span class="text-sm text-gray-500">{{ $pm->count }} quotes · ₹{{ number_format($pm->value/100000,1) }}L</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-{{ $fc2 }}-400 h-3 rounded-full" style="width:{{ $pct2 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-8">No quotation data yet.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Monthly Trend --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-yellow-500"></i> Monthly Quotations ({{ date('Y') }})
        </h3>
        <a href="{{ route('reports.quotations') }}" class="text-sm text-yellow-600 hover:underline font-medium">Full report →</a>
    </div>
    @php
    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $monthlyMap = $monthlyQuotes->keyBy('month');
    $maxVal = $monthlyQuotes->max('count') ?: 1;
    @endphp
    <div class="flex items-end gap-2 h-40">
        @for($m=1; $m<=12; $m++)
        @php $md = $monthlyMap[$m] ?? null; $h = $md ? max(($md->count/$maxVal)*100, 4) : 4; @endphp
        <div class="flex-1 flex flex-col items-center gap-1">
            <span class="text-xs text-gray-500">{{ $md?->count ?? 0 }}</span>
            <div class="w-full rounded-t-md {{ $m == date('n') ? 'bg-yellow-400' : 'bg-gray-200' }} transition-all hover:opacity-80"
                 style="height: {{ $h }}%"
                 title="{{ $months[$m-1] }}: {{ $md?->count ?? 0 }} quotes"></div>
            <span class="text-xs text-gray-400">{{ $months[$m-1] }}</span>
        </div>
        @endfor
    </div>
</div>

@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
