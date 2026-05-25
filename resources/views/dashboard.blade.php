@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

{{-- Welcome banner --}}
<div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background: radial-gradient(circle at 80% 50%, #B8960C 0%, transparent 60%);"></div>
    <div class="relative">
        <p class="text-yellow-400 text-sm font-medium mb-1">{{ now()->format('l, d F Y') }}</p>
        <h2 class="text-2xl font-bold">Welcome back, {{ $user->name }} 👋</h2>
        <p class="text-gray-400 text-sm mt-1">
            {{ $user->role?->display_name }} ·
            {{ $user->getTerritoryLabel() }}
        </p>
    </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $kpis = [
            ['label' => 'Active Leads', 'value' => '—', 'icon' => 'users', 'color' => 'blue', 'change' => null],
            ['label' => 'Open Quotes', 'value' => '—', 'icon' => 'file-text', 'color' => 'yellow', 'change' => null],
            ['label' => 'Pending Approvals', 'value' => '—', 'icon' => 'clock', 'color' => 'orange', 'change' => null],
            ['label' => 'Won This Month', 'value' => '—', 'icon' => 'trophy', 'color' => 'green', 'change' => null],
        ];
        $colorMap = [
            'blue'   => 'bg-blue-50 text-blue-600',
            'yellow' => 'bg-yellow-50 text-yellow-600',
            'orange' => 'bg-orange-50 text-orange-600',
            'green'  => 'bg-green-50 text-green-600',
        ];
    @endphp

    @foreach($kpis as $kpi)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl {{ $colorMap[$kpi['color']] }} flex items-center justify-center">
                <i data-lucide="{{ $kpi['icon'] }}" class="w-5 h-5"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $kpi['value'] }}</p>
        <p class="text-sm text-gray-500 mt-0.5">{{ $kpi['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Two column layout --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent Activity placeholder --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i data-lucide="activity" class="w-4 h-4 text-yellow-500"></i>
            Recent Activity
        </h3>
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="bar-chart-2" class="w-12 h-12 mx-auto opacity-30 mb-3"></i>
            <p class="font-medium">Activity feed loads here</p>
            <p class="text-sm mt-1">Your leads, quotes, and approvals will appear here.</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i data-lucide="zap" class="w-4 h-4 text-yellow-500"></i>
                Quick Actions
            </h3>
            <div class="space-y-2">
                <a href="{{ route('leads.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition group">
                    <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">New Lead</p>
                        <p class="text-xs text-gray-500">Add a new customer lead</p>
                    </div>
                </a>

                <a href="{{ route('quotations.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition group">
                    <div class="w-9 h-9 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center group-hover:bg-yellow-100 transition">
                        <i data-lucide="file-plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">New Quotation</p>
                        <p class="text-xs text-gray-500">Create a product quotation</p>
                    </div>
                </a>

                @if($user->canApproveQuotations())
                <a href="{{ route('approvals.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition group">
                    <div class="w-9 h-9 bg-green-50 text-green-600 rounded-lg flex items-center justify-center group-hover:bg-green-100 transition">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Review Approvals</p>
                        <p class="text-xs text-gray-500">Quotes awaiting your action</p>
                    </div>
                </a>
                @endif
            </div>
        </div>

        {{-- Products Reference --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-yellow-500"></i>
                Product Lines
            </h3>
            <div class="space-y-3">
                @foreach([
                    ['Orbit', 'Compact 2-passenger circular lift', 'Micro / Std / Max', 'blue'],
                    ['Apex', '6-passenger premium circular lift', 'Premium', 'purple'],
                    ['Nova', 'Flagship 360° panoramic lift', 'Std / Max', 'yellow'],
                ] as [$name, $desc, $variants, $color])
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-{{ $color }}-100 text-{{ $color }}-700 flex items-center justify-center text-xs font-bold shrink-0">
                        {{ $name[0] }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $name }}</p>
                        <p class="text-xs text-gray-500">{{ $desc }}</p>
                        <p class="text-xs text-gray-400">{{ $variants }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
