<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Odyssey PMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Poppins','sans-serif']}}}}</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>* { font-family: 'Poppins', sans-serif; } [x-cloak]{display:none!important}</style>
</head>
<body class="h-full flex items-center justify-center px-4" style="background:linear-gradient(135deg,#1A1A2E 0%,#16213E 50%,#0F3460 100%)">
<div class="w-full max-w-sm">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex w-16 h-16 rounded-2xl items-center justify-center mb-4 shadow-2xl" style="background:linear-gradient(135deg,#B8960C,#8B6914)">
            <span class="text-white text-2xl font-bold">O</span>
        </div>
        <h1 class="text-white text-xl font-semibold">Odyssey Elevators</h1>
        <p class="text-sm mt-1" style="color:rgba(255,255,255,0.4)">Proposal Management System</p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-3xl shadow-2xl p-8">
        <h2 class="text-gray-900 text-lg font-semibold mb-6">Welcome back</h2>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-sm text-red-600">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif
        @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-100 rounded-xl px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" x-data="{showPass:false,loading:false}" @submit="loading=true">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent transition"
                       style="focus:ring-color:#B8960C" placeholder="you@odysseyelevators.com">
            </div>
            <div class="mb-5">
                <div class="flex justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs font-medium" style="color:#B8960C">Forgot?</a>
                </div>
                <div class="relative">
                    <input :type="showPass?'text':'password'" name="password" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-12 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent transition"
                           placeholder="••••••••">
                    <button type="button" @click="showPass=!showPass"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 hover:text-gray-600"
                            x-text="showPass?'Hide':'Show'"></button>
                </div>
            </div>
            <div class="flex items-center mb-6">
                <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border-gray-300">
                <label for="remember" class="ml-2 text-sm text-gray-500">Keep me signed in</label>
            </div>
            <button type="submit" :disabled="loading"
                    class="w-full text-white font-semibold py-3 rounded-xl text-sm transition disabled:opacity-60"
                    style="background:linear-gradient(135deg,#B8960C,#8B6914)">
                <span x-text="loading?'Signing in…':'Sign In →'"></span>
            </button>
        </form>
    </div>
    <p class="text-center text-xs mt-6" style="color:rgba(255,255,255,0.25)">© {{ date('Y') }} Odyssey Elevators Pvt Ltd · Authorised users only</p>
</div>
</body>
</html>
