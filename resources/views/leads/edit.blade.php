@extends('layouts.app')
@section('title','Update Lead')
@section('page-title','Update Lead')
@section('breadcrumb')
<a href="{{ route('leads.index') }}" class="hover:underline">Leads</a> <span class="mx-1">›</span>
<a href="{{ route('leads.show', $lead) }}" class="hover:underline">{{ $lead->customer?->name }}</a> <span class="mx-1">›</span>
<span class="text-gray-700">Edit</span>
@endsection

@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('leads.update', $lead) }}">
@csrf @method('PUT')

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
    <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
        <i data-lucide="refresh-cw" class="w-4 h-4 text-yellow-500"></i> Update Lead Status
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stage <span class="text-red-500">*</span></label>
            <select name="stage" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                @foreach(App\Models\Lead::STAGES as $key=>$s)
                <option value="{{ $key }}" {{ old('stage',$lead->stage)===$key?'selected':'' }}>{{ $s['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date & Time</label>
            <input type="datetime-local" name="follow_up_at"
                   value="{{ old('follow_up_at', $lead->follow_up_at?->format('Y-m-d\TH:i')) }}"
                   class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Site Visit Date</label>
            <input type="date" name="site_visit_date"
                   value="{{ old('site_visit_date', $lead->site_visit_date?->format('Y-m-d')) }}"
                   class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lost Reason</label>
            <input type="text" name="lost_reason" value="{{ old('lost_reason', $lead->lost_reason) }}"
                   class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                   placeholder="If lost, reason…">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">{{ old('notes', $lead->notes) }}</textarea>
        </div>
    </div>
</div>

<div class="flex items-center gap-3">
    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">Save Changes</button>
    <a href="{{ route('leads.show', $lead) }}" class="px-6 py-2.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">Cancel</a>
</div>
</form>
</div>
@endsection
@push('scripts')<script>lucide.createIcons();</script>@endpush
