@extends('layouts.app')
@section('title','Quotations')
@section('page-title','Quotations')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage all product quotations and approvals.</p>
    <a href="{{ route('quotations.create') }}" class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
        <i data-lucide="file-plus" class="w-4 h-4"></i> New Quotation
    </a>
</div>

<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Quote number or customer…"
               class="flex-1 min-w-48 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="">All Status</option>
            @foreach(App\Models\Quotation::STATUS_LABELS as $k=>$l)
            <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Filter</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('quotations.index') }}" class="text-sm text-gray-500 px-3 py-2 rounded-lg hover:bg-gray-100">Clear</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Quote #</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Product</th>
                <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Created By</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="text-right px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($quotations as $q)
            @php $c = App\Models\Quotation::STATUS_COLORS[$q->status] ?? 'gray'; @endphp
            <tr class="hover:bg-gray-50 transition group">
                <td class="px-5 py-4 font-mono text-sm font-semibold text-gray-900">{{ $q->quote_number }}</td>
                <td class="px-5 py-4">
                    <p class="font-medium text-gray-900">{{ $q->customer?->name }}</p>
                    <p class="text-xs text-gray-500">{{ $q->customer?->phone }}</p>
                </td>
                <td class="px-5 py-4 text-gray-600">{{ $q->product?->variant }}</td>
                <td class="px-5 py-4 text-right font-bold text-gray-900">₹{{ number_format($q->total, 0) }}</td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700">
                        {{ $q->status_label }}
                    </span>
                </td>
                <td class="px-5 py-4 text-gray-600 text-sm">{{ $q->createdBy?->name }}</td>
                <td class="px-5 py-4 text-gray-500 text-sm">{{ $q->created_at->format('d M Y') }}</td>
                <td class="px-5 py-4 text-right opacity-0 group-hover:opacity-100 transition">
                    <a href="{{ route('quotations.show', $q) }}" class="text-yellow-600 hover:underline text-sm font-medium">View →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">
                <i data-lucide="file-text" class="w-10 h-10 mx-auto opacity-30 mb-2"></i>
                <p>No quotations found.</p>
                <a href="{{ route('quotations.create') }}" class="text-yellow-600 text-sm mt-1 inline-block">Create first quotation →</a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    @if($quotations->hasPages())
    <div class="border-t border-gray-100 px-5 py-3 flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $quotations->firstItem() }}–{{ $quotations->lastItem() }} of {{ $quotations->total() }}</p>
        {{ $quotations->links() }}
    </div>
    @endif
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
