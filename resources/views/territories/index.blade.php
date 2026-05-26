@extends('layouts.app')
@section('title','Territory Management')
@section('page-title','Territory Management')

@section('content')
<div x-data="territoryManager()" x-init="init()">

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach(['state'=>['States','map','blue'],'district'=>['Districts','map-pin','purple'],'city'=>['Cities','navigation','green']] as $t=>[$label,$icon,$color])
    <a href="{{ route('admin.territories.index', ['type'=>$t]) }}"
       class="card p-5 flex items-center gap-4 hover:shadow-md transition cursor-pointer {{ request('type',$t==='state'?'state':null)===$t?'ring-2 ring-yellow-400':'' }}">
        <div class="w-11 h-11 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center shrink-0">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $counts[$t] }}</p>
            <p class="text-sm text-gray-500">{{ $label }}</p>
        </div>
    </a>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Add Territory Form --}}
    <div class="card p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-5 flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4 text-yellow-500"></i>
            Add Territory
        </h3>
        <form method="POST" action="{{ route('admin.territories.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Type</label>
                <select name="type" x-model="addType" @change="addParentId=''"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <option value="state">State</option>
                    <option value="district">District</option>
                    <option value="city">City</option>
                </select>
            </div>

            <div class="mb-4" x-show="addType==='district'" x-cloak>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Parent State</label>
                <select name="parent_id" x-model="addParentId"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <option value="">Select State…</option>
                    @foreach($states as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4" x-show="addType==='city'" x-cloak>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Parent State</label>
                <select @change="loadDistricts($event.target.value)"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-3">
                    <option value="">Select State…</option>
                    @foreach($states as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Parent District</label>
                <select name="parent_id" x-model="addParentId" :disabled="districts.length===0"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:bg-gray-50">
                    <option value="">Select District…</option>
                    <template x-for="d in districts" :key="d.id">
                        <option :value="d.id" x-text="d.name"></option>
                    </template>
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Name</label>
                <input type="text" name="name" required placeholder="e.g. Chennai"
                       class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <button type="submit"
                    class="w-full text-white font-semibold py-2.5 rounded-xl text-sm transition"
                    style="background:linear-gradient(135deg,#B8960C,#8B6914)">
                Add Territory
            </button>
        </form>
    </div>

    {{-- Territory List --}}
    <div class="lg:col-span-2">
        {{-- Tab bar --}}
        <div class="flex gap-1 mb-4 bg-white rounded-xl border border-gray-100 p-1 shadow-sm">
            @foreach(['state'=>'States','district'=>'Districts','city'=>'Cities'] as $t=>$label)
            <a href="{{ route('admin.territories.index', array_merge(request()->query(), ['type'=>$t])) }}"
               class="flex-1 text-center py-2 rounded-lg text-sm font-medium transition
               {{ request('type','state')===$t ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
               style="{{ request('type','state')===$t ? 'background:linear-gradient(135deg,#B8960C,#8B6914)' : '' }}">
                {{ $label }} <span class="ml-1 text-xs opacity-70">({{ $counts[$t] }})</span>
            </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" class="mb-4 flex gap-2">
            <input type="hidden" name="type" value="{{ request('type','state') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search {{ request('type','state') }}s…"
                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white">
            <button type="submit" class="px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-xl">Search</button>
            @if(request('search'))
            <a href="{{ route('admin.territories.index', ['type'=>request('type','state')]) }}"
               class="px-4 py-2.5 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50">Clear</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        @if(request('type','state') !== 'state')
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Parent</th>
                        @endif
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($territories as $t)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $t->type==='state' ? 'bg-blue-100 text-blue-700' : ($t->type==='district' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700') }}">
                                    {{ strtoupper(substr($t->name,0,1)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $t->name }}</span>
                            </div>
                        </td>
                        @if(request('type','state') !== 'state')
                        <td class="px-5 py-3.5 text-gray-500 text-sm">{{ $t->parent?->name ?? '—' }}</td>
                        @endif
                        <td class="px-5 py-3.5 text-center">
                            <span class="badge {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $t->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition">
                                {{-- Inline edit --}}
                                <div x-data="{ editing: false, name: '{{ addslashes($t->name) }}' }">
                                    <form x-show="!editing" @submit.prevent="">
                                        <button type="button" @click="editing=true"
                                                class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                    <div x-show="editing" x-cloak class="flex items-center gap-1">
                                        <form method="POST" action="{{ route('admin.territories.update', $t) }}" class="flex items-center gap-1">
                                            @csrf @method('PUT')
                                            <input type="text" name="name" x-model="name"
                                                   class="border border-yellow-300 rounded-lg px-2 py-1 text-xs w-28 focus:outline-none focus:ring-1 focus:ring-yellow-400">
                                            <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                        <button @click="editing=false" class="p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Toggle active --}}
                                <form method="POST" action="{{ route('admin.territories.toggle', $t) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg {{ $t->is_active ? 'text-gray-400 hover:text-orange-500 hover:bg-orange-50' : 'text-gray-400 hover:text-green-600 hover:bg-green-50' }}"
                                            title="{{ $t->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i data-lucide="{{ $t->is_active ? 'eye-off' : 'eye' }}" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('admin.territories.destroy', $t) }}"
                                      onsubmit="return confirm('Delete {{ $t->name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-gray-400">
                            <i data-lucide="map-pin" class="w-10 h-10 mx-auto opacity-20 mb-2"></i>
                            <p class="text-sm font-medium">No {{ request('type','state') }}s found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($territories->hasPages())
            <div class="border-t border-gray-100 px-5 py-3 flex items-center justify-between">
                <p class="text-sm text-gray-400">{{ $territories->firstItem() }}–{{ $territories->lastItem() }} of {{ $territories->total() }}</p>
                {{ $territories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
function territoryManager() {
    return {
        addType: 'state',
        addParentId: '',
        districts: [],
        async loadDistricts(stateId) {
            if (!stateId) { this.districts = []; return; }
            const res = await fetch(`/admin/territories/districts?state_id=${stateId}`);
            this.districts = await res.json();
        },
        init() {}
    }
}
lucide.createIcons();
</script>
@endpush
