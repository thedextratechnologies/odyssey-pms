@extends('layouts.app')
@section('title','Pending Approvals')
@section('page-title','Pending Approvals')

@section('content')
<p class="text-sm text-gray-500 mb-6">Quotations awaiting your review and action.</p>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Quote #</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Product</th>
                <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Stage</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Submitted By</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="text-right px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($quotations as $q)
            @php $c = App\Models\Quotation::STATUS_COLORS[$q->status] ?? 'gray'; @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-4 font-mono font-semibold text-gray-900">{{ $q->quote_number }}</td>
                <td class="px-5 py-4">
                    <p class="font-medium text-gray-900">{{ $q->customer?->name }}</p>
                    <p class="text-xs text-gray-500">{{ $q->customer?->phone }}</p>
                </td>
                <td class="px-5 py-4 text-gray-600">{{ $q->product?->variant }}</td>
                <td class="px-5 py-4 text-right font-bold text-gray-900">₹{{ number_format($q->total, 0) }}</td>
                <td class="px-5 py-4">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700">
                        {{ $q->status_label }}
                    </span>
                </td>
                <td class="px-5 py-4 text-gray-600">{{ $q->createdBy?->name }}</td>
                <td class="px-5 py-4 text-gray-500">{{ $q->created_at->format('d M') }}</td>
                <td class="px-5 py-4 text-right">
                    <a href="{{ route('quotations.show', $q) }}" class="text-yellow-600 hover:underline text-sm font-semibold">Review →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">
                <i data-lucide="check-circle" class="w-10 h-10 mx-auto opacity-30 mb-2"></i>
                <p class="font-medium">All caught up!</p>
                <p class="text-sm">No quotations pending your approval.</p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
