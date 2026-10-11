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

{{-- overflow-hidden hanya di desktop. Di HP halaman harus bisa discroll, kalau tidak form terpotong. --}}
<body class="min-h-screen bg-[#f7f6f1] font-['Poppins'] md:overflow-hidden">

    <div class="bg-[#f7f6f1] min-h-[100dvh] flex flex-col md:flex-row">

        <!-- ATAS (HP) / KIRI (desktop): Gambar -->
        <div class="relative h-[220px] sm:h-[280px] md:h-screen md:w-1/2 overflow-hidden rounded-b-[2rem] md:rounded-none">
            {{--
                Di HP gambar dilebarkan 145% dan ditempel ke kiri supaya lekukan putih di sisi kanan
                gambar (bawaan file bg-sawit2.png) ikut terpotong keluar layar.
                Angka 145% bisa disetel: makin besar = lekukan makin tersembunyi, gambar makin ter-zoom.
                Di desktop (md ke atas) tampilannya sama persis seperti sebelumnya.
            --}}
            <img src="{{ asset('images/bg-sawit2.png') }}" alt="Kelapa Sawit"
                class="h-full w-[145%] max-w-none object-cover object-[0%_40%] md:w-full md:max-w-full md:object-[45%_center]">

            <div
                class="absolute top-4 left-4 md:top-6 md:left-6 bg-white rounded-full pl-2 pr-4 md:pr-5 py-1.5 md:py-2 flex items-center gap-2 shadow">
                <img src="{{ asset('images/logo.png') }}" alt="" class="w-8 h-8 rounded-full object-cover">
                <span class="font-bold text-green-800 text-sm">KELAPAKUY!</span>
            </div>
        </div>

        <!-- BAWAH (HP) / KANAN (desktop): Form -->
        <div
            class="relative z-10 flex flex-1 items-start justify-center px-4 pb-10 md:h-screen md:w-1/2 md:flex-none md:items-center md:p-8">

            <!-- Glass Card: di HP sedikit menimpa gambar supaya terasa menyatu -->
            <div
                class="-mt-6 w-full max-w-md rounded-3xl border border-white/40 bg-white/70 px-6 pb-8 pt-8 shadow-xl backdrop-blur-xl md:mt-0 md:bg-white/30 md:p-10">

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

                        {{-- autocapitalize/autocorrect dimatikan: keyboard HP tidak lagi mengubah huruf pertama jadi kapital --}}
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required
                            autofocus autocomplete="username" autocapitalize="none" autocorrect="off"
                            spellcheck="false"
                            class="w-full rounded-xl bg-[#c9cdb8] border-none py-3 px-4 text-base text-gray-800 placeholder-gray-500 focus:ring-2 focus:ring-green-700">

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
                                class="w-full rounded-xl bg-[#c9cdb8] border-none py-3 px-4 pr-12 text-base text-gray-800 focus:ring-2 focus:ring-green-700">

                            <button type="button" aria-label="Tampilkan atau sembunyikan kata sandi"
                                onclick="const el=document.getElementById('password'); 
                        const isPassword = el.type === 'password';
                        el.type = isPassword ? 'text' : 'password';
                        document.getElementById('eye-open').classList.toggle('hidden', isPassword);
                        document.getElementById('eye-closed').classList.toggle('hidden', !isPassword);"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-gray-600 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-700">

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
                    <div class="mt-3 mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 text-sm">
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
                        class="w-full bg-green-800 hover:bg-green-900 text-white text-base font-semibold py-3 rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-green-700 focus-visible:ring-offset-2">
                        Masuk
                    </button>
                </form>

            </div>
        </div>

    </div>

</body>

</html>