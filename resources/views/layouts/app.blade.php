<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Odyssey PMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        gold: { DEFAULT: '#B8960C', light: '#FDF6DC', dark: '#8B6914' },
                        navy: { DEFAULT: '#1A1A2E', light: '#252542' }
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        * { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar { background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%); }
        .nav-link { display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:10px; font-size:13px; font-weight:500; color:rgba(255,255,255,0.55); transition:all 0.15s; text-decoration:none; white-space:nowrap; }
        .nav-link:hover { background:rgba(255,255,255,0.08); color:#fff; }
        .nav-link.active { background:linear-gradient(135deg,#B8960C,#8B6914); color:#fff; box-shadow:0 4px 15px rgba(184,150,12,0.3); }
        .nav-link svg { width:16px; height:16px; flex-shrink:0; }
        .nav-section { font-size:10px; font-weight:600; letter-spacing:0.1em; color:rgba(255,255,255,0.25); padding:14px 12px 4px; text-transform:uppercase; }
        .card { background:#fff; border-radius:16px; border:1px solid #f0f0f0; box-shadow:0 1px 4px rgba(0,0,0,0.04); }
        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        ::-webkit-scrollbar { width:4px; } ::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:4px; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50" x-data="{ sidebarOpen: true }">
<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="sidebar flex flex-col shrink-0 transition-all duration-300"
           :style="sidebarOpen ? 'width:240px;min-width:240px' : 'width:64px;min-width:64px'">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 border-b border-white/10" style="min-height:64px">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#B8960C,#8B6914)">
                <span class="text-white font-bold text-sm">O</span>
            </div>
            <div x-show="sidebarOpen" x-cloak>
                <p class="text-white font-semibold text-sm leading-tight">Odyssey</p>
                <p class="text-xs" style="color:rgba(255,255,255,0.35)">Proposal System</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-2 py-4 overflow-y-auto space-y-0.5">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i><span x-show="sidebarOpen" x-cloak>Dashboard</span>
            </a>
            <a href="{{ route('leads.index') }}" class="nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                <i data-lucide="users"></i><span x-show="sidebarOpen" x-cloak>Leads & Customers</span>
            </a>
            <a href="{{ route('quotations.index') }}" class="nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i><span x-show="sidebarOpen" x-cloak>Quotations</span>
            </a>
            @if(auth()->user()->canApproveQuotations())
            <a href="{{ route('approvals.index') }}" class="nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                <i data-lucide="check-square"></i><span x-show="sidebarOpen" x-cloak>Approvals</span>
            </a>
            @endif
            <a href="{{ route('franchises.index') }}" class="nav-link {{ request()->routeIs('franchises.*') ? 'active' : '' }}">
                <i data-lucide="building-2"></i><span x-show="sidebarOpen" x-cloak>Franchises</span>
            </a>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3"></i><span x-show="sidebarOpen" x-cloak>Reports</span>
            </a>
            @if(auth()->user()->isSuperAdmin())
            <p class="nav-section" x-show="sidebarOpen" x-cloak>Administration</p>
            <div class="my-2 border-t border-white/10" x-show="!sidebarOpen"></div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i data-lucide="user-cog"></i><span x-show="sidebarOpen" x-cloak>Users</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i data-lucide="package"></i><span x-show="sidebarOpen" x-cloak>Products & Pricing</span>
            </a>
            <a href="{{ route('admin.territories.index') }}" class="nav-link {{ request()->routeIs('admin.territories.*') ? 'active' : '' }}">
                <i data-lucide="map-pin"></i><span x-show="sidebarOpen" x-cloak>Territories</span>
            </a>
            <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <i data-lucide="shield-check"></i><span x-show="sidebarOpen" x-cloak>Audit Logs</span>
            </a>
            @endif
        </nav>

        {{-- Collapse --}}
        <div class="border-t border-white/10 p-2">
            <button @click="sidebarOpen=!sidebarOpen" class="w-full flex items-center justify-center p-2 rounded-lg hover:bg-white/10 transition" style="color:rgba(255,255,255,0.35)">
                <i data-lucide="chevrons-left" x-show="sidebarOpen"></i>
                <i data-lucide="chevrons-right" x-show="!sidebarOpen" x-cloak></i>
            </button>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-100 px-6 flex items-center justify-between shrink-0" style="min-height:64px">
            <div>
                <h1 class="text-base font-semibold text-gray-800">@yield('page-title','Dashboard')</h1>
                @hasSection('breadcrumb')
                <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">@yield('breadcrumb')</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <button class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <i data-lucide="bell" style="width:16px;height:16px"></i>
                </button>
                <div x-data="{open:false}" class="relative">
                    <button @click="open=!open" class="flex items-center gap-2 bg-gray-50 border border-gray-100 rounded-xl px-3 py-2 hover:bg-gray-100 transition">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0" style="background:linear-gradient(135deg,#B8960C,#8B6914)">
                            {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-semibold text-gray-800 leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400 leading-tight">{{ auth()->user()->role?->display_name }}</p>
                        </div>
                        <i data-lucide="chevron-down" style="width:14px;height:14px;color:#9ca3af"></i>
                    </button>
                    <div x-show="open" @click.outside="open=false" x-cloak
                         class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-50 mb-1">
                            <p class="text-xs text-gray-400">Territory</p>
                            <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->getTerritoryLabel() }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                            <i data-lucide="user" style="width:15px;height:15px"></i> My Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                                <i data-lucide="log-out" style="width:15px;height:15px"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash --}}
        @foreach(['success'=>['green','check-circle'],'error'=>['red','alert-circle'],'warning'=>['yellow','alert-triangle']] as $type=>[$color,$icon])
        @if(session($type))
        <div x-data="{show:true}" x-show="show" x-cloak x-init="setTimeout(()=>show=false,5000)"
             class="mx-6 mt-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border
             {{ $color==='green'?'bg-green-50 border-green-200 text-green-800':($color==='red'?'bg-red-50 border-red-200 text-red-800':'bg-yellow-50 border-yellow-200 text-yellow-800') }}">
            <i data-lucide="{{ $icon }}" style="width:16px;height:16px;flex-shrink:0"></i>
            {{ session($type) }}
            <button @click="show=false" class="ml-auto opacity-50 hover:opacity-100"><i data-lucide="x" style="width:14px;height:14px"></i></button>
        </div>
        @endif
        @endforeach

        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    lucide.createIcons();
    document.addEventListener('alpine:initialized', ()=>lucide.createIcons());
</script>
@stack('scripts')
</body>
</html>
