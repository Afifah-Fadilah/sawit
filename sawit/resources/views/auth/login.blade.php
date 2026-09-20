<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin (Login)</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen overflow-hidden font-['Poppins']">

    <div class="bg-[#f7f6f1] min-h-screen flex flex-col md:flex-row">

        <!-- KIRI: Gambar -->
        <div class="md:w-1/2 relative h-[300px] md:h-screen">
            <img src="{{ asset('images/bg-sawit2.png') }}" alt="Kelapa Sawit"
                class="w-full h-full object-cover object-[45%_center]">

            <div class="absolute top-6 left-6 bg-white rounded-full pl-2 pr-5 py-2 flex items-center gap-2 shadow">
                <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 rounded-full object-cover">
                <span class="font-bold text-green-800 text-sm">KELAPAKUY!</span>
            </div>
        </div>

        <!-- KANAN: Form -->
        <div class="md:w-1/2 flex items-center justify-center p-8 md:h-screen">

            <!-- Glass Card -->
            <div class="w-full max-w-md bg-white/30 backdrop-blur-xl border border-white/40 rounded-3xl shadow-xl p-10">

                <h1 class="text-3xl font-bold text-green-800 text-center mb-1">Masuk</h1>
                <p class="text-center text-sm text-gray-600 mb-6">
                    Masukkan Nama Pengguna dan Kata Sandi Anda
                </p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="username" class="block text-sm font-semibold text-gray-800 mb-1">
                            Nama Pengguna
                        </label>

                        <input id="username" type="text" name="username" value="{{ old('username') }}" required
                            autofocus autocomplete="username"
                            class="w-full rounded-xl bg-[#c9cdb8] border-none py-3 px-4 text-gray-800 placeholder-gray-500 focus:ring-2 focus:ring-green-700">

                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-2">
                        <label for="password" class="block text-sm font-semibold text-gray-800 mb-1">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="w-full rounded-xl bg-[#c9cdb8] border-none py-3 px-4 pr-10 text-gray-800 focus:ring-2 focus:ring-green-700">

                            <button type="button"
                                onclick="const el=document.getElementById('password'); 
                        const isPassword = el.type === 'password';
                        el.type = isPassword ? 'text' : 'password';
                        document.getElementById('eye-open').classList.toggle('hidden', isPassword);
                        document.getElementById('eye-closed').classList.toggle('hidden', !isPassword);"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800">

                                <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>

                                <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between mt-3 mb-6 text-sm">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-gray-700">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-gray-400 text-green-700 focus:ring-green-700">
                            ingat saya
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-gray-700 underline hover:text-green-800">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full bg-green-800 hover:bg-green-900 text-white font-semibold py-3 rounded-xl transition">
                        Masuk
                    </button>
                </form>

            </div>
        </div>

    </div>

</body>

</html>
