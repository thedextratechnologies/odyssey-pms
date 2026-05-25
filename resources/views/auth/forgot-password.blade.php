<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Odyssey PMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center px-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-500 rounded-2xl shadow-2xl mb-4">
            <span class="text-white text-2xl font-bold">O</span>
        </div>
        <h1 class="text-white text-xl font-bold">Reset Your Password</h1>
        <p class="text-gray-400 text-sm mt-1">Enter your email and we'll send you a reset link.</p>
    </div>
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-700">✓ {{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
            @foreach($errors->all() as $err)<p>⚠ {{ $err }}</p>@endforeach
        </div>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
                       placeholder="you@odysseyelevators.com">
            </div>
            <button type="submit"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Send Reset Link →
            </button>
        </form>
        <p class="text-center text-sm mt-4">
            <a href="{{ route('login') }}" class="text-yellow-600 hover:underline">← Back to login</a>
        </p>
    </div>
</div>
</body>
</html>
