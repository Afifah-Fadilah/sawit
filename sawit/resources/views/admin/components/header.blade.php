{{--
    resources/views/admin/components/header.blade.php

    Cara pakai (judul halaman bisa diganti per halaman):
        @include('admin.components.header', ['title' => 'Dashboard'])

    Tombol hamburger bekerja bersama sidebar.blade.php, jadi keduanya harus di-include
    di halaman yang sama.
--}}

@php
    $pageTitle = $title ?? 'Dashboard';
@endphp

<style>
    .kp-topbar {
        --kp-deep: #5f6f52;
        --kp-ink: #4d5b42;

        position: sticky;
        top: 0;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 84px;
        padding: 0 24px;
        background: #fbfaf5;
        box-shadow: 0 1px 0 rgba(95, 111, 82, .10), 0 6px 12px -8px rgba(95, 111, 82, .18);
        font-family: 'Poppins', system-ui, sans-serif;
    }

    .kp-topbar__left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    /* Tombol hamburger (buka/tutup sidebar) */
    .kp-topbar__toggle {
        display: grid;
        place-items: center;
        flex: none;
        width: 40px;
        height: 40px;
        padding: 0;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: var(--kp-deep);
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .kp-topbar__toggle:hover { background: rgba(95, 111, 82, .12); }
    .kp-topbar__toggle:focus-visible {
        outline: 2px solid var(--kp-deep);
        outline-offset: 2px;
    }

    .kp-topbar__title {
        margin: 0;
        overflow: hidden;
        color: var(--kp-deep);
        font-size: 19px;
        font-weight: 600;
        letter-spacing: .01em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Pil tanggal */
    .kp-date {
        display: flex;
        align-items: center;
        flex: none;
        gap: 12px;
        padding: 12px 22px 12px 14px;
        border-radius: 16px;
        background: #e6e8d5;
        box-shadow: 0 3px 8px rgba(95, 111, 82, .14);
        color: var(--kp-ink);
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1;
    }
    .kp-date svg { flex: none; color: var(--kp-deep); }

    @media (max-width: 600px) {
        .kp-topbar { min-height: 68px; padding: 0 14px; gap: 10px; }
        .kp-topbar__title { font-size: 16px; }
        .kp-date { gap: 8px; padding: 9px 12px; font-size: 12px; border-radius: 12px; }
        .kp-date svg { width: 20px; height: 20px; }
    }
</style>

<header class="kp-topbar">
    <div class="kp-topbar__left">
        <button type="button" class="kp-topbar__toggle" data-kp-sidebar-toggle
                aria-controls="kpSidebar" aria-expanded="true" aria-label="Buka atau tutup menu samping">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                <line x1="4" y1="7" x2="20" y2="7"/>
                <line x1="4" y1="12" x2="20" y2="12"/>
                <line x1="4" y1="17" x2="20" y2="17"/>
            </svg>
        </button>

        <h1 class="kp-topbar__title">{{ $pageTitle }}</h1>
    </div>

    <div class="kp-date">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect width="18" height="18" x="3" y="4" rx="2"/>
            <path d="M16 2v4"/>
            <path d="M8 2v4"/>
            <path d="M3 10h18"/>
        </svg>
        {{-- Isi awal dari server; langsung diganti JS dengan format "07 September 2026" --}}
        <time id="kpDate" datetime="{{ now()->toDateString() }}">{{ now()->format('d/m/Y') }}</time>
    </div>
</header>

<script>
    (function () {
        var el = document.getElementById('kpDate');
        if (!el) return;

        var formatter = new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        function pad(n) { return n < 10 ? '0' + n : '' + n; }

        function render() {
            var now = new Date();
            el.textContent = formatter.format(now);
            el.setAttribute('datetime',
                now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()));
        }

        // Perbarui tepat saat pergantian hari (tengah malam)
        function scheduleNextUpdate() {
            var now = new Date();
            var midnight = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 1);
            setTimeout(function () {
                render();
                scheduleNextUpdate();
            }, midnight - now);
        }

        render();
        scheduleNextUpdate();

        // Jika tab/komputer baru "bangun", pastikan tanggalnya langsung benar
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) render();
        });
    })();
</script>