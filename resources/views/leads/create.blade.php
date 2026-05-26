@extends('layouts.app')
@section('title','New Lead')
@section('page-title','New Lead')
@section('breadcrumb')
<a href="{{ route('leads.index') }}" class="hover:underline">Leads</a> <span class="mx-1">›</span> <span class="text-gray-700">New Lead</span>
@endsection

@section('content')
<div class="max-w-3xl" x-data="leadForm()">
<form method="POST" action="{{ route('leads.store') }}" @submit="submitting=true">
@csrf

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
    @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
</div>
@endif

{{-- Customer Details --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
        <i data-lucide="user" class="w-4 h-4 text-yellow-500"></i> Customer Details
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Customer full name">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
            <input type="text" name="phone" value="{{ old('phone') }}" required
                   class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="+91 98765 43210">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Property Type <span class="text-red-500">*</span></label>
            <select name="property_type" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                @foreach(['villa'=>'Villa','duplex'=>'Duplex','penthouse'=>'Penthouse','apartment'=>'Apartment','bungalow'=>'Bungalow','other'=>'Other'] as $v=>$l)
                <option value="{{ $v }}" {{ old('property_type')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Number of Floors <span class="text-red-500">*</span></label>
            <input type="number" name="num_floors" value="{{ old('num_floors',3) }}" min="1" max="20" required
                   class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Budget Range</label>
            <select name="budget_range" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">Select range…</option>
                @foreach(['Under ₹5L'=>'Under ₹5L','₹5L – ₹10L'=>'₹5L – ₹10L','₹10L – ₹15L'=>'₹10L – ₹15L','₹15L – ₹20L'=>'₹15L – ₹20L','Above ₹20L'=>'Above ₹20L'] as $v=>$l)
                <option value="{{ $v }}" {{ old('budget_range')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <input type="text" name="address" value="{{ old('address') }}"
                   class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Property address">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
            <select name="state_id" x-model="stateId" @change="loadDistricts()" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">Select State…</option>
                @foreach($states as $s)
                <option value="{{ $s->id }}" {{ old('state_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">City/District</label>
            <select name="city_id" :disabled="!stateId" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:bg-gray-50">
                <option value="">Select…</option>
                <template x-for="c in cities" :key="c.id">
                    <option :value="c.id" x-text="c.name"></option>
                </template>
            </select>
        </div>
    </div>
</div>

{{-- Lead Source --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
        <i data-lucide="target" class="w-4 h-4 text-yellow-500"></i> Lead Source
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Source <span class="text-red-500">*</span></label>
            <select name="source" x-model="source" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                @foreach(['direct'=>'Direct','franchise'=>'Franchise Partner','referral'=>'Referral','digital'=>'Digital / Online','walk_in'=>'Walk-in','other'=>'Other'] as $v=>$l)
                <option value="{{ $v }}" {{ old('source')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div x-show="source==='franchise'" x-cloak>
            <label class="block text-sm font-medium text-gray-700 mb-1">Franchise Partner</label>
            <select name="franchise_id" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">Select franchise…</option>
                @foreach($franchises as $f)
                <option value="{{ $f->id }}" {{ old('franchise_id')==$f->id?'selected':'' }}>{{ $f->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Any additional context…">{{ old('notes') }}</textarea>
        </div>
    </div>
</div>

<div class="flex items-center gap-3">
    <button type="submit" :disabled="submitting" class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 disabled:opacity-60 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition shadow-sm">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        <span x-text="submitting?'Creating…':'Create Lead'"></span>
    </button>
    <a href="{{ route('leads.index') }}" class="px-6 py-2.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Cancel</a>
</div>

</form>
</div>
@endsection

@push('scripts')
<script>
function leadForm() {
    return {
        source: '{{ old('source','direct') }}',
        stateId: '{{ old('state_id','') }}',
        cities: [],
        submitting: false,
        async loadDistricts() {
            this.cities = [];
            if (!this.stateId) return;
            const res = await fetch(`/admin/users/cities?district_id=0&state_id=${this.stateId}`);
            // load cities directly
            const r2 = await fetch(`/admin/users/districts?state_id=${this.stateId}`);
            const districts = await r2.json();
            // flatten: show cities from all districts
            let allCities = [];
            for (const d of districts) {
                const cr = await fetch(`/admin/users/cities?district_id=${d.id}`);
                const cs = await cr.json();
                allCities = allCities.concat(cs.map(c=>({...c, name:`${d.name} › ${c.name}`})));
            }
            this.cities = allCities;
        }
    }
}
lucide.createIcons();
</script>
@endpush
