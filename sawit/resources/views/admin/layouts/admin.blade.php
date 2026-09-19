{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Kelapakuy</title>

    {{-- Font & logo dimuat di <head> supaya sudah siap sebelum halaman pertama digambar --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('images/logo.png') }}">

    <style>
        body { margin: 0; }

        /* Transisi halus antar halaman: sidebar & header tetap diam, hanya isi konten yang memudar.
           Browser yang belum mendukung akan mengabaikannya (pindah halaman seperti biasa). */
        @view-transition { navigation: auto; }
        .kp-sidebar { view-transition-name: kp-sidebar; }
        .kp-topbar  { view-transition-name: kp-topbar; }
        ::view-transition-old(root),
        ::view-transition-new(root) { animation-duration: .15s; }

        @media (prefers-reduced-motion: reduce) {
            @view-transition { navigation: none; }
        }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="kp-main">
        @include('admin.components.header', ['title' => $__env->yieldContent('title', 'Dashboard')])

        <main class="kp-content">
            @yield('content')
        </main>
    </div>
</body>
</html>