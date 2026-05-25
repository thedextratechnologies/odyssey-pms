@extends('layouts.app')

@section('title', $module)
@section('page-title', $module)

@section('content')
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="w-20 h-20 bg-yellow-50 rounded-2xl flex items-center justify-center mb-6">
        <i data-lucide="construction" class="w-10 h-10 text-yellow-500"></i>
    </div>
    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $module }}</h2>
    <p class="text-gray-500 max-w-sm mb-8">
        This module is being built as part of the next development sprint.
        The foundation (Auth + User Management) is now complete.
    </p>
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('dashboard') }}"
           class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium px-4 py-2 rounded-lg transition">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
