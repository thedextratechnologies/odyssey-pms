@extends('layouts.app')
@section('title', $franchise->company_name)
@section('page-title', $franchise->company_name)
@section('breadcrumb')
<a href="{{ route('franchises.index') }}" class="hover:underline">Franchises</a> <span class="mx-1">›</span> {{ $franchise->company_name }}
@endsection
@section('content')
<div class="max-w-3xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6 flex justify-between items-start">
    <div>
        <h2 class="text-xl font-bold text-gray-900">{{ $franchise->company_name }}</h2>
        <p class="text-gray-500">{{ $franchise->owner_name }} · {{ $franchise->phone }}</p>
        <span class="inline-flex mt-2 px-2.5 py-1 rounded-full text-xs font-semibold {{ $franchise->status==='active'?'bg-green-100 text-green-700':'bg-gray-100 text-gray-500' }}">{{ ucfirst($franchise->status) }}</span>
    </div>
    <a href="{{ route('franchises.edit', $franchise) }}" class="flex items-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm px-4 py-2 rounded-lg">
        <i data-lucide="pencil" class="w-4 h-4"></i> Edit
    </a>
</div>
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-blue-600">{{ $totalLeads }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Leads</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $wonLeads }}</p>
        <p class="text-sm text-gray-500 mt-1">Won Leads</p>
    </div>
</div>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Details</h3>
    <dl class="space-y-3">
        @foreach([['Email',$franchise->email??'—'],['State',$franchise->state?->name??'—'],['District',$franchise->district?->name??'—'],['Managed By',$franchise->manager?->name??'—'],['Agreement',$franchise->agreement_date?->format('d M Y')??'—'],['Notes',$franchise->notes??'—']] as [$l,$v])
        <div class="flex justify-between"><dt class="text-sm text-gray-500">{{ $l }}</dt><dd class="text-sm font-medium text-gray-800">{{ $v }}</dd></div>
        @endforeach
    </dl>
</div>
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
