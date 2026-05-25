<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password — Odyssey PMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center px-4">

<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-500 rounded-2xl shadow-2xl mb-4">
            <span class="text-white text-2xl font-bold">O</span>
        </div>
        <h1 class="text-white text-xl font-bold">Set a New Password</h1>
        <p class="text-gray-400 text-sm mt-1">You must change your password before continuing.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
            @foreach($errors->all() as $err)<p>⚠ {{ $err }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('auth.change-password') }}"
              x-data="{
                password: '',
                confirm: '',
                get strength() {
                    let s = 0;
                    if (this.password.length >= 8) s++;
                    if (/[A-Z]/.test(this.password)) s++;
                    if (/[0-9]/.test(this.password)) s++;
                    if (/[^A-Za-z0-9]/.test(this.password)) s++;
                    return s;
                },
                get strengthLabel() { return ['','Weak','Fair','Good','Strong'][this.strength]; },
                get strengthColor() { return ['bg-gray-200','bg-red-400','bg-yellow-400','bg-blue-400','bg-green-500'][this.strength]; },
                get match() { return this.confirm.length > 0 && this.password === this.confirm; }
              }">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                <input type="password" name="password" x-model="password"
                       required minlength="8"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                       placeholder="Minimum 8 characters">
                {{-- Strength bar --}}
                <div class="mt-2 flex gap-1 h-1.5">
                    <template x-for="i in 4">
                        <div class="flex-1 rounded-full transition-colors duration-300"
                             :class="i <= strength ? strengthColor : 'bg-gray-200'"></div>
                    </template>
                </div>
                <p class="text-xs mt-1" :class="strength >= 3 ? 'text-green-600' : 'text-gray-400'"
                   x-text="password.length > 0 ? 'Strength: ' + strengthLabel : 'Use uppercase, lowercase, numbers and symbols'"></p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation" x-model="confirm"
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                       placeholder="Repeat password">
                <p x-show="confirm.length > 0 && !match" x-cloak class="text-xs text-red-500 mt-1">Passwords do not match</p>
                <p x-show="match" x-cloak class="text-xs text-green-600 mt-1">✓ Passwords match</p>
            </div>

            <button type="submit"
                    :disabled="!match || strength < 2"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Set Password & Continue →
            </button>
        </form>
    </div>
</div>

</body>
</html>
