@extends('layouts.app')
@section('title', 'Edit ' . $user->name)
@section('page-title', 'Edit User')
@section('breadcrumb')
    <span>Admin</span> <span class="mx-1">›</span>
    <a href="{{ route('admin.users.index') }}" class="hover:underline">Users</a> <span class="mx-1">›</span>
    <a href="{{ route('admin.users.show', $user) }}" class="hover:underline">{{ $user->name }}</a> <span class="mx-1">›</span>
    <span class="text-gray-700 font-medium">Edit</span>
@endsection

@section('content')
<div class="max-w-3xl"
     x-data="editUserForm()"
     x-init="init()">

    <form method="POST" action="{{ route('admin.users.update', $user) }}" @submit="submitting = true">
        @csrf @method('PUT')

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="font-medium text-red-800 text-sm mb-2">Please fix the following errors:</p>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<li>• {{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-yellow-500"></i> Personal Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}"
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Joining</label>
                    <input type="date" name="date_of_joining" value="{{ old('date_of_joining', $user->date_of_joining?->format('Y-m-d')) }}"
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        @foreach(['active','inactive','suspended'] as $s)
                        <option value="{{ $s }}" {{ old('status', $user->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <i data-lucide="shield" class="w-4 h-4 text-yellow-500"></i> Role & Territory
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role_id" required class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                    <select name="manager_id" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">None</option>
                        @foreach($managers as $m)
                        <option value="{{ $m->id }}" {{ old('manager_id', $user->manager_id) == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->role?->display_name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <select name="state_id" x-model="stateId" @change="loadDistricts()"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select State…</option>
                        @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ old('state_id', $user->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                    <select name="district_id" x-model="districtId" @change="loadCities()"
                            :disabled="!stateId"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:bg-gray-50">
                        <option value="">Select District…</option>
                        <template x-for="d in districts" :key="d.id">
                            <option :value="d.id" :selected="d.id == {{ $user->district_id ?? 0 }}" x-text="d.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <select name="city_id" :disabled="!districtId"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:bg-gray-50">
                        <option value="">Select City…</option>
                        <template x-for="c in cities" :key="c.id">
                            <option :value="c.id" :selected="c.id == {{ $user->city_id ?? 0 }}" x-text="c.name"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" :disabled="submitting"
                    class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 disabled:opacity-60 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition shadow-sm">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span x-text="submitting ? 'Saving…' : 'Save Changes'"></span>
            </button>
            <a href="{{ route('admin.users.show', $user) }}"
               class="px-6 py-2.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function editUserForm() {
    return {
        stateId: '{{ old('state_id', $user->state_id ?? '') }}',
        districtId: '{{ old('district_id', $user->district_id ?? '') }}',
        districts: @json($districts),
        cities: @json($cities),
        submitting: false,
        init() {
            if (this.stateId && this.districts.length === 0) this.loadDistricts();
            if (this.districtId && this.cities.length === 0) this.loadCities();
        },
        async loadDistricts() {
            this.districts = []; this.cities = []; this.districtId = '';
            if (!this.stateId) return;
            const res = await fetch(`/admin/users/districts?state_id=${this.stateId}`);
            this.districts = await res.json();
        },
        async loadCities() {
            this.cities = [];
            if (!this.districtId) return;
            const res = await fetch(`/admin/users/cities?district_id=${this.districtId}`);
            this.cities = await res.json();
        }
    }
}
lucide.createIcons();
</script>
@endpush
