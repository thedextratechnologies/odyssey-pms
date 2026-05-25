@extends('layouts.app')

@section('title', 'Add User')
@section('page-title', 'Add New User')

@section('breadcrumb')
    <span>Administration</span> <span class="mx-1">›</span>
    <a href="{{ route('admin.users.index') }}" class="hover:underline">Users</a> <span class="mx-1">›</span>
    <span class="text-gray-700 font-medium">New User</span>
@endsection

@section('content')
<div class="max-w-3xl"
     x-data="createUserForm()"
     x-init="init()">

    <form method="POST" action="{{ route('admin.users.store') }}" @submit="submitting = true">
        @csrf

        {{-- Errors --}}
        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="font-medium text-red-800 text-sm mb-2">Please fix the following errors:</p>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)
                    <li>• {{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Personal Details --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-yellow-500"></i> Personal Details
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 @error('name') border-red-400 @enderror"
                           placeholder="e.g. Rajesh Kumar">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id') }}"
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                           placeholder="e.g. OE-TN-001">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 @error('email') border-red-400 @enderror"
                           placeholder="name@odysseyelevators.com">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                           placeholder="+91 98765 43210">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Joining</label>
                    <input type="date" name="date_of_joining" value="{{ old('date_of_joining', date('Y-m-d')) }}"
                           class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Role & Territory --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <i data-lucide="shield" class="w-4 h-4 text-yellow-500"></i> Role & Territory
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role_id" required x-model="selectedRole"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 @error('role_id') border-red-400 @enderror">
                        <option value="">Select a role…</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reporting Manager</label>
                    <select name="manager_id"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">None</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }} ({{ $manager->role?->display_name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- State --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <select name="state_id" x-model="stateId" @change="loadDistricts()"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select State…</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- District --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                    <select name="district_id" x-model="districtId" @change="loadCities()"
                            :disabled="!stateId || loadingDistricts"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:bg-gray-50 disabled:text-gray-400">
                        <option value="">
                            <span x-text="loadingDistricts ? 'Loading…' : 'Select District…'"></span>
                        </option>
                        <template x-for="d in districts" :key="d.id">
                            <option :value="d.id" x-text="d.name"></option>
                        </template>
                    </select>
                </div>

                {{-- City --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <select name="city_id"
                            :disabled="!districtId || loadingCities"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:bg-gray-50 disabled:text-gray-400">
                        <option value="">
                            <span x-text="loadingCities ? 'Loading…' : 'Select City…'"></span>
                        </option>
                        <template x-for="c in cities" :key="c.id">
                            <option :value="c.id" x-text="c.name"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        {{-- Info box --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm text-blue-800">
            <div class="flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 mt-0.5 shrink-0"></i>
                <div>
                    <p class="font-medium">About passwords</p>
                    <p class="text-blue-700 mt-1">A temporary password will be generated and emailed to the user. They will be required to change it on first login.</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit" :disabled="submitting"
                    class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 disabled:opacity-60 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition shadow-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span x-text="submitting ? 'Creating…' : 'Create User'"></span>
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="px-6 py-2.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function createUserForm() {
    return {
        selectedRole: '{{ old('role_id', '') }}',
        stateId: '{{ old('state_id', '') }}',
        districtId: '{{ old('district_id', '') }}',
        districts: [],
        cities: [],
        loadingDistricts: false,
        loadingCities: false,
        submitting: false,

        init() {
            if (this.stateId) this.loadDistricts();
        },

        async loadDistricts() {
            this.districtId = '';
            this.districts = [];
            this.cities = [];
            if (!this.stateId) return;

            this.loadingDistricts = true;
            try {
                const res = await fetch(`/admin/users/districts?state_id=${this.stateId}`);
                this.districts = await res.json();
            } catch (e) {
                console.error('Failed to load districts', e);
            } finally {
                this.loadingDistricts = false;
            }
        },

        async loadCities() {
            this.cities = [];
            if (!this.districtId) return;

            this.loadingCities = true;
            try {
                const res = await fetch(`/admin/users/cities?district_id=${this.districtId}`);
                this.cities = await res.json();
            } catch (e) {
                console.error('Failed to load cities', e);
            } finally {
                this.loadingCities = false;
            }
        }
    }
}
lucide.createIcons();
</script>
@endpush
