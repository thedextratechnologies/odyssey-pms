@extends('layouts.app')
@section('title','Products & Pricing')
@section('page-title','Products & Pricing')

@section('content')
<p class="text-sm text-gray-500 mb-6">Manage product variants, base prices, and add-on options.</p>

<div class="space-y-6">
    @foreach($products->groupBy('family') as $family => $variants)
    @php $fc = ['orbit'=>'blue','apex'=>'purple','nova'=>'yellow'][$family] ?? 'gray'; @endphp
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-{{ $fc }}-50 border-b border-{{ $fc }}-100 px-5 py-3">
            <h3 class="font-bold text-{{ $fc }}-800 uppercase tracking-wider text-sm">{{ ucfirst($family) }} Series</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Variant</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Capacity</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Base Price (₹)</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($variants as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-semibold text-gray-900">{{ $p->variant }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $p->description }}</td>
                    <td class="px-5 py-4 text-center text-gray-600">{{ $p->capacity_persons }} persons</td>
                    <td class="px-5 py-4 text-right font-bold text-gray-900">₹{{ number_format($p->base_price, 0) }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->is_active?'bg-green-100 text-green-700':'bg-gray-100 text-gray-500' }}">
                            {{ $p->is_active?'Active':'Inactive' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    {{-- Add-ons --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-5 py-3 flex items-center justify-between">
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Global Add-ons</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Category</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Price (₹)</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Unit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($addons as $addon)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $addon->name }}</td>
                    <td class="px-5 py-3 text-gray-600 capitalize">{{ str_replace('_',' ',$addon->category) }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-900">₹{{ number_format($addon->price, 0) }}</td>
                    <td class="px-5 py-3 text-gray-500 capitalize">{{ str_replace('_',' ',$addon->unit) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
