@extends('layouts.app')
@section('title','Add Franchise')
@section('page-title','Add Franchise Partner')
@section('breadcrumb')
<a href="{{ route('franchises.index') }}" class="hover:underline">Franchises</a> <span class="mx-1">›</span> New
@endsection
@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('franchises.store') }}">
@csrf
@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
    @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
</div>
@endif
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-base font-semibold text-gray-900 mb-5">Franchise Details</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
        <input type="text" name="company_name" value="{{ old('company_name') }}" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Owner Name *</label>
        <input type="text" name="owner_name" value="{{ old('owner_name') }}" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
        <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
        <select name="state_id" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">Select State…</option>
            @foreach($states as $s)<option value="{{ $s->id }}" {{ old('state_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach
        </select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Managed By</label>
        <select name="managed_by" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">None</option>
            @foreach($managers as $m)<option value="{{ $m->id }}" {{ old('managed_by')==$m->id?'selected':'' }}>{{ $m->name }} ({{ $m->role?->display_name }})</option>@endforeach
        </select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Agreement Date</label>
        <input type="date" name="agreement_date" value="{{ old('agreement_date') }}" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="active">Active</option><option value="inactive">Inactive</option>
        </select></div>
        <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">{{ old('notes') }}</textarea></div>
    </div>
</div>
<div class="flex gap-3">
    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">Add Franchise</button>
    <a href="{{ route('franchises.index') }}" class="px-6 py-2.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">Cancel</a>
</div>
</form>
</div>
@endsection
