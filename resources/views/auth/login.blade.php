<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Odyssey PMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: { DEFAULT: '#B8960C', light: '#F5E6C8', dark: '#8B6914' },
                        odyssey: { dark: '#1A1A2E' }
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gradient-to-br from-odyssey-dark via-gray-900 to-gray-800 flex items-center justify-center px-4">

<div class="w-full max-w-md">

    {{-- Logo / Brand --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-500 rounded-2xl shadow-2xl mb-4">
            <span class="text-white text-2xl font-bold">O</span>
        </div>
        <h1 class="text-white text-2xl font-bold">Odyssey Elevators</h1>
        <p class="text-gray-400 text-sm mt-1">Proposal Management System</p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-gray-900 text-xl font-semibold mb-6">Sign in to your account</h2>

        {{-- Errors --}}
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5">⚠</span> {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-700">
            ✓ {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" x-data="{ showPass: false, loading: false }" @submit="loading = true">
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input
                    type="email" id="email" name="email"
                    value="{{ old('email') }}"
                    required autocomplete="email" autofocus
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition @error('email') border-red-400 bg-red-50 @enderror"
                    placeholder="you@odysseyelevators.com"
                >
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-yellow-600 hover:text-yellow-700 font-medium">Forgot password?</a>
                </div>
                <div class="relative">
                    <input
                        :type="showPass ? 'text' : 'password'"
                        id="password" name="password"
                        required autocomplete="current-password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition @error('password') border-red-400 bg-red-50 @enderror"
                        placeholder="••••••••"
                    >
                    <button type="button" @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs">
                        <span x-text="showPass ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center mb-6">
                <input type="checkbox" id="remember" name="remember"
                       class="h-4 w-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-500">
                <label for="remember" class="ml-2 text-sm text-gray-600">Keep me signed in</label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    :disabled="loading"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 disabled:opacity-60 text-white font-semibold py-2.5 rounded-lg text-sm transition focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                <span x-show="!loading">Sign In →</span>
                <span x-show="loading" x-cloak>Signing in…</span>
            </button>
        </form>
    </div>

    <p class="text-center text-gray-500 text-xs mt-6">
        © {{ date('Y') }} Odyssey Elevators Pvt Ltd · For authorised users only
    </p>
</div>

</body>
</html>
