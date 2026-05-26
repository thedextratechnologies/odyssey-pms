@extends('layouts.app')
@section('title', $quotation->quote_number)
@section('page-title', $quotation->quote_number)
@section('breadcrumb')
<a href="{{ route('quotations.index') }}" class="hover:underline">Quotations</a> <span class="mx-1">›</span>
<span class="text-gray-700 font-medium">{{ $quotation->quote_number }}</span>
@endsection

@section('content')
@php
$c = App\Models\Quotation::STATUS_COLORS[$quotation->status] ?? 'gray';
$user = auth()->user();
@endphp

<div class="max-w-4xl">

{{-- Header --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-xl font-bold text-gray-900">{{ $quotation->quote_number }}</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-{{ $c }}-100 text-{{ $c }}-700">
                    {{ $quotation->status_label }}
                </span>
            </div>
            <p class="text-gray-600">{{ $quotation->customer?->name }} · {{ $quotation->customer?->phone }}</p>
            <p class="text-sm text-gray-400 mt-1">Created by {{ $quotation->createdBy?->name }} · {{ $quotation->created_at->format('d M Y') }} · Valid until {{ $quotation->valid_until?->format('d M Y') }}</p>
        </div>
        <div class="text-right">
            <p class="text-3xl font-bold text-gray-900">₹{{ number_format($quotation->total, 0) }}</p>
            <p class="text-sm text-gray-400">incl. 18% GST</p>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-gray-100">
        @if($quotation->status === 'draft' || $quotation->status === 'revision_requested')
        <form method="POST" action="{{ route('quotations.submit', $quotation) }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i data-lucide="send" class="w-4 h-4"></i> Submit for Approval
            </button>
        </form>
        @endif

        @if($canApprove)
        <button onclick="document.getElementById('approveModal').classList.remove('hidden')"
                class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i data-lucide="check-circle" class="w-4 h-4"></i> Approve
        </button>
        <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i data-lucide="x-circle" class="w-4 h-4"></i> Reject
        </button>
        <button onclick="document.getElementById('revisionModal').classList.remove('hidden')"
                class="flex items-center gap-2 border border-orange-300 text-orange-600 hover:bg-orange-50 text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i data-lucide="edit" class="w-4 h-4"></i> Request Revision
        </button>
        @endif

        @if($quotation->status === 'approved')
        <form method="POST" action="{{ route('quotations.won', $quotation) }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                🏆 Mark as Won
            </button>
        </form>
        <button onclick="document.getElementById('lostModal').classList.remove('hidden')"
                class="flex items-center gap-2 border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium px-4 py-2 rounded-lg transition">
            Mark as Lost
        </button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Product --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Product</h3>
            @php $fc = ['orbit'=>'blue','apex'=>'purple','nova'=>'yellow'][$quotation->product?->family] ?? 'gray'; @endphp
            <div class="flex items-center gap-4 p-4 bg-{{ $fc }}-50 rounded-xl border border-{{ $fc }}-100">
                <div class="w-12 h-12 rounded-xl bg-{{ $fc }}-100 text-{{ $fc }}-700 flex items-center justify-center font-bold text-lg">
                    {{ strtoupper(substr($quotation->product?->family ?? 'O', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $quotation->product?->variant }}</p>
                    <p class="text-sm text-gray-600">{{ $quotation->product?->description }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $quotation->product?->capacity_persons }} passengers · {{ $quotation->product?->door_type }}</p>
                </div>
            </div>
        </div>

        {{-- Line Items --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Line Items</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase">Description</th>
                        <th class="text-center pb-3 text-xs font-semibold text-gray-400 uppercase">Qty</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase">Unit Price</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($quotation->items as $item)
                    <tr>
                        <td class="py-3 text-gray-800">{{ $item->description }}</td>
                        <td class="py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="py-3 text-right text-gray-600">₹{{ number_format($item->unit_price, 0) }}</td>
                        <td class="py-3 text-right font-medium text-gray-900">₹{{ number_format($item->amount, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-100">
                        <td colspan="3" class="py-2 text-right text-sm text-gray-500">Subtotal</td>
                        <td class="py-2 text-right text-sm font-medium">₹{{ number_format($quotation->subtotal, 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="py-2 text-right text-sm text-gray-500">GST ({{ $quotation->gst_rate }}%)</td>
                        <td class="py-2 text-right text-sm font-medium">₹{{ number_format($quotation->gst_amount, 0) }}</td>
                    </tr>
                    <tr class="border-t-2 border-gray-200">
                        <td colspan="3" class="py-3 text-right font-bold text-gray-900">Total</td>
                        <td class="py-3 text-right text-xl font-bold text-gray-900">₹{{ number_format($quotation->total, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        {{-- Approval History --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Approval History</h3>
            @forelse($quotation->approvals as $approval)
            @php $ac = ['approved'=>'green','rejected'=>'red','revision_requested'=>'orange','pending'=>'yellow'][$approval->status] ?? 'gray'; @endphp
            <div class="flex items-start gap-3 py-3 border-b border-gray-50 last:border-0">
                <div class="w-2 h-2 rounded-full bg-{{ $ac }}-400 mt-1.5 shrink-0"></div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ ucfirst(str_replace('_',' ',$approval->status)) }}</p>
                    <p class="text-xs text-gray-500">{{ $approval->role_level }} level</p>
                    @if($approval->approver) <p class="text-xs text-gray-400">by {{ $approval->approver->name }}</p> @endif
                    @if($approval->comment) <p class="text-xs text-gray-600 mt-1 bg-gray-50 rounded p-2">{{ $approval->comment }}</p> @endif
                    @if($approval->actioned_at) <p class="text-xs text-gray-400 mt-1">{{ $approval->actioned_at->diffForHumans() }}</p> @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">No approval actions yet.</p>
            @endforelse
        </div>

        @if($quotation->notes)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Notes</h3>
            <p class="text-sm text-gray-600">{{ $quotation->notes }}</p>
        </div>
        @endif
    </div>
</div>

</div>

{{-- Modals --}}
@if($canApprove)
<div id="approveModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Approve Quotation</h3>
        <form method="POST" action="{{ route('quotations.approve', $quotation) }}">
            @csrf
            <textarea name="comment" rows="3" placeholder="Optional comment…" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 mb-4"></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-lg text-sm">Confirm Approval</button>
                <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Quotation</h3>
        <form method="POST" action="{{ route('quotations.reject', $quotation) }}">
            @csrf
            <textarea name="rejection_reason" rows="3" required placeholder="Reason for rejection (required)…" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 mb-4"></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 rounded-lg text-sm">Confirm Rejection</button>
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="revisionModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Request Revision</h3>
        <form method="POST" action="{{ route('quotations.revision', $quotation) }}">
            @csrf
            <textarea name="comment" rows="3" required placeholder="What needs to be revised?" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 mb-4"></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-lg text-sm">Send Back</button>
                <button type="button" onclick="document.getElementById('revisionModal').classList.add('hidden')" class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endif

<div id="lostModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Mark as Lost</h3>
        <form method="POST" action="{{ route('quotations.lost', $quotation) }}">
            @csrf
            <input type="text" name="lost_reason" required placeholder="Reason (competitor, budget, etc)…" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 mb-4">
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 rounded-lg text-sm">Mark Lost</button>
                <button type="button" onclick="document.getElementById('lostModal').classList.add('hidden')" class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
