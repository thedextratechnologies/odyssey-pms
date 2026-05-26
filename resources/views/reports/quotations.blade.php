@extends('layouts.app')
@section('title','Quotation Report')
@section('page-title','Quotation Report')
@section('breadcrumb')
<a href="{{ route('reports.index') }}" class="hover:underline">Reports</a> <span class="mx-1">›</span> Quotations
@endsection

@section('content')
<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">All Status</option>
            @foreach(App\Models\Quotation::STATUS_LABELS as $k=>$l)
            <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <button type="submit" class="bg-gray-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Apply</button>
        <a href="{{ route('reports.quotations') }}" class="text-sm text-gray-500 px-3 py-2 rounded-lg hover:bg-gray-100">Clear</a>
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
        <p class="text-sm text-gray-600">{{ $quotations->total() }} quotations · Total value: ₹{{ number_format($quotations->sum('total'), 0) }}</p>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Quote #</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Product</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Created By</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($quotations as $q)
            @php $c = App\Models\Quotation::STATUS_COLORS[$q->status] ?? 'gray'; @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-mono font-semibold">
                    <a href="{{ route('quotations.show', $q) }}" class="text-yellow-600 hover:underline">{{ $q->quote_number }}</a>
                </td>
                <td class="px-5 py-3 text-gray-800">{{ $q->customer?->name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $q->product?->variant }}</td>
                <td class="px-5 py-3 text-right font-semibold">₹{{ number_format($q->total, 0) }}</td>
                <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700">{{ $q->status_label }}</span></td>
                <td class="px-5 py-3 text-gray-600">{{ $q->createdBy?->name }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $q->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No quotations found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($quotations->hasPages())
    <div class="border-t border-gray-100 px-5 py-3">{{ $quotations->links() }}</div>
    @endif
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
