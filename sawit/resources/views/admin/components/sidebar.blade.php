{{--
    resources/views/admin/components/sidebar.blade.php

    Struktur layout yang dipakai (sidebar + header + konten):

        <body>
            @include('admin.components.sidebar')

            <div class="kp-main">
                @include('admin.components.header', ['title' => 'Dashboard'])

                <main class="kp-content">
                    @yield('content')
                </main>
            </div>
        </body>

    Menu memakai URL (bukan nama route). Sesuaikan 'path' di array $menus dengan
    URL halaman di proyekmu, contoh: 'admin/dashboard' untuk http://127.0.0.1:8000/admin/dashboard
--}}

@php
    $user = auth()->user();
    $userName = $user->name ?? 'Admin';

    $menus = [
        [
            'label' => 'Dashboard',
            'path'  => 'admin/dashboard',
            'icon'  => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        ],
        [
            'label' => 'Kelola Blok',
            'path'  => 'admin/kelola-blok',
            'icon'  => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/>',
        ],
        [
            'label' => 'Mandor',
            'path'  => 'admin/mandor',
            'icon'  => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        ],
        [
            'label' => 'Kelola Jenis Pekerjaan',
            'path'  => 'admin/jenis-pekerjaan',
            'icon'  => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
        ],
        [
            'label' => 'Kelola Tarif Upah',
            'path'  => 'admin/tarif-upah',
            'icon'  => '<circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>',
        ],
        [
            'label' => 'Pekerja',
            'path'  => 'admin/pekerja',
            'icon'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        ],
        [
            'label' => 'Hasil Kerja',
            'path'  => 'admin/hasil-kerja',
            'icon'  => '<rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>',
        ],
        [
            'label' => 'Kasbon',
            'path'  => 'admin/kasbon',
            'icon'  => '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="m9 15 3 3 3-3"/>',
        ],
        [
            'label' => 'Laporan Hasil Kerja',
            'path'  => 'admin/laporan-hasil-kerja',
            'icon'  => '<path d="M12 22h6a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v10"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10.4 12.6a2 2 0 1 1 3 3L8 21l-4 1 1-4z"/>',
        ],
        [
            'label' => 'Laporan Upah',
            'path'  => 'admin/laporan-upah',
            'icon'  => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h2"/><path d="M14 13h2"/><path d="M8 17h2"/><path d="M14 17h2"/>',
        ],
        [
            'label' => 'Pengaturan',
            'path'  => 'admin/pengaturan',
            'icon'  => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
        ],
    ];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

{{-- Pulihkan status sidebar (tertutup/terbuka) sebelum halaman digambar, supaya tidak berkedip --}}
<script>
    try {
        if (localStorage.getItem('kpSidebarCollapsed') === '1') {
            document.documentElement.classList.add('kp-collapsed');
        }
    } catch (e) {}
</script>

<style>
    :root { --kp-sidebar-w: 242px; }

    .kp-sidebar {
        --kp-deep: #5f6f52;
        --kp-ink: #3f4a35;
        --kp-sage: #a9b388;
        --kp-line: #e3e8d3;
        --kp-cream: #fefae0;

        position: fixed;
        inset: 0 auto 0 0;
        z-index: 40;
        display: flex;
        flex-direction: column;
        width: var(--kp-sidebar-w);
        background: linear-gradient(180deg, #fdfbf8 0%, #a9b388 94%, #5f6f52 100%);
        box-shadow: 6px 0 18px rgba(95, 111, 82, .18);
        font-family: 'Poppins', system-ui, sans-serif;
        transition: transform .25s ease, visibility 0s;
    }

    /* Area konten mengikuti lebar sidebar */
    .kp-main {
        min-height: 100vh;
        margin-left: var(--kp-sidebar-w);
        background: #fbfaf5;
        transition: margin-left .25s ease;
    }
    .kp-content { padding: 24px 26px 32px; }

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
        color: var(--kp-cream);
        background: linear-gradient(175deg, #5f6f52 0%, #a9b388 90%);
        box-shadow: 0 3px 8px rgba(95, 111, 82, .3);
    }
    /* Bayangan tipis supaya teks tetap terbaca di bagian gradasi yang lebih terang */
    .kp-link.is-active span { text-shadow: 0 1px 2px rgba(40, 52, 30, .45); }
    .kp-link svg { flex: none; }

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
        .kp-main { margin-left: 0; }
        .kp-sidebar {
            transform: translateX(-100%);
            visibility: hidden;
            transition: transform .25s ease, visibility 0s .25s;
        }
        .kp-sidebar.is-open {
            transform: none;
            visibility: visible;
            transition: transform .25s ease, visibility 0s;
        }
    }

    /* Layar lebar: sidebar terbuka, bisa ditutup lewat tombol hamburger */
    @media (min-width: 1024px) {
        .kp-overlay { display: none !important; }
        html.kp-collapsed .kp-main { margin-left: 0; }
        html.kp-collapsed .kp-sidebar {
            transform: translateX(-100%);
            visibility: hidden;
            transition: transform .25s ease, visibility 0s .25s;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .kp-sidebar, .kp-main, .kp-link, .kp-profile__chevron { transition: none !important; }
    }
</style>

<aside id="kpSidebar" class="kp-sidebar" aria-label="Menu utama">
    {{-- Logo --}}
    <a href="{{ url('admin/dashboard') }}" class="kp-brand">
        <span class="kp-brand__logo">
            <img src="{{ asset('images/logo.png') }}" alt="" width="38" height="38">
        </span>
        <span class="kp-brand__name">KELAPAKUY!</span>
    </a>

    {{-- Menu --}}
    <ul class="kp-nav">
        @foreach ($menus as $menu)
            @php
                $isActive = request()->is($menu['path'], $menu['path'] . '/*');
                $href = url($menu['path']);
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
        var root = document.documentElement;
        var sidebar = document.getElementById('kpSidebar');
        var overlay = document.getElementById('kpOverlay');
        var btn = document.getElementById('kpProfileBtn');
        var menu = document.getElementById('kpProfileMenu');
        var desktop = window.matchMedia('(min-width: 1024px)');

        // Sidebar sedang terlihat atau tidak (tergantung ukuran layar)
        function isSidebarOpen() {
            return desktop.matches
                ? !root.classList.contains('kp-collapsed')
                : sidebar.classList.contains('is-open');
        }

        // Sinkronkan status aria-expanded pada semua tombol hamburger
        function syncToggleButtons() {
            var open = isSidebarOpen() ? 'true' : 'false';
            document.querySelectorAll('[data-kp-sidebar-toggle]').forEach(function (el) {
                el.setAttribute('aria-expanded', open);
            });
        }

        // Buka/tutup sidebar. Dipakai oleh tombol hamburger di header.
        window.toggleSidebar = function () {
            if (desktop.matches) {
                var collapsed = root.classList.toggle('kp-collapsed');
                try { localStorage.setItem('kpSidebarCollapsed', collapsed ? '1' : '0'); } catch (e) {}
            } else {
                var open = sidebar.classList.toggle('is-open');
                overlay.hidden = !open;
            }
            syncToggleButtons();
        };

        document.addEventListener('click', function (e) {
            if (e.target.closest('[data-kp-sidebar-toggle]')) window.toggleSidebar();
        });

        // Saat ukuran layar berpindah antara mobile dan desktop, reset status buka mobile
        desktop.addEventListener('change', function () {
            sidebar.classList.remove('is-open');
            overlay.hidden = true;
            syncToggleButtons();
        });

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncToggleButtons);
        } else {
            syncToggleButtons();
        }

        // Menu profil
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
            if (!desktop.matches && sidebar.classList.contains('is-open')) window.toggleSidebar();
        });
    })();
</script>