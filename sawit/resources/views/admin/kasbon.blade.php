{{-- resources/views/admin/kasbon.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Kasbon')

@section('content')
@php
    $stats = [
        [
            'label' => 'Total Kasbon Aktif',
            'value' => number_format($totalAktif, 0, ',', '.'),
            'unit'  => 'Kasbon',
            'sub'   => 'Total Nominal Rp' . number_format($nominalAktif, 0, ',', '.'),
            'icon'  => '<rect x="2" y="7" width="20" height="13" rx="2"/><path d="M16 11h.01"/><path d="M2 10h20"/>',
        ],
        [
            'label' => 'Total Kasbon Lunas',
            'value' => number_format($totalLunas, 0, ',', '.'),
            'unit'  => 'Kasbon',
            'sub'   => 'Total Nominal Rp' . number_format($nominalLunas, 0, ',', '.'),
            'icon'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-4"/>',
        ],
        [
            'label' => 'Menunggu Potongan',
            'value' => number_format($totalMenunggu, 0, ',', '.'),
            'unit'  => 'Kasbon',
            'sub'   => 'Total Nominal Rp' . number_format($nominalMenunggu, 0, ',', '.'),
            'icon'  => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        ],
        [
            'label' => 'Terlambat Bayar',
            'value' => number_format($totalTerlambat, 0, ',', '.'),
            'unit'  => 'Kasbon',
            'sub'   => 'Total Nominal Rp' . number_format($nominalTerlambat, 0, ',', '.'),
            'icon'  => '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/>',
        ],
        [
            'label' => 'Total Semua Kasbon',
            'value' => number_format($totalSemua, 0, ',', '.'),
            'unit'  => 'Kasbon',
            'sub'   => 'Total Nominal Rp' . number_format($nominalSemua, 0, ',', '.'),
            'icon'  => '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>',
        ],
    ];

    $pesanSukses = session('sukses');

    $hal  = $kasbons->currentPage();
    $last = $kasbons->lastPage();
    $mul  = max(1, $hal - 2);
    $akh  = min($last, $hal + 2);

    $peminjamTerpilih = request('peminjam');

    // Form tambah / ubah
    $peminjamLama = old('peminjam');
    $bukaFormLagi = $errors->any() && old('_mode');   // buka ulang dialog bila validasi gagal
    $modeLama     = old('_mode', 'tambah');
    $idLama       = old('_id');
@endphp

<style>
    .kb {
        --kb-deep: #5f6f52;
        --kb-ink: #1f2419;
        --kb-card: #f2f0d9;
        --kb-tan: #b8926e;
        --kb-head: #e2d8bc;

        color: var(--kb-ink);
        font-family: 'Poppins', system-ui, sans-serif;
    }
    .kb, .kb *, .kb *::before, .kb *::after { box-sizing: border-box; }

    .kb-card {
        border-radius: 20px;
        background: var(--kb-card);
        box-shadow: 0 6px 16px rgba(95, 111, 82, .16);
    }

    .kb-flash {
        margin: 0 0 18px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #e3f2d4;
        color: #23652d;
        font-size: 13px;
        font-weight: 500;
    }

    .kb-stats {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }
    .kb-stat {
        display: grid;
        grid-template-columns: auto 1fr;
        column-gap: 12px;
        align-items: start;
        padding: 16px 16px;
    }
    .kb-stat__icon {
        display: grid;
        place-items: center;
        grid-row: 1 / span 4;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--kb-tan);
        color: var(--kb-card);
        flex-shrink: 0;
    }
    .kb-stat__label { margin: 0; font-size: 10.5px; font-weight: 600; }
    .kb-stat__value { margin: 4px 0 0; font-size: 26px; font-weight: 700; line-height: 1.2; }
    .kb-stat__unit  { margin: 2px 0 0; font-size: 10.5px; font-weight: 500; }
    .kb-stat__sub   { margin: 6px 0 0; font-size: 10.5px; font-weight: 700; color: var(--kb-deep); }

    .kb-panel { padding: 18px 16px 16px; }

    .kb-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .kb-title { margin: 0; color: var(--kb-deep); font-size: 15px; font-weight: 600; }

    .kb-filterbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }
    .kb-search { position: relative; flex: 1 1 240px; }
    .kb-search svg {
        position: absolute;
        top: 50%;
        left: 12px;
        color: #7a7a6c;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .kb-input,
    .kb-select {
        height: 38px;
        border: 1px solid #d5d3bb;
        border-radius: 10px;
        background: #fff;
        color: var(--kb-ink);
        font: inherit;
        font-size: 12px;
    }
    .kb-input { width: 100%; padding: 0 12px; }
    .kb-search .kb-input { padding: 0 12px 0 34px; }
    .kb-filterbar .kb-input--month { width: 160px; flex: 0 0 auto; }
    .kb-select {
        min-width: 150px;
        padding: 0 38px 0 14px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235f6f52' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        cursor: pointer;
        flex: 0 0 auto;
    }
    .kb-input:focus-visible,
    .kb-select:focus-visible,
    .kb-btn:focus-visible,
    .kb-icon:focus-visible,
    .kb-page:focus-visible {
        outline: 2px solid var(--kb-deep);
        outline-offset: 2px;
    }

    .kb-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 38px;
        padding: 0 18px;
        border: 0;
        border-radius: 10px;
        background: var(--kb-deep);
        color: #fff;
        font: inherit;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: background-color .15s ease;
        flex: 0 0 auto;
        white-space: nowrap;
    }
    .kb-btn[hidden] { display: none; }
    .kb-btn:hover { background: #4d5b42; color: #fff; }
    .kb-btn--ghost { border: 1px solid #cfcdb4; background: #fff; color: var(--kb-ink); }
    .kb-btn--ghost:hover { background: #f3f2e6; color: var(--kb-ink); }
    .kb-btn--danger { background: #b3382c; }
    .kb-btn--danger:hover { background: #952d23; }

    .kb-sr {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
        white-space: nowrap;
    }

    .kb-wrap { border-radius: 12px; background: #fff; overflow: hidden; }
    .kb-scroll { overflow-x: auto; }
    .kb-table { width: 100%; min-width: 960px; border-collapse: collapse; font-size: 12px; }
    .kb-table th {
        padding: 14px 16px;
        background: var(--kb-head);
        color: #4a4633;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }
    .kb-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #ecebe3;
        white-space: nowrap;
    }
    .kb-table .kb-c { text-align: center; }
    .kb-empty { padding: 34px 16px !important; color: #6b6b60; text-align: center; white-space: normal !important; }

    .kb-orang { display: flex; align-items: center; gap: 10px; }
    .kb-avatar {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--kb-deep);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .kb-orang__nama { font-weight: 600; }
    .kb-orang__sub { display: block; margin-top: 2px; font-size: 10px; color: #8a8a7a; font-weight: 500; }

    .kb-sisa { font-weight: 600; }
    .kb-sisa--lunas { color: #23652d; }
    .kb-sisa--ada { color: #b3382c; }

    .kb-badge {
        display: inline-block;
        min-width: 64px;
        padding: 3px 12px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 600;
        text-align: center;
    }
    .kb-badge--aktif { background: #e3f4d6; color: #23652d; }
    .kb-badge--lunas { background: #e4e9f6; color: #38508a; }
    .kb-badge--terlambat { background: #fbe1de; color: #b3382c; }

    .kb-potongan__utama { font-weight: 500; }
    .kb-potongan__sub { display: block; margin-top: 2px; font-size: 10px; font-weight: 500; }
    .kb-potongan__sub--normal { color: #8a8a7a; }
    .kb-potongan__sub--telat { color: #b3382c; }

    .kb-actions { display: inline-flex; gap: 6px; }
    .kb-icon {
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        padding: 0;
        border: 1px solid #d3d2c8;
        border-radius: 8px;
        background: #fff;
        color: var(--kb-ink);
        cursor: pointer;
        text-decoration: none;
        transition: background-color .15s ease;
    }
    .kb-icon:hover { background: #f1f0e6; }
    .kb-icon--del { border-color: #efc5bf; background: #fdf1ef; color: #b3382c; }
    .kb-icon--del:hover { background: #fadfdb; }

    .kb-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 18px;
        font-size: 11.5px;
    }
    .kb-pager { display: flex; gap: 4px; }
    .kb-page {
        display: grid;
        place-items: center;
        min-width: 26px;
        height: 26px;
        padding: 0 6px;
        border: 1px solid #d3d2c8;
        border-radius: 6px;
        background: #fff;
        color: var(--kb-ink);
        font-size: 11.5px;
        text-decoration: none;
    }
    a.kb-page:hover { background: #f1f0e6; }
    .kb-page--on { border-color: var(--kb-deep); background: var(--kb-deep); color: #fff; }
    .kb-page--off { opacity: .4; }

    /* Dialog */
    .kb-dlg {
        width: min(560px, calc(100vw - 32px));
        padding: 0;
        border: 0;
        border-radius: 20px;
        background: #fbfaf1;
        color: var(--kb-ink);
        box-shadow: 0 24px 60px rgba(30, 40, 20, .38);
        font-family: 'Poppins', system-ui, sans-serif;
        overflow-x: hidden;
    }
    .kb-dlg--sm { width: min(420px, calc(100vw - 32px)); }
    .kb-dlg--lg { width: min(780px, calc(100vw - 32px)); max-height: calc(100vh - 32px); }
    .kb-dlg::backdrop { background: rgba(30, 38, 22, .5); }

    .kb-dlg__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 22px 28px 18px;
        border-bottom: 1px solid #e8e6d0;
    }
    .kb-dlg__title { margin: 0; color: var(--kb-deep); font-size: 18px; font-weight: 600; }
    .kb-dlg__x {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        padding: 0;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #6b6b60;
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .kb-dlg__x:hover { background: #eeecd8; }
    .kb-dlg__x:focus-visible { outline: 2px solid var(--kb-deep); outline-offset: 2px; }

    .kb-dlg__body { padding: 24px 28px 26px; }
    .kb-dlg__text { margin: 0; font-size: 13.5px; line-height: 1.7; }

    .kb-detail { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 22px; }
    .kb-detail--full { grid-column: 1 / -1; }
    .kb-detail dt { margin: 0 0 4px; font-size: 11px; font-weight: 600; color: #8a8a7a; }
    .kb-detail dd { margin: 0; font-size: 13.5px; font-weight: 500; }

    .kb-dlg__actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 28px;
        border-top: 1px solid #e8e6d0;
        background: #f6f5e6;
    }
    .kb-dlg__actions .kb-btn { height: 40px; padding: 0 22px; }

    /* Form tambah / ubah */
    .kf-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 34px;
        align-items: start;
    }
    .kf-col { display: flex; flex-direction: column; gap: 18px; }
    .kf-field { display: flex; flex-direction: column; }
    .kf-field[hidden] { display: none; }
    .kf-label { margin: 0 0 8px; color: var(--kb-deep); font-size: 11px; font-weight: 600; }
    .kf-label span { color: #d33a2c; }
    .kf-hint { margin: 8px 0 0; font-size: 10px; color: #4a4633; }
    .kf-error { margin: 6px 0 0; font-size: 10.5px; color: #b3382c; font-weight: 500; }

    .kf-input,
    .kf-select,
    .kf-textarea {
        width: 100%;
        height: 42px;
        border: 1px solid #e3e1c9;
        border-radius: 10px;
        background: #fff;
        color: var(--kb-ink);
        font: inherit;
        font-size: 11.5px;
    }
    .kf-input, .kf-select { padding: 0 14px; }
    .kf-input::placeholder, .kf-textarea::placeholder { color: #8a8a7a; }
    .kf-input[readonly] { background: #f3f2e6; color: #4a4633; cursor: default; }
    .kf-textarea { height: 86px; padding: 12px 14px; resize: vertical; }
    .kf-select {
        padding-right: 40px;
        appearance: none;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%235f6f52' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
    }
    .kf-icon-input { position: relative; }
    .kf-icon-input svg {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #5f5f52;
        pointer-events: none;
    }
    .kf-icon-input .kf-input { padding-left: 40px; }

    .kf-money {
        display: flex;
        height: 42px;
        border: 1px solid #e3e1c9;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
    }
    .kf-money__prefix {
        display: grid;
        place-items: center;
        min-width: 38px;
        padding: 0 12px;
        background: #ebeae4;
        font-size: 12px;
        font-weight: 600;
    }
    .kf-money .kf-input { height: 100%; border: 0; border-radius: 0; }

    .kf-input:focus-visible,
    .kf-select:focus-visible,
    .kf-textarea:focus-visible { outline: 2px solid var(--kb-deep); outline-offset: 1px; }
    .kf-money:focus-within { outline: 2px solid var(--kb-deep); outline-offset: 1px; }
    .kf-money .kf-input:focus-visible { outline: 0; }
    .kf-invalid { border-color: #b3382c; }

    .kf-info {
        margin-top: 24px;
        padding: 14px 18px 16px;
        border: 1px solid #e3e1c9;
        border-radius: 12px;
        background: #ebeedf;
        font-size: 10.5px;
    }
    .kf-info__title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 8px;
        font-size: 11px;
        font-weight: 600;
    }
    .kf-info ul { margin: 0; padding: 0; list-style: none; }
    .kf-info li { display: flex; align-items: center; gap: 10px; margin-top: 4px; }
    .kf-info li::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--kb-ink);
        flex-shrink: 0;
    }

    @media (max-width: 760px) {
        .kf-grid { grid-template-columns: minmax(0, 1fr); gap: 18px; }
    }
    @media (max-width: 520px) {
        .kb-dlg__head { padding: 18px 20px 14px; }
        .kb-dlg__body { padding: 20px; }
        .kb-dlg__actions { padding: 14px 20px; }
        .kb-detail { grid-template-columns: minmax(0, 1fr); }
        .kb-filterbar .kb-input--month { width: 100%; }
    }
</style>

<div class="kb">
    @if ($pesanSukses)
        <p class="kb-flash" role="status">{{ $pesanSukses }}</p>
    @endif

    {{-- Kartu statistik --}}
    <section class="kb-stats" aria-label="Ringkasan kasbon">
        @foreach ($stats as $stat)
            <article class="kb-card kb-stat">
                <span class="kb-stat__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        {!! $stat['icon'] !!}
                    </svg>
                </span>
                <h2 class="kb-stat__label">{{ $stat['label'] }}</h2>
                <p class="kb-stat__value">{{ $stat['value'] }}</p>
                <p class="kb-stat__unit">{{ $stat['unit'] }}</p>
                <p class="kb-stat__sub">{{ $stat['sub'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="kb-card kb-panel">
        <div class="kb-head">
            <h2 class="kb-title">Daftar Kasbon</h2>
            <button type="button" class="kb-btn" data-aksi="tambah-kasbon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Tambah Kasbon
            </button>
        </div>

        {{-- Filter --}}
        <form method="get" action="{{ route('admin.kasbon.index') }}" class="kb-filterbar" role="search">
            <input class="kb-input kb-input--month" type="month" name="bulan" value="{{ request('bulan') }}"
                   aria-label="Filter bulan" onchange="this.form.submit()">

            <select name="status" class="kb-select" aria-label="Filter status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Potongan</option>
                <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat Bayar</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>

            <select name="peminjam" class="kb-select" aria-label="Filter pekerja atau mandor" onchange="this.form.submit()">
                <option value="">Semua Pekerja &amp; Mandor</option>
                <option value="pekerja" {{ $peminjamTerpilih === 'pekerja' ? 'selected' : '' }}>Pekerja</option>
                <option value="mandor" {{ $peminjamTerpilih === 'mandor' ? 'selected' : '' }}>Mandor</option>
            </select>

            <select name="blok" class="kb-select" aria-label="Filter blok" onchange="this.form.submit()">
                <option value="">Semua Blok</option>
                @foreach ($bloks as $b)
                    <option value="{{ $b->id }}" {{ (string) request('blok') === (string) $b->id ? 'selected' : '' }}>{{ $b->nama_blok }}</option>
                @endforeach
            </select>

            <div class="kb-search">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="search" name="q" value="{{ request('q') }}" class="kb-input"
                       placeholder="Cari nama pekerja atau mandor..." aria-label="Cari kasbon">
            </div>

            <button type="submit" class="kb-sr">Cari</button>

            @if (request()->anyFilled(['bulan', 'status', 'peminjam', 'blok', 'q']))
                <a href="{{ route('admin.kasbon.index') }}" class="kb-btn kb-btn--ghost">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                    </svg>
                    Reset Filter
                </a>
            @endif
        </form>

        <div class="kb-wrap">
            <div class="kb-scroll">
                <table class="kb-table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Pekerja / Mandor</th>
                            <th scope="col">Nominal Kasbon</th>
                            <th scope="col">Sisa Pinjaman</th>
                            <th scope="col" class="kb-c">Status</th>
                            <th scope="col">Rencana Potongan</th>
                            <th scope="col" class="kb-c">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kasbons as $k)
                            @php
                                $statusTampil = $k->status_tampil;
                                $sisaAdaSaldo = $k->sisa_pinjaman > 0;
                            @endphp
                            <tr data-kasbon="{{ json_encode([
                                'id'           => $k->id,
                                'nama'         => $k->nama_peminjam,
                                'kode'         => $k->kode_peminjam,
                                'tipe'         => $k->tipe_peminjam,
                                'blok'         => optional($k->blok)->nama_blok,
                                'tanggal'      => optional($k->tanggal_kasbon)->translatedFormat('d M Y'),
                                'nominal'      => number_format($k->nominal_kasbon, 0, ',', '.'),
                                'sisa'         => number_format($k->sisa_pinjaman, 0, ',', '.'),
                                'tujuan'       => $k->tujuan_kasbon,
                                'rencana'      => optional($k->tanggal_rencana_potongan)->translatedFormat('d M Y'),
                                'potongan'     => number_format($k->nominal_potongan_per_periode, 0, ',', '.'),
                                'tanggalLunas' => optional($k->tanggal_lunas)->translatedFormat('d M Y'),
                                'statusTampil' => $statusTampil,
                                'bisaLunas'    => $k->status === 'aktif',
                                'bisaBatalLunas' => $k->status === 'lunas',

                                // Data mentah untuk form ubah
                                'peminjam'     => $k->tipe_peminjam . ':' . ($k->tipe_peminjam === 'mandor' ? $k->mandor_id : $k->pekerja_id),
                                'tanggalRaw'   => optional($k->tanggal_kasbon)->format('Y-m-d'),
                                'rencanaRaw'   => optional($k->tanggal_rencana_potongan)->format('Y-m-d'),
                                'nominalRaw'   => (int) $k->nominal_kasbon,
                                'potonganRaw'  => (int) $k->nominal_potongan_per_periode,
                            ]) }}">
                                <td>{{ $kasbons->firstItem() + $loop->index }}</td>
                                <td>{{ optional($k->tanggal_kasbon)->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="kb-orang">
                                        <span class="kb-avatar" aria-hidden="true">{{ strtoupper(substr($k->nama_peminjam ?? '-', 0, 1)) }}</span>
                                        <span>
                                            <span class="kb-orang__nama">{{ $k->nama_peminjam ?? '-' }}</span>
                                            <span class="kb-orang__sub">{{ $k->kode_peminjam }} &bull; {{ ucfirst($k->tipe_peminjam) }}</span>
                                        </span>
                                    </span>
                                </td>
                                <td>Rp{{ number_format($k->nominal_kasbon, 0, ',', '.') }}</td>
                                <td>
                                    <span class="kb-sisa {{ $sisaAdaSaldo ? 'kb-sisa--ada' : 'kb-sisa--lunas' }}">
                                        Rp{{ number_format($k->sisa_pinjaman, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="kb-c">
                                    <span class="kb-badge kb-badge--{{ $statusTampil === 'menunggu' ? 'aktif' : $statusTampil }}">
                                        {{ match($statusTampil) {
                                            'aktif', 'menunggu' => 'Aktif',
                                            'terlambat' => 'Terlambat',
                                            'lunas' => 'Lunas',
                                            default => ucfirst($statusTampil),
                                        } }}
                                    </span>
                                </td>
                                <td>
                                    <span class="kb-potongan__utama">Rp{{ number_format($k->nominal_potongan_per_periode, 0, ',', '.') }} / periode</span>
                                    @if ($statusTampil === 'lunas')
                                        <span class="kb-potongan__sub kb-potongan__sub--normal">Lunas pada {{ optional($k->tanggal_lunas)->translatedFormat('d M Y') }}</span>
                                    @elseif ($statusTampil === 'terlambat')
                                        <span class="kb-potongan__sub kb-potongan__sub--telat">Terlambat {{ $k->keterlambatan_hari }} hari</span>
                                    @else
                                        <span class="kb-potongan__sub kb-potongan__sub--normal">(Mulai {{ optional($k->tanggal_rencana_potongan)->translatedFormat('d M Y') }})</span>
                                    @endif
                                </td>
                                <td class="kb-c">
                                    <span class="kb-actions">
                                        <button type="button" class="kb-icon" data-aksi="lihat-kasbon" aria-label="Lihat detail kasbon">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="kb-icon" data-aksi="ubah-kasbon" aria-label="Ubah kasbon">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="kb-icon kb-icon--del" data-aksi="hapus-kasbon" aria-label="Hapus kasbon">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="kb-empty">
                                    @if (request()->anyFilled(['bulan', 'status', 'peminjam', 'blok', 'q']))
                                        Tidak ada data kasbon yang cocok dengan filter ini.
                                    @else
                                        Belum ada data kasbon. Klik "Tambah Kasbon" untuk menambahkan.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="kb-foot">
                <span>
                    @if ($kasbons->total() > 0)
                        Menampilkan {{ $kasbons->firstItem() }} - {{ $kasbons->lastItem() }} dari {{ $kasbons->total() }} data
                    @else
                        Menampilkan 0 data
                    @endif
                </span>

                @if ($kasbons->hasPages())
                    <nav class="kb-pager" aria-label="Halaman">
                        @if ($kasbons->onFirstPage())
                            <span class="kb-page kb-page--off" aria-hidden="true">&lsaquo;</span>
                        @else
                            <a class="kb-page" href="{{ $kasbons->previousPageUrl() }}">&lsaquo;</a>
                        @endif
                        @for ($i = $mul; $i <= $akh; $i++)
                            @if ($i === $hal)
                                <span class="kb-page kb-page--on" aria-current="page">{{ $i }}</span>
                            @else
                                <a class="kb-page" href="{{ $kasbons->url($i) }}">{{ $i }}</a>
                            @endif
                        @endfor
                        @if ($kasbons->hasMorePages())
                            <a class="kb-page" href="{{ $kasbons->nextPageUrl() }}">&rsaquo;</a>
                        @else
                            <span class="kb-page kb-page--off" aria-hidden="true">&rsaquo;</span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </section>
</div>

{{-- ===================== DIALOG: FORM TAMBAH / UBAH KASBON ===================== --}}
<dialog id="dlgFormKasbon" class="kb kb-dlg kb-dlg--lg" aria-labelledby="judulFormKasbon">
    <div class="kb-dlg__box">
        <div class="kb-dlg__head">
            <h2 id="judulFormKasbon" class="kb-dlg__title">Informasi Kasbon</h2>
            <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <form id="formKasbon" method="post" action="{{ route('admin.kasbon.store') }}" novalidate>
            @csrf
            <input type="hidden" name="_method" id="fkMethod" value="PUT" disabled>
            <input type="hidden" name="_mode" id="fkMode" value="{{ $modeLama }}">
            <input type="hidden" name="_id" id="fkId" value="{{ $idLama }}">

            <div class="kb-dlg__body">
                <div class="kf-grid">
                    {{-- Kolom kiri --}}
                    <div class="kf-col">
                        <div class="kf-field">
                            <label class="kf-label" for="fkPeminjam">Pekerja / Mandor <span>*</span></label>
                            <select id="fkPeminjam" name="peminjam" class="kf-select @error('peminjam') kf-invalid @enderror" required>
                                <option value="" disabled {{ $peminjamLama ? '' : 'selected' }}>Pilih Pekerja / Mandor....</option>
                                <optgroup label="Pekerja">
                                    @foreach ($pekerjas as $p)
                                        <option value="pekerja:{{ $p->id }}"
                                                data-blok="{{ optional($bloks->firstWhere('id', $p->blok_id))->nama_blok }}"
                                                {{ $peminjamLama === 'pekerja:' . $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Mandor">
                                    @foreach ($mandors as $m)
                                        <option value="mandor:{{ $m->id }}" {{ $peminjamLama === 'mandor:' . $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('peminjam') <p class="kf-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Blok otomatis dari pekerja; disembunyikan untuk mandor --}}
                        <div class="kf-field" id="fkBlokWrap">
                            <label class="kf-label" for="fkBlokNama">Blok</label>
                            <input type="text" id="fkBlokNama" class="kf-input" value=""
                                   placeholder="Otomatis sesuai pekerja" readonly tabindex="-1">
                        </div>

                        <div class="kf-field">
                            <label class="kf-label" for="fkTanggal">Tanggal Kasbon <span>*</span></label>
                            <div class="kf-icon-input">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                </svg>
                                <input type="date" id="fkTanggal" name="tanggal_kasbon"
                                       value="{{ old('tanggal_kasbon', now()->format('Y-m-d')) }}"
                                       class="kf-input @error('tanggal_kasbon') kf-invalid @enderror" required>
                            </div>
                            @error('tanggal_kasbon') <p class="kf-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="kf-field">
                            <label class="kf-label" for="fkNominal">Nominal Kasbon <span>*</span></label>
                            <div class="kf-money @error('nominal_kasbon') kf-invalid @enderror">
                                <span class="kf-money__prefix">Rp</span>
                                <input type="text" inputmode="numeric" id="fkNominal" name="nominal_kasbon"
                                       value="{{ old('nominal_kasbon') }}" class="kf-input" data-angka
                                       placeholder="Masukkan nominal kasbon...." required>
                            </div>
                            <p class="kf-hint">Masukkan nominal kasbon tanpa titik dan koma.</p>
                            @error('nominal_kasbon') <p class="kf-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Kolom kanan --}}
                    <div class="kf-col">
                        <div class="kf-field">
                            <label class="kf-label" for="fkTujuan">Tujuan Kasbon <span>*</span></label>
                            <textarea id="fkTujuan" name="tujuan_kasbon" class="kf-textarea @error('tujuan_kasbon') kf-invalid @enderror"
                                      placeholder="Masukkan tujuan kasbon...." required>{{ old('tujuan_kasbon') }}</textarea>
                            @error('tujuan_kasbon') <p class="kf-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="kf-field">
                            <label class="kf-label" for="fkRencana">Tanggal Rencana Potongan <span>*</span></label>
                            <div class="kf-icon-input">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                </svg>
                                <input type="date" id="fkRencana" name="tanggal_rencana_potongan"
                                       value="{{ old('tanggal_rencana_potongan') }}"
                                       class="kf-input @error('tanggal_rencana_potongan') kf-invalid @enderror"
                                       aria-label="Pilih tanggal mulai potongan" required>
                            </div>
                            @error('tanggal_rencana_potongan') <p class="kf-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="kf-field">
                            <label class="kf-label" for="fkPotongan">Nominal Potongan Per Periode <span>*</span></label>
                            <div class="kf-money @error('nominal_potongan_per_periode') kf-invalid @enderror">
                                <span class="kf-money__prefix">Rp</span>
                                <input type="text" inputmode="numeric" id="fkPotongan" name="nominal_potongan_per_periode"
                                       value="{{ old('nominal_potongan_per_periode') }}" class="kf-input" data-angka
                                       placeholder="Masukkan nominal potongan per periode...." required>
                            </div>
                            <p class="kf-hint">Nominal yang akan dipotong setiap periode.</p>
                            @error('nominal_potongan_per_periode') <p class="kf-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="kf-info" role="note">
                    <p class="kf-info__title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        Informasi Penting
                    </p>
                    <ul>
                        <li>Nominal kasbon akan dipotong dari upah pekerja sesuai nominal potongan per periode.</li>
                        <li>Pastikan tanggal rencana potongan sesuai dengan kesepakatan.</li>
                    </ul>
                </div>
            </div>

            <div class="kb-dlg__actions">
                <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Batal</button>
                <button type="submit" class="kb-btn" id="fkSimpan">Simpan Kasbon</button>
            </div>
        </form>
    </div>
</dialog>

{{-- ===================== DIALOG: LIHAT DETAIL KASBON ===================== --}}
<dialog id="dlgLihatKasbon" class="kb kb-dlg" aria-labelledby="judulLihatKasbon">
    <div class="kb-dlg__box">
        <div class="kb-dlg__head">
            <h2 id="judulLihatKasbon" class="kb-dlg__title">Detail Kasbon</h2>
            <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
        <div class="kb-dlg__body">
            <dl class="kb-detail">
                <div>
                    <dt>Pekerja / Mandor</dt>
                    <dd id="dkNama">-</dd>
                </div>
                <div>
                    <dt>Blok</dt>
                    <dd id="dkBlok">-</dd>
                </div>
                <div>
                    <dt>Tanggal Kasbon</dt>
                    <dd id="dkTanggal">-</dd>
                </div>
                <div>
                    <dt>Nominal Kasbon</dt>
                    <dd id="dkNominal">-</dd>
                </div>
                <div class="kb-detail--full">
                    <dt>Tujuan Kasbon</dt>
                    <dd id="dkTujuan">-</dd>
                </div>
                <div>
                    <dt>Tanggal Rencana Potongan</dt>
                    <dd id="dkRencana">-</dd>
                </div>
                <div>
                    <dt>Nominal Potongan / Periode</dt>
                    <dd id="dkPotongan">-</dd>
                </div>
                <div>
                    <dt>Sisa Pinjaman</dt>
                    <dd id="dkSisa">-</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd id="dkStatus">-</dd>
                </div>
                <div class="kb-detail--full" id="dkEstimasiWrap" hidden>
                    <dt>Lunas Pada</dt>
                    <dd id="dkEstimasi">-</dd>
                </div>
            </dl>
        </div>
        <div class="kb-dlg__actions">
            <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Tutup</button>
            <form id="formLunasKasbon" method="post" action="{{ url('admin/kasbon') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="kb-btn" id="btnLunasKasbon">Tandai Lunas</button>
            </form>
            <form id="formBatalLunasKasbon" method="post" action="{{ url('admin/kasbon') }}"
                  onsubmit="return confirm('Batalkan status lunas? Kasbon akan kembali menjadi aktif.');">
                @csrf
                @method('PATCH')
                <button type="submit" class="kb-btn kb-btn--danger" id="btnBatalLunasKasbon" hidden>Batalkan Lunas</button>
            </form>
        </div>
    </div>
</dialog>

{{-- ===================== DIALOG: HAPUS KASBON ===================== --}}
<dialog id="dlgHapusKasbon" class="kb kb-dlg kb-dlg--sm" aria-labelledby="judulHapusKasbon">
    <div class="kb-dlg__box">
        <div class="kb-dlg__head">
            <h2 id="judulHapusKasbon" class="kb-dlg__title">Hapus Data Kasbon?</h2>
            <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
        <form id="formHapusKasbon" method="post" action="{{ url('admin/kasbon') }}">
            @csrf
            @method('DELETE')
            <div class="kb-dlg__body">
                <p class="kb-dlg__text">
                    Data kasbon <strong id="namaHapusKasbon"></strong> akan dihapus permanen.
                </p>
            </div>
            <div class="kb-dlg__actions">
                <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Batal</button>
                <button type="submit" class="kb-btn kb-btn--danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    (function () {
        var urlDasar = {!! json_encode(url('admin/kasbon')) !!};
        var hariIni = {!! json_encode(now()->format('Y-m-d')) !!};

        // ---------- Dialog: Form Tambah / Ubah ----------
        var dlgForm = document.getElementById('dlgFormKasbon');
        var formKasbon = document.getElementById('formKasbon');
        var fkMethod = document.getElementById('fkMethod');
        var fkMode = document.getElementById('fkMode');
        var fkId = document.getElementById('fkId');
        var fkJudul = document.getElementById('judulFormKasbon');
        var fkSimpan = document.getElementById('fkSimpan');
        var fkPeminjam = document.getElementById('fkPeminjam');
        var fkBlokWrap = document.getElementById('fkBlokWrap');
        var fkBlokNama = document.getElementById('fkBlokNama');

        // Blok otomatis: pekerja -> tampil blok miliknya, mandor -> disembunyikan
        function sinkronBlok() {
            var nilai = fkPeminjam.value || '';
            var opt = fkPeminjam.options[fkPeminjam.selectedIndex];

            if (nilai.indexOf('mandor:') === 0) {
                fkBlokWrap.hidden = true;
                fkBlokNama.value = '';
            } else {
                fkBlokWrap.hidden = false;
                fkBlokNama.value = nilai ? ((opt && opt.dataset.blok) || '-') : '';
            }
        }
        fkPeminjam.addEventListener('change', sinkronBlok);

        function aturModeForm(mode, id) {
            fkMode.value = mode;
            fkId.value = id || '';
            if (mode === 'ubah') {
                formKasbon.action = urlDasar + '/' + id;
                fkMethod.disabled = false;
                fkJudul.textContent = 'Ubah Kasbon';
                fkSimpan.textContent = 'Simpan Perubahan';
            } else {
                formKasbon.action = urlDasar;
                fkMethod.disabled = true;
                fkJudul.textContent = 'Informasi Kasbon';
                fkSimpan.textContent = 'Simpan Kasbon';
            }
        }

        function kosongkanError() {
            dlgForm.querySelectorAll('.kf-error').forEach(function (el) { el.remove(); });
            dlgForm.querySelectorAll('.kf-invalid').forEach(function (el) { el.classList.remove('kf-invalid'); });
        }

        function bukaFormTambah() {
            kosongkanError();
            formKasbon.reset();
            fkPeminjam.selectedIndex = 0;
            document.getElementById('fkNominal').value = '';
            document.getElementById('fkTujuan').value = '';
            document.getElementById('fkRencana').value = '';
            document.getElementById('fkPotongan').value = '';
            document.getElementById('fkTanggal').value = hariIni;
            aturModeForm('tambah');
            sinkronBlok();
            dlgForm.showModal();
        }

        function bukaFormUbah(d) {
            kosongkanError();
            fkPeminjam.value = d.peminjam || '';
            document.getElementById('fkTanggal').value = d.tanggalRaw || '';
            document.getElementById('fkNominal').value = d.nominalRaw || '';
            document.getElementById('fkTujuan').value = d.tujuan || '';
            document.getElementById('fkRencana').value = d.rencanaRaw || '';
            document.getElementById('fkPotongan').value = d.potonganRaw || '';
            aturModeForm('ubah', d.id);
            sinkronBlok();
            dlgForm.showModal();
        }

        // Input nominal hanya angka
        document.querySelectorAll('[data-angka]').forEach(function (el) {
            el.addEventListener('input', function () {
                el.value = el.value.replace(/\D/g, '');
            });
        });

        // Validasi gagal: buka lagi dialog dengan isian lama
        @if ($bukaFormLagi)
            aturModeForm({!! json_encode($modeLama) !!}, {!! json_encode($idLama) !!});
            sinkronBlok();
            dlgForm.showModal();
        @endif

        // ---------- Dialog: Lihat Detail ----------
        var dlgLihat = document.getElementById('dlgLihatKasbon');
        var formLunas = document.getElementById('formLunasKasbon');
        var btnLunas = document.getElementById('btnLunasKasbon');
        var formBatalLunas = document.getElementById('formBatalLunasKasbon');
        var btnBatalLunas = document.getElementById('btnBatalLunasKasbon');
        var estimasiWrap = document.getElementById('dkEstimasiWrap');

        function bukaLihatKasbon(d) {
            document.getElementById('dkNama').textContent = (d.nama || '-') + ' (' + (d.kode || '-') + ' \u2022 ' + (d.tipe === 'mandor' ? 'Mandor' : 'Pekerja') + ')';
            document.getElementById('dkBlok').textContent = d.blok || '-';
            document.getElementById('dkTanggal').textContent = d.tanggal || '-';
            document.getElementById('dkNominal').textContent = 'Rp' + (d.nominal || '0');
            document.getElementById('dkTujuan').textContent = d.tujuan || '-';
            document.getElementById('dkRencana').textContent = d.rencana || '-';
            document.getElementById('dkPotongan').textContent = 'Rp' + (d.potongan || '0') + ' / periode';
            document.getElementById('dkSisa').textContent = 'Rp' + (d.sisa || '0');

            var labelStatus = { aktif: 'Aktif', menunggu: 'Menunggu Potongan', terlambat: 'Terlambat Bayar', lunas: 'Lunas' };
            document.getElementById('dkStatus').textContent = labelStatus[d.statusTampil] || d.statusTampil;

            // Tanggal lunas hanya ditampilkan bila kasbon sudah lunas
            if (d.statusTampil === 'lunas') {
                estimasiWrap.hidden = false;
                document.getElementById('dkEstimasi').textContent = d.tanggalLunas || '-';
            } else {
                estimasiWrap.hidden = true;
            }

            formLunas.action = urlDasar + '/' + d.id + '/lunas';
            btnLunas.hidden = !d.bisaLunas;

            formBatalLunas.action = urlDasar + '/' + d.id + '/batal-lunas';
            btnBatalLunas.hidden = !d.bisaBatalLunas;

            dlgLihat.showModal();
        }

        // ---------- Dialog: Hapus ----------
        var dlgHapus = document.getElementById('dlgHapusKasbon');
        var formHapus = document.getElementById('formHapusKasbon');
        var namaHapus = document.getElementById('namaHapusKasbon');

        function bukaHapusKasbon(d) {
            formHapus.action = urlDasar + '/' + d.id;
            namaHapus.textContent = (d.nama || '-') + ' \u2013 ' + (d.tanggal || '');
            dlgHapus.showModal();
        }

        // ---------- Delegasi klik ----------
        document.addEventListener('click', function (e) {
            var tombol = e.target.closest('[data-aksi]');
            if (!tombol) return;
            var aksi = tombol.dataset.aksi;

            if (aksi === 'tutup') {
                tombol.closest('dialog').close();
                return;
            }

            if (aksi === 'tambah-kasbon') {
                bukaFormTambah();
                return;
            }

            var baris = tombol.closest('tr');
            if (!baris) return;

            var d = JSON.parse(baris.dataset.kasbon);
            if (aksi === 'lihat-kasbon') bukaLihatKasbon(d);
            if (aksi === 'ubah-kasbon') bukaFormUbah(d);
            if (aksi === 'hapus-kasbon') bukaHapusKasbon(d);
        });

        [dlgForm, dlgLihat, dlgHapus].forEach(function (d) {
            d.addEventListener('click', function (e) { if (e.target === d) d.close(); });
        });
    })();
</script>
@endsection