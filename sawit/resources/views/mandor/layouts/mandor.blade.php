<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mandor') - KELAPAKUY!</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="kp-body">

    @include('mandor.components.sidebar')

    <div class="kp-main">
        @include('mandor.components.header', ['title' => $title ?? 'Dashboard'])

        <main class="kp-content">
            @yield('content')
        </main>
    </div>

</body>
</html>