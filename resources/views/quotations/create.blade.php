@extends('layouts.app')
@section('title','New Quotation')
@section('page-title','New Quotation')
@section('breadcrumb')
<a href="{{ route('quotations.index') }}" class="hover:underline">Quotations</a> <span class="mx-1">›</span> New
@endsection

@section('content')
<div class="max-w-4xl" x-data="quotationForm()">
<form method="POST" action="{{ route('quotations.store') }}" @submit="submitting=true">
@csrf

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
    @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
</div>
@endif

{{-- Lead Selection --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i data-lucide="users" class="w-4 h-4 text-yellow-500"></i> Select Lead
    </h3>
    <select name="lead_id" required x-model="selectedLeadId" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <option value="">— Choose a lead —</option>
        @foreach($leads as $l)
        <option value="{{ $l->id }}" {{ (old('lead_id', $selectedLead?->id)==$l->id)?'selected':'' }}>
            {{ $l->customer?->name }} · {{ $l->customer?->phone }} · {{ $l->stage_label }}
        </option>
        @endforeach
    </select>
</div>

{{-- Product Selection --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i data-lucide="package" class="w-4 h-4 text-yellow-500"></i> Select Product
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($products as $product)
        @php $fc = ['orbit'=>'blue','apex'=>'purple','nova'=>'yellow'][$product->family] ?? 'gray'; @endphp
        <label class="cursor-pointer">
            <input type="radio" name="product_id" value="{{ $product->id }}"
                   x-model="selectedProductId"
                   @change="loadAddons({{ $product->id }}, {{ $product->base_price }})"
                   class="sr-only" {{ old('product_id')==$product->id?'checked':'' }}>
            <div :class="selectedProductId=={{ $product->id }} ? 'border-yellow-400 bg-yellow-50 shadow-md' : 'border-gray-200 hover:border-yellow-200'"
                 class="border-2 rounded-xl p-4 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-{{ $fc }}-600">{{ $product->family_label }}</span>
                    <span class="text-xs text-gray-400">{{ $product->capacity_persons }}P</span>
                </div>
                <p class="font-semibold text-gray-900">{{ $product->variant }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $product->description }}</p>
                <p class="text-lg font-bold text-gray-900 mt-3">₹{{ number_format($product->base_price, 0) }}</p>
                <p class="text-xs text-gray-400">{{ $product->door_type }}</p>
            </div>
        </label>
        @endforeach
    </div>
</div>

{{-- Line Items --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
            <i data-lucide="list" class="w-4 h-4 text-yellow-500"></i> Line Items
        </h3>
        <button type="button" @click="addItem()" class="text-sm text-yellow-600 hover:text-yellow-700 font-medium">+ Add Item</button>
    </div>

    <div class="space-y-3">
        <template x-for="(item, idx) in items" :key="idx">
            <div class="grid grid-cols-12 gap-2 items-start">
                <div class="col-span-5">
                    <input type="text" :name="`items[${idx}][description]`" x-model="item.description"
                           placeholder="Description" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <input type="hidden" :name="`items[${idx}][category]`" x-model="item.category">
                </div>
                <div class="col-span-2">
                    <input type="number" :name="`items[${idx}][quantity]`" x-model.number="item.quantity"
                           min="1" placeholder="Qty" required
                           @change="calcItem(idx)"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div class="col-span-3">
                    <input type="number" :name="`items[${idx}][unit_price]`" x-model.number="item.unit_price"
                           min="0" step="100" placeholder="Unit Price (₹)" required
                           @change="calcItem(idx)"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div class="col-span-1 text-right pt-2">
                    <p class="text-sm font-semibold text-gray-700" x-text="'₹'+formatNum(item.amount)"></p>
                </div>
                <div class="col-span-1 text-center">
                    <button type="button" @click="removeItem(idx)" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Totals --}}
    <div class="border-t border-gray-100 mt-6 pt-4">
        <div class="flex justify-end">
            <div class="w-72 space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span x-text="'₹'+formatNum(subtotal)"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>GST (18%)</span>
                    <span x-text="'₹'+formatNum(gstAmount)"></span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 border-t border-gray-200 pt-2">
                    <span>Total</span>
                    <span x-text="'₹'+formatNum(total)"></span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Options --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i data-lucide="settings" class="w-4 h-4 text-yellow-500"></i> Quote Options
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Valid For (days) <span class="text-red-500">*</span></label>
            <select name="valid_days" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="30" selected>30 days</option>
                <option value="45">45 days</option>
                <option value="60">60 days</option>
                <option value="90">90 days</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes for Customer</label>
            <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Any special remarks…">{{ old('notes') }}</textarea>
        </div>
    </div>
</div>

<div class="flex items-center gap-3">
    <button type="submit" :disabled="submitting||!selectedProductId||items.length===0"
            class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 disabled:opacity-50 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition shadow-sm">
        <i data-lucide="file-text" class="w-4 h-4"></i>
        <span x-text="submitting?'Creating…':'Create Quotation'"></span>
    </button>
    <a href="{{ route('quotations.index') }}" class="px-6 py-2.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Cancel</a>
</div>

</form>
</div>
@endsection

@push('scripts')
<script>
function quotationForm() {
    return {
        selectedLeadId: '{{ old('lead_id', $selectedLead?->id ?? '') }}',
        selectedProductId: null,
        items: [],
        submitting: false,
        get subtotal() { return this.items.reduce((s,i)=>s+i.amount,0); },
        get gstAmount() { return Math.round(this.subtotal*18/100); },
        get total() { return this.subtotal + this.gstAmount; },
        formatNum(n) { return Number(n||0).toLocaleString('en-IN'); },
        addItem(desc='', cat='general', qty=1, price=0) {
            this.items.push({description:desc, category:cat, quantity:qty, unit_price:price, amount:qty*price});
            this.$nextTick(()=>lucide.createIcons());
        },
        removeItem(idx) { this.items.splice(idx,1); },
        calcItem(idx) {
            const i = this.items[idx];
            i.amount = (i.quantity||0) * (i.unit_price||0);
        },
        async loadAddons(productId, basePrice) {
            this.items = [];
            this.addItem('Base Unit — ' + document.querySelector(`input[value="${productId}"]`)?.closest('label')?.querySelector('p.font-semibold')?.textContent || 'Elevator', 'base', 1, basePrice);
            this.addItem('Standard Installation Charges', 'installation', 1, 55000);
            lucide.createIcons();
        }
    }
}
lucide.createIcons();
</script>
@endpush
