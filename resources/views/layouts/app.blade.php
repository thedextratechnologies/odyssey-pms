<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Odyssey PMS</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold:  { DEFAULT: '#B8960C', light: '#F5E6C8', dark: '#8B6914' },
                        odyssey: { dark: '#1A1A2E', darker: '#12122A' }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-150; }
        .sidebar-link.active { @apply bg-yellow-600 text-white shadow-sm; }
        .sidebar-link:not(.active) { @apply text-gray-300 hover:bg-white/10 hover:text-white; }
        .badge { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold; }
    </style>
    @stack('styles')
</head>
<body class="h-full" x-data="{ sidebarOpen: true, mobileOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ─────────────────────────────────────────── --}}
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-16'"
        class="hidden lg:flex flex-col bg-odyssey-dark transition-all duration-300 overflow-hidden shrink-0"
    >
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10 min-h-[72px]">
            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center shrink-0">
                <span class="text-white font-bold text-sm">O</span>
            </div>
            <div x-show="sidebarOpen" x-cloak class="overflow-hidden">
                <p class="text-white font-bold text-sm leading-tight">Odyssey</p>
                <p class="text-gray-400 text-xs">Proposal System</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Dashboard</span>
            </a>

            <a href="{{ route('leads.index') }}"
               class="sidebar-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Leads & Customers</span>
            </a>

            <a href="{{ route('quotations.index') }}"
               class="sidebar-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Quotations</span>
            </a>

            @if(auth()->user()->canApproveQuotations())
            <a href="{{ route('approvals.index') }}"
               class="sidebar-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Approvals</span>
                @php $pendingCount = auth()->user()->pendingApprovalCount ?? 0 @endphp
                @if($pendingCount > 0)
                    <span x-show="sidebarOpen" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $pendingCount }}</span>
                @endif
            </a>
            @endif

            <a href="{{ route('franchises.index') }}"
               class="sidebar-link {{ request()->routeIs('franchises.*') ? 'active' : '' }}">
                <i data-lucide="building-2" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Franchises</span>
            </a>

            <a href="{{ route('reports.index') }}"
               class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Reports</span>
            </a>

            {{-- Admin only --}}
            @if(auth()->user()->isSuperAdmin())
            <div class="pt-4 pb-1" x-show="sidebarOpen" x-cloak>
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Administration</p>
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i data-lucide="user-cog" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Users</span>
            </a>

            <a href="{{ route('admin.territories.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.territories.*') ? 'active' : '' }}">
                <i data-lucide="map-pin" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Territories</span>
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i data-lucide="package" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Products & Pricing</span>
            </a>

            <a href="{{ route('admin.audit-logs.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-cloak>Audit Logs</span>
            </a>
            @endif
        </nav>

        {{-- Collapse toggle --}}
        <div class="border-t border-white/10 p-3">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="w-full flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition">
                <i :data-lucide="sidebarOpen ? 'chevrons-left' : 'chevrons-right'" class="w-4 h-4"></i>
            </button>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ─────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                {{-- Mobile menu --}}
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('breadcrumb')
                    <div class="text-sm text-gray-500 flex items-center gap-1">@yield('breadcrumb')</div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                {{-- Notifications --}}
                <button class="relative text-gray-500 hover:text-gray-700">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>

                {{-- User menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 hover:bg-gray-50 rounded-lg px-3 py-2 transition">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->role?->display_name }}</p>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs font-medium text-gray-500">Territory</p>
                            <p class="text-sm text-gray-700">{{ auth()->user()->getTerritoryLabel() }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i data-lucide="user" class="w-4 h-4"></i> My Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 5000)"
             class="mx-6 mt-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="mx-6 mt-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
            {{ session('error') }}
            <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        @endif

        @if(session('warning'))
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="mx-6 mt-4 flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
            {{ session('warning') }}
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>lucide.createIcons();</script>
@stack('scripts')
</body>
</html>
