@php
    $mandorMenus = [
        [
            'label' => 'Dashboard',
            'path'  => 'mandor/dashboard',
            'icon'  => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        ],
        [
            'label' => 'Input Data',
            'path'  => 'mandor/input-data-harian',
            'icon'  => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/>',
        ],
        [
            'label' => 'Riwayat',
            'path'  => 'mandor/riwayat',
            'icon'  => '<path d="M20 6H10"/><path d="M20 12H10"/><path d="M20 18H10"/><circle cx="4" cy="6" r="2"/><circle cx="4" cy="12" r="2"/><circle cx="4" cy="18" r="2"/>',
        ],
        [
            'label' => 'Akun',
            'path'  => 'mandor/akun',
            'icon'  => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        ],
    ];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root { --kp-rail-w: 64px; }

    .kp-body {
        margin: 0;
        background: #ececec;
        font-family: 'Poppins', system-ui, sans-serif;
    }

    .kp-rail {
        --kp-deep: #5f6f52;
        --kp-sage: #a9b388;
        --kp-cream: #fefae0;

        position: fixed;
        inset: 0 auto 0 0;
        z-index: 40;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: var(--kp-rail-w);
        padding: 18px 0;
        background: linear-gradient(180deg, #fdfbf8 0%, #a9b388 60%, #5f6f52 100%);
        box-shadow: 4px 0 14px rgba(95, 111, 82, .18);
    }

    .kp-rail__logo {
        display: grid;
        place-items: center;
        flex: none;
        width: 38px;
        height: 38px;
        margin-bottom: 26px;
        border-radius: 50%;
        background: #fff;
        overflow: hidden;
    }
    .kp-rail__logo img { width: 100%; height: 100%; object-fit: cover; }

    .kp-rail__nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }

    .kp-rail__link {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        color: var(--kp-deep);
        text-decoration: none;
        transition: background-color .15s ease, color .15s ease;
    }
    .kp-rail__link:hover { background: rgba(255,255,255,.35); }
    .kp-rail__link.is-active {
        background: var(--kp-deep);
        color: var(--kp-cream);
        box-shadow: 0 3px 8px rgba(95, 111, 82, .35);
    }

    .kp-rail__logout {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        margin-top: auto;
        border: none;
        border-radius: 12px;
        background: #fdf1ef;
        color: #a33a2f;
        cursor: pointer;
    }
    .kp-rail__logout:hover { background: #f8ddd8; }

    .kp-main {
        min-height: 100vh;
        margin-left: var(--kp-rail-w);
        background: #fbfaf5;
    }
    .kp-content { padding: 16px 16px 90px; }
</style>

<aside class="kp-rail" aria-label="Menu utama">
    <a href="{{ url('mandor/dashboard') }}" class="kp-rail__logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </a>

    <nav class="kp-rail__nav">
        @foreach ($mandorMenus as $menu)
            @php $isActive = request()->is($menu['path'], $menu['path'].'/*'); @endphp
            <a href="{{ url($menu['path']) }}"
               class="kp-rail__link {{ $isActive ? 'is-active' : '' }}"
               title="{{ $menu['label'] }}"
               @if ($isActive) aria-current="page" @endif>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    {!! $menu['icon'] !!}
                </svg>
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
        @csrf
        <button type="submit" class="kp-rail__logout" title="Keluar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" x2="9" y1="12" y2="12"/>
            </svg>
        </button>
    </form>
</aside>