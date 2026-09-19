{{--
    resources/views/admin/components/sidebar.blade.php

    Cara pakai di layout:
        @include('admin.components.sidebar')

    Sesuaikan nama route di array $menus di bawah dengan route milik proyekmu.
    Jika route belum ada, link otomatis diarahkan ke "#" supaya halaman tidak error.
--}}

@php
    $user = auth()->user();
    $userName = $user->name ?? 'Admin';

    $menus = [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'match' => 'admin.dashboard',
            'icon'  => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        ],
        [
            'label' => 'Kelola Blok',
            'route' => 'admin.blok.index',
            'match' => 'admin.blok.*',
            'icon'  => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/>',
        ],
        [
            'label' => 'Mandor',
            'route' => 'admin.mandor.index',
            'match' => 'admin.mandor.*',
            'icon'  => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        ],
        [
            'label' => 'Kelola Jenis Pekerjaan',
            'route' => 'admin.jenis-pekerjaan.index',
            'match' => 'admin.jenis-pekerjaan.*',
            'icon'  => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
        ],
        [
            'label' => 'Kelola Tarif Upah',
            'route' => 'admin.tarif-upah.index',
            'match' => 'admin.tarif-upah.*',
            'icon'  => '<circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>',
        ],
        [
            'label' => 'Pekerja',
            'route' => 'admin.pekerja.index',
            'match' => 'admin.pekerja.*',
            'icon'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        ],
        [
            'label' => 'Hasil Kerja',
            'route' => 'admin.hasil-kerja.index',
            'match' => 'admin.hasil-kerja.*',
            'icon'  => '<rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>',
        ],
        [
            'label' => 'Kasbon',
            'route' => 'admin.kasbon.index',
            'match' => 'admin.kasbon.*',
            'icon'  => '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="m9 15 3 3 3-3"/>',
        ],
        [
            'label' => 'Laporan Hasil Kerja',
            'route' => 'admin.laporan-hasil-kerja.index',
            'match' => 'admin.laporan-hasil-kerja.*',
            'icon'  => '<path d="M12 22h6a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v10"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10.4 12.6a2 2 0 1 1 3 3L8 21l-4 1 1-4z"/>',
        ],
        [
            'label' => 'Laporan Upah',
            'route' => 'admin.laporan-upah.index',
            'match' => 'admin.laporan-upah.*',
            'icon'  => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h2"/><path d="M14 13h2"/><path d="M8 17h2"/><path d="M14 17h2"/>',
        ],
        [
            'label' => 'Pengaturan',
            'route' => 'admin.pengaturan',
            'match' => 'admin.pengaturan*',
            'icon'  => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
        ],
    ];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .kp-sidebar {
        --kp-deep: #5f6f52;
        --kp-ink: #3f4a35;
        --kp-sage: #a9b388;
        --kp-line: #e3e8d3;

        position: fixed;
        inset: 0 auto 0 0;
        z-index: 40;
        display: flex;
        flex-direction: column;
        width: 242px;
        background: linear-gradient(180deg, #fdfbf8 0%, #a9b388 94%, #5f6f52 100%);
        box-shadow: 6px 0 18px rgba(95, 111, 82, .18);
        font-family: 'Poppins', system-ui, sans-serif;
        transition: transform .25s ease;
    }

    /* Brand */
    .kp-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 20px 20px 18px;
        text-decoration: none;
    }
    .kp-brand__logo {
        display: grid;
        place-items: center;
        flex: none;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #dfe5cf;
        overflow: hidden;
    }
    .kp-brand__logo img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .kp-brand__name {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: .01em;
        color: var(--kp-ink);
    }

    /* Menu */
    .kp-nav {
        flex: 1;
        min-height: 0;
        margin: 0;
        padding: 0 11px 0 6px;
        list-style: none;
        overflow-y: auto;
        scrollbar-width: none;
    }
    .kp-nav::-webkit-scrollbar { display: none; }
    .kp-nav li + li { margin-top: 2px; }

    .kp-link {
        display: flex;
        align-items: center;
        gap: 14px;
        height: 46px;
        padding: 0 18px;
        border-radius: 10px;
        color: var(--kp-ink);
        font-size: 13.5px;
        font-weight: 500;
        text-decoration: none;
        transition: background-color .15s ease, color .15s ease;
    }
    .kp-link:hover { background: rgba(95, 111, 82, .12); }
    .kp-link:focus-visible,
    .kp-profile__btn:focus-visible,
    .kp-profile__menu button:focus-visible {
        outline: 2px solid var(--kp-deep);
        outline-offset: 2px;
    }
    .kp-link.is-active {
        color: #fff;
        background: linear-gradient(90deg, var(--kp-deep) 0%, var(--kp-sage) 100%);
        box-shadow: 0 3px 8px rgba(95, 111, 82, .3);
    }
    .kp-link svg,
    .kp-brand__logo svg { flex: none; }

    /* Profil */
    .kp-profile {
        position: relative;
        margin: 12px;
    }
    .kp-profile__btn {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        height: 52px;
        padding: 0 14px 0 10px;
        border: 1.5px solid var(--kp-line);
        border-radius: 14px;
        background: var(--kp-deep);
        color: #fff;
        font: inherit;
        font-size: 12px;
        font-weight: 500;
        text-align: left;
        cursor: pointer;
    }
    .kp-profile__avatar {
        display: grid;
        place-items: center;
        flex: none;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #dfe5cf;
        color: var(--kp-deep);
        font-size: 14px;
        font-weight: 600;
    }
    .kp-profile__name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .kp-profile__chevron { transition: transform .2s ease; }
    .kp-profile__btn[aria-expanded="true"] .kp-profile__chevron { transform: rotate(180deg); }

    .kp-profile__menu {
        position: absolute;
        right: 0;
        bottom: calc(100% + 8px);
        left: 0;
        padding: 6px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(50, 60, 40, .22);
    }
    .kp-profile__menu[hidden] { display: none; }
    .kp-profile__menu button {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px 12px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #a33a2f;
        font: inherit;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
    }
    .kp-profile__menu button:hover { background: #f8ebe9; }

    /* Overlay (mobile) */
    .kp-overlay {
        position: fixed;
        inset: 0;
        z-index: 30;
        background: rgba(30, 38, 22, .45);
    }
    .kp-overlay[hidden] { display: none; }

    /* Layar kecil: sidebar tersembunyi, dibuka lewat tombol hamburger */
    @media (max-width: 1023px) {
        .kp-sidebar { transform: translateX(-100%); }
        .kp-sidebar.is-open { transform: none; }
    }
    @media (min-width: 1024px) {
        .kp-overlay { display: none !important; }
    }
    @media (prefers-reduced-motion: reduce) {
        .kp-sidebar, .kp-link, .kp-profile__chevron { transition: none; }
    }
</style>

<aside id="kpSidebar" class="kp-sidebar" aria-label="Menu utama">
    {{-- Logo --}}
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="kp-brand">
        <span class="kp-brand__logo">
            <img src="{{ asset('images/logo.png') }}" alt="" width="38" height="38">
        </span>
        <span class="kp-brand__name">KELAPAKUY!</span>
    </a>

    {{-- Menu --}}
    <ul class="kp-nav">
        @foreach ($menus as $menu)
            @php
                $isActive = request()->routeIs($menu['match']);
                $href = Route::has($menu['route']) ? route($menu['route']) : '#';
            @endphp
            <li>
                <a href="{{ $href }}"
                   class="kp-link {{ $isActive ? 'is-active' : '' }}"
                   @if ($isActive) aria-current="page" @endif>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        {!! $menu['icon'] !!}
                    </svg>
                    <span>{{ $menu['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    {{-- Profil admin --}}
    <div class="kp-profile">
        <div id="kpProfileMenu" class="kp-profile__menu" hidden>
            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                @csrf
                <button type="submit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>

        <button id="kpProfileBtn" type="button" class="kp-profile__btn"
                aria-haspopup="true" aria-expanded="false" aria-controls="kpProfileMenu">
            {{-- Ganti dengan <img> jika user punya foto profil --}}
            <span class="kp-profile__avatar" aria-hidden="true">{{ strtoupper(mb_substr($userName, 0, 1)) }}</span>
            <span class="kp-profile__name">{{ $userName }}</span>
            <svg class="kp-profile__chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </button>
    </div>
</aside>

<div id="kpOverlay" class="kp-overlay" hidden onclick="toggleSidebar()"></div>

<script>
    (function () {
        var sidebar = document.getElementById('kpSidebar');
        var overlay = document.getElementById('kpOverlay');
        var btn = document.getElementById('kpProfileBtn');
        var menu = document.getElementById('kpProfileMenu');

        // Dipanggil dari tombol hamburger di header: onclick="toggleSidebar()"
        window.toggleSidebar = function () {
            var open = sidebar.classList.toggle('is-open');
            overlay.hidden = !open;
        };

        function setProfileMenu(open) {
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            menu.hidden = !open;
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            setProfileMenu(menu.hidden);
        });

        document.addEventListener('click', function (e) {
            if (!menu.hidden && !menu.contains(e.target)) setProfileMenu(false);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            setProfileMenu(false);
            if (sidebar.classList.contains('is-open')) window.toggleSidebar();
        });
    })();
</script>