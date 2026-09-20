{{-- resources/views/admin/mandor.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Mandor')

@section('content')
    @php
        $stats = [
            [
                'label' => 'Total Mandor',
                'value' => number_format($totalMandor, 0, ',', '.'),
                'unit' => 'Data',
                'icon' =>
                    '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            ],
            [
                'label' => 'Mandor Aktif',
                'value' => number_format($mandorAktif, 0, ',', '.'),
                'unit' => 'Orang',
                'icon' =>
                    '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/>',
            ],
            [
                'label' => 'Nonaktif',
                'value' => number_format($mandorNonaktif, 0, ',', '.'),
                'unit' => 'Orang',
                'icon' => '<circle cx="12" cy="8" r="4"/><path d="M6 21v-1a6 6 0 0 1 12 0v1"/>',
            ],
        ];

        // Tab aktif: dari old input (validasi gagal) -> flash session 'tab' (setelah simpan sukses) -> query string -> default
        $tabAktif = old('_tab', session('tab', request('tab', 'daftar')));

        // Pesan sukses: mendukung 'sukses' (MandorController) dan 'success' (JadwalMandorController)
        $pesanSukses = session('sukses', session('success'));

        // Daftar mandor lengkap untuk dropdown jadwal.
        // Kalau controller mengirim $semuaMandor, pakai itu; kalau tidak, fallback ke halaman aktif.
        $mandorJadwal = isset($semuaMandor) ? $semuaMandor : $mandors;

        // Supaya link pagination membawa tab yang benar
        $mandors->appends(['tab' => 'daftar']);
        $jadwals->appends(['tab' => 'jadwal']);

        // Pagination Daftar Mandor
        $halM = $mandors->currentPage();
        $lastM = $mandors->lastPage();
        $mulM = max(1, $halM - 2);
        $akhM = min($lastM, $halM + 2);

        // Pagination Jadwal
        $halJ = $jadwals->currentPage();
        $lastJ = $jadwals->lastPage();
        $mulJ = max(1, $halJ - 2);
        $akhJ = min($lastJ, $halJ + 2);

        // Daftar afdeling unik dari blok yang ada, untuk dropdown filter di form jadwal
        $daftarAfdeling = $bloks->pluck('afdeling')->unique()->values();
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

        .kb,
        .kb *,
        .kb *::before,
        .kb *::after {
            box-sizing: border-box;
        }

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
            grid-template-columns: repeat(auto-fill, minmax(210px, 240px));
            gap: 20px;
            margin-bottom: 22px;
        }

        .kb-stat {
            display: grid;
            grid-template-columns: auto 1fr;
            column-gap: 14px;
            align-items: start;
            padding: 20px 18px;
        }

        .kb-stat__icon {
            display: grid;
            place-items: center;
            grid-row: 1 / span 3;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--kb-tan);
            color: var(--kb-card);
        }

        .kb-stat__label {
            margin: 0;
            font-size: 11.5px;
            font-weight: 600;
        }

        .kb-stat__value {
            margin: 4px 0 0;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
        }

        .kb-stat__unit {
            margin: 2px 0 0;
            font-size: 11.5px;
            font-weight: 500;
        }

        .kb-panel {
            padding: 18px 16px 16px;
        }

        /* Tabs */
        .kb-tabs {
            display: flex;
            gap: 22px;
            border-bottom: 1px solid #e8e6d0;
            margin-bottom: 16px;
        }

        .kb-tab {
            padding: 4px 0 12px;
            border: 0;
            border-bottom: 2px solid transparent;
            background: transparent;
            color: #8a8a7a;
            font: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }

        .kb-tab--on {
            border-bottom-color: var(--kb-deep);
            color: var(--kb-deep);
            font-weight: 700;
        }

        .kb-panel-body[hidden] {
            display: none;
        }

        .kb-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .kb-title {
            margin: 0;
            color: var(--kb-deep);
            font-size: 15px;
            font-weight: 600;
        }

        .kb-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .kb-search {
            position: relative;
        }

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

        .kb-input {
            width: 230px;
            padding: 0 12px 0 34px;
        }

        .kb-select {
            min-width: 150px;
            padding: 0 38px 0 14px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235f6f52' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            cursor: pointer;
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
            transition: background-color .15s ease;
        }

        .kb-btn:hover {
            background: #4d5b42;
        }

        .kb-btn[hidden] {
            display: none;
        }

        .kb-btn--ghost {
            border: 1px solid #cfcdb4;
            background: #fff;
            color: var(--kb-ink);
        }

        .kb-btn--ghost:hover {
            background: #f3f2e6;
        }

        .kb-btn--danger {
            background: #b3382c;
        }

        .kb-btn--danger:hover {
            background: #952d23;
        }

        .kb-sr {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            white-space: nowrap;
        }

        .kb-wrap {
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }

        .kb-scroll {
            overflow-x: auto;
        }

        .kb-table {
            width: 100%;
            min-width: 740px;
            border-collapse: collapse;
            font-size: 12px;
        }

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

        .kb-table .kb-c {
            text-align: center;
        }

        .kb-empty {
            padding: 34px 16px !important;
            color: #6b6b60;
            text-align: center;
            white-space: normal !important;
        }

        .kb-orang {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .kb-avatar {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--kb-deep);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .kb-orang__nama {
            font-weight: 600;
        }

        .kb-orang__sub {
            display: block;
            margin-top: 2px;
            font-size: 10px;
            color: #8a8a7a;
            font-weight: 500;
        }

        .kb-badge {
            display: inline-block;
            min-width: 54px;
            padding: 3px 12px;
            border-radius: 6px;
            font-size: 10.5px;
            font-weight: 600;
            text-align: center;
        }

        .kb-badge--aktif {
            background: #e3f4d6;
            color: #23652d;
        }

        .kb-badge--nonaktif {
            background: #fbe1de;
            color: #b3382c;
        }

        .kb-actions {
            display: inline-flex;
            gap: 6px;
        }

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
            transition: background-color .15s ease;
        }

        .kb-icon:hover {
            background: #f1f0e6;
        }

        .kb-icon--del {
            border-color: #efc5bf;
            background: #fdf1ef;
            color: #b3382c;
        }

        .kb-icon--del:hover {
            background: #fadfdb;
        }

        .kb-foot {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 18px;
            font-size: 11.5px;
        }

        .kb-pager {
            display: flex;
            gap: 4px;
        }

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

        a.kb-page:hover {
            background: #f1f0e6;
        }

        .kb-page--on {
            border-color: var(--kb-deep);
            background: var(--kb-deep);
            color: #fff;
        }

        .kb-page--off {
            opacity: .4;
        }

        /* Dialog (sama seperti kelolablok) */
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

        .kb-dlg--sm {
            width: min(420px, calc(100vw - 32px));
        }

        .kb-dlg::backdrop {
            background: rgba(30, 38, 22, .5);
        }

        .kb-dlg__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 22px 28px 18px;
            border-bottom: 1px solid #e8e6d0;
        }

        .kb-dlg__title {
            margin: 0;
            color: var(--kb-deep);
            font-size: 18px;
            font-weight: 600;
        }

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

        .kb-dlg__x:hover {
            background: #eeecd8;
        }

        .kb-dlg__x:focus-visible {
            outline: 2px solid var(--kb-deep);
            outline-offset: 2px;
        }

        .kb-dlg__body {
            padding: 24px 28px 26px;
        }

        .kb-dlg__text {
            margin: 0;
            font-size: 13.5px;
            line-height: 1.7;
        }

        .kb-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 22px;
        }

        .kb-field {
            min-width: 0;
        }

        .kb-field--full {
            grid-column: 1 / -1;
        }

        .kb-label {
            display: block;
            margin-bottom: 7px;
            font-size: 12.5px;
            font-weight: 500;
        }

        .kb-field .kb-input,
        .kb-field .kb-select {
            display: block;
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1px solid #d5d3bb;
            border-radius: 10px;
            background-color: #fff;
            font-size: 13.5px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .kb-field .kb-input::placeholder {
            color: #a3a293;
        }

        .kb-field .kb-input:focus-visible,
        .kb-field .kb-select:focus-visible {
            outline: none;
            border-color: var(--kb-deep);
            box-shadow: 0 0 0 3px rgba(95, 111, 82, .2);
        }

        .kb-field .kb-select {
            padding-right: 38px;
        }

        .kb-field .kb-input:disabled,
        .kb-field .kb-select:disabled {
            background-color: #f3f2e6;
            color: #3f4a35;
            -webkit-text-fill-color: #3f4a35;
            opacity: 1;
        }

        .kb-hint {
            margin: 6px 0 0;
            color: #6b6b60;
            font-size: 11px;
        }

        .kb-err {
            margin: 6px 0 0;
            color: #a33a2f;
            font-size: 11.5px;
        }

        .kb-dlg__actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 28px;
            border-top: 1px solid #e8e6d0;
            background: #f6f5e6;
        }

        .kb-dlg__actions .kb-btn {
            height: 40px;
            padding: 0 22px;
        }

        .kb-blok-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 18px;
            padding: 12px 14px;
            border: 1px solid #d5d3bb;
            border-radius: 10px;
            background: #fff;
            min-height: 44px;
        }

        .kb-blok-opsi {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 500;
        }

        .kb-blok-opsi input:disabled+span,
        .kb-blok-opsi:has(input:disabled) {
            color: #a3a293;
        }

        .kb-blok-placeholder {
            color: #a3a293;
            font-size: 12.5px;
        }

        @media (max-width: 520px) {
            .kb-form {
                grid-template-columns: minmax(0, 1fr);
                gap: 16px;
            }

            .kb-dlg__head {
                padding: 18px 20px 14px;
            }

            .kb-dlg__body {
                padding: 20px;
            }

            .kb-dlg__actions {
                padding: 14px 20px;
            }

            .kb-tabs {
                gap: 14px;
                overflow-x: auto;
            }
        }
    </style>

    <div class="kb">
        @if ($pesanSukses)
            <p class="kb-flash" role="status">{{ $pesanSukses }}</p>
        @endif

        {{-- Kartu statistik --}}
        <section class="kb-stats" aria-label="Ringkasan mandor">
            @foreach ($stats as $stat)
                <article class="kb-card kb-stat">
                    <span class="kb-stat__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            {!! $stat['icon'] !!}
                        </svg>
                    </span>
                    <h2 class="kb-stat__label">{{ $stat['label'] }}</h2>
                    <p class="kb-stat__value">{{ $stat['value'] }}</p>
                    <p class="kb-stat__unit">{{ $stat['unit'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="kb-card kb-panel">
            {{-- Tab --}}
            <div class="kb-tabs" role="tablist">
                <button type="button" class="kb-tab {{ $tabAktif === 'daftar' ? 'kb-tab--on' : '' }}" data-tab="daftar"
                    role="tab">Daftar Mandor</button>
                <button type="button" class="kb-tab {{ $tabAktif === 'akun' ? 'kb-tab--on' : '' }}" data-tab="akun"
                    role="tab">Kelola Akun</button>
                <button type="button" class="kb-tab {{ $tabAktif === 'jadwal' ? 'kb-tab--on' : '' }}" data-tab="jadwal"
                    role="tab">Jadwal Mandor</button>
            </div>

            {{-- ============ TAB 1: DAFTAR MANDOR ============ --}}
            <div class="kb-panel-body" data-tab-panel="daftar" @if ($tabAktif !== 'daftar') hidden @endif>
                <div class="kb-head">
                    <h2 class="kb-title">Daftar Mandor</h2>
                    <div class="kb-toolbar">
                        <form method="get" action="{{ url('admin/mandor') }}" class="kb-toolbar" role="search">
                            <input type="hidden" name="tab" value="daftar">
                            <div class="kb-search">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.3-4.3" />
                                </svg>
                                <input type="search" name="q" value="{{ request('q') }}" class="kb-input"
                                    placeholder="Cari nama atau no. HP..." aria-label="Cari mandor">
                            </div>

                            <select name="status" class="kb-select" aria-label="Filter status"
                                onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>

                            <button type="submit" class="kb-sr">Cari</button>
                        </form>

                        <button type="button" class="kb-btn" data-aksi="tambah-mandor">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            Tambah Mandor
                        </button>
                    </div>
                </div>

                <div class="kb-wrap">
                    <div class="kb-scroll">
                        <table class="kb-table">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Mandor</th>
                                    <th scope="col">No. HP</th>
                                    <th scope="col" class="kb-c">Status</th>
                                    <th scope="col" class="kb-c">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mandors as $mandor)
                                    <tr
                                        data-mandor="{{ json_encode([
                                            'id' => $mandor->id,
                                            'nama' => $mandor->nama,
                                            'phone' => $mandor->phone,
                                            'status' => $mandor->status,
                                        ]) }}">
                                        <td>{{ $mandors->firstItem() + $loop->index }}</td>
                                        <td>
                                            <span class="kb-orang">
                                                <span class="kb-avatar"
                                                    aria-hidden="true">{{ strtoupper(substr($mandor->nama, 0, 1)) }}</span>
                                                <span>
                                                    <span class="kb-orang__nama">{{ $mandor->nama }}</span>
                                                    <span class="kb-orang__sub">{{ $mandor->kode_mandor }}</span>
                                                </span>
                                            </span>
                                        </td>
                                        <td>{{ $mandor->phone }}</td>
                                        <td class="kb-c">
                                            <span
                                                class="kb-badge kb-badge--{{ $mandor->status }}">{{ ucfirst($mandor->status) }}</span>
                                        </td>
                                        <td class="kb-c">
                                            <span class="kb-actions">
                                                <button type="button" class="kb-icon" data-aksi="lihat-mandor"
                                                    aria-label="Lihat {{ $mandor->nama }}">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="kb-icon" data-aksi="ubah-mandor"
                                                    aria-label="Ubah {{ $mandor->nama }}">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                        <path d="m15 5 4 4" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="kb-icon kb-icon--del"
                                                    data-aksi="hapus-mandor" aria-label="Hapus {{ $mandor->nama }}">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M3 6h18" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="kb-empty">
                                            Belum ada data mandor. Klik "Tambah Mandor" untuk menambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="kb-foot">
                        <span>
                            @if ($mandors->total() > 0)
                                Menampilkan {{ $mandors->firstItem() }} - {{ $mandors->lastItem() }} dari
                                {{ $mandors->total() }} data
                            @else
                                Menampilkan 0 data
                            @endif
                        </span>

                        @if ($mandors->hasPages())
                            <nav class="kb-pager" aria-label="Halaman">
                                @if ($mandors->onFirstPage())
                                    <span class="kb-page kb-page--off" aria-hidden="true">&lsaquo;</span>
                                @else
                                    <a class="kb-page" href="{{ $mandors->previousPageUrl() }}">&lsaquo;</a>
                                @endif
                                @for ($i = $mulM; $i <= $akhM; $i++)
                                    @if ($i === $halM)
                                        <span class="kb-page kb-page--on" aria-current="page">{{ $i }}</span>
                                    @else
                                        <a class="kb-page" href="{{ $mandors->url($i) }}">{{ $i }}</a>
                                    @endif
                                @endfor
                                @if ($mandors->hasMorePages())
                                    <a class="kb-page" href="{{ $mandors->nextPageUrl() }}">&rsaquo;</a>
                                @else
                                    <span class="kb-page kb-page--off" aria-hidden="true">&rsaquo;</span>
                                @endif
                            </nav>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============ TAB 2: KELOLA AKUN ============ --}}
            <div class="kb-panel-body" data-tab-panel="akun" @if ($tabAktif !== 'akun') hidden @endif>
                <div class="kb-head">
                    <h2 class="kb-title">Kelola Akun Mandor</h2>
                    <span style="font-size:11.5px;color:#6b6b60;">Username &amp; password login mandor ke aplikasi</span>
                </div>

                <div class="kb-wrap">
                    <div class="kb-scroll">
                        <table class="kb-table">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Mandor</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Password</th>
                                    <th scope="col" class="kb-c">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mandors as $mandor)
                                    <tr
                                        data-akun="{{ json_encode([
                                            'id' => $mandor->id,
                                            'nama' => $mandor->nama,
                                            'kode' => $mandor->kode_mandor,
                                            'status' => $mandor->status,
                                            'username' => optional($mandor->user)->username,
                                            'diperbarui' => optional(optional($mandor->user)->updated_at)->translatedFormat('d M Y, H:i'),
                                        ]) }}">
                                        <td>{{ $mandors->firstItem() + $loop->index }}</td>
                                        <td>{{ $mandor->nama }}</td>
                                        <td>{{ optional($mandor->user)->username ?? '-' }}</td>
                                        <td>••••••••</td>
                                        <td class="kb-c">
                                            <span class="kb-actions">
                                                <button type="button" class="kb-icon" data-aksi="lihat-akun"
                                                    aria-label="Lihat akun {{ $mandor->nama }}">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="kb-icon" data-aksi="ubah-akun"
                                                    aria-label="Ubah akun {{ $mandor->nama }}">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                        <path d="m15 5 4 4" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="kb-icon kb-icon--del"
                                                    data-aksi="hapus-akun" aria-label="Hapus akun {{ $mandor->nama }}">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M3 6h18" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="kb-empty">Belum ada akun mandor. Tambahkan mandor lebih
                                            dulu di tab "Daftar Mandor".</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ============ TAB 3: JADWAL MANDOR ============ --}}
            <div class="kb-panel-body" data-tab-panel="jadwal" @if ($tabAktif !== 'jadwal') hidden @endif>
                <div class="kb-head">
                    <h2 class="kb-title">Jadwal Penugasan Mandor</h2>
                    <div class="kb-toolbar">
                        <button type="button" class="kb-btn" data-aksi="tambah-jadwal">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            Tambah Jadwal
                        </button>
                    </div>
                </div>

                <div class="kb-wrap">
                    <div class="kb-scroll">
                        <table class="kb-table">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Mandor</th>
                                    <th scope="col">Jumlah Pekerja</th>
                                    <th scope="col">Blok</th>
                                    <th scope="col">Afdeling</th>
                                    <th scope="col">Periode</th>
                                    <th scope="col" class="kb-c">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jadwals as $jadwal)
                                    <tr
                                        data-jadwal="{{ json_encode([
                                            'id' => $jadwal->id,
                                            'mandor_id' => $jadwal->mandor_id,
                                            'blok_id' => $jadwal->blok_id,
                                            'blok_ids' => method_exists($jadwal, 'bloks')
                                                ? $jadwal->bloks->pluck('id')->values()
                                                : array_values(array_filter([$jadwal->blok_id])),
                                            'afdeling' => optional($jadwal->blok)->afdeling,
                                            'tanggal_mulai' => optional($jadwal->tanggal_mulai)->format('Y-m-d'),
                                            'tanggal_selesai' => optional($jadwal->tanggal_selesai)->format('Y-m-d'),
                                            'keterangan' => $jadwal->keterangan,
                                        ]) }}">
                                        <td>{{ $jadwals->firstItem() + $loop->index }}</td>
                                        <td>{{ optional($jadwal->mandor)->nama }}</td>
                                        <td>
                                            {{ $jadwal->bloks->sum(function ($blok) {
                                                return $blok->pekerjas->where('status', 'aktif')->count();
                                            }) }}
                                            Orang
                                        </td>
                                        <td>
                                            @if (method_exists($jadwal, 'bloks') && $jadwal->bloks->count())
                                                {{ $jadwal->bloks->pluck('nama_blok')->implode(', ') }}
                                            @else
                                                {{ optional($jadwal->blok)->nama_blok }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (method_exists($jadwal, 'bloks') && $jadwal->bloks->count())
                                                {{ $jadwal->bloks->pluck('afdeling')->unique()->implode(', ') }}
                                            @else
                                                {{ optional($jadwal->blok)->afdeling }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional($jadwal->tanggal_mulai)->translatedFormat('d M Y') }}
                                            &ndash;
                                            {{ optional($jadwal->tanggal_selesai)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="kb-c">
                                            <span class="kb-actions">
                                                <button type="button" class="kb-icon" data-aksi="ubah-jadwal"
                                                    aria-label="Ubah jadwal">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                        <path d="m15 5 4 4" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="kb-icon kb-icon--del"
                                                    data-aksi="hapus-jadwal" aria-label="Hapus jadwal">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <path d="M3 6h18" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="kb-empty">Belum ada jadwal. Klik "Tambah Jadwal" untuk
                                            menambahkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="kb-foot">
                        <span>
                            @if ($jadwals->total() > 0)
                                Menampilkan {{ $jadwals->firstItem() }} - {{ $jadwals->lastItem() }} dari
                                {{ $jadwals->total() }} data
                            @else
                                Menampilkan 0 data
                            @endif
                        </span>
                        @if ($jadwals->hasPages())
                            <nav class="kb-pager" aria-label="Halaman jadwal">
                                @if ($jadwals->onFirstPage())
                                    <span class="kb-page kb-page--off" aria-hidden="true">&lsaquo;</span>
                                @else
                                    <a class="kb-page" href="{{ $jadwals->previousPageUrl() }}">&lsaquo;</a>
                                @endif
                                @for ($i = $mulJ; $i <= $akhJ; $i++)
                                    @if ($i === $halJ)
                                        <span class="kb-page kb-page--on" aria-current="page">{{ $i }}</span>
                                    @else
                                        <a class="kb-page" href="{{ $jadwals->url($i) }}">{{ $i }}</a>
                                    @endif
                                @endfor
                                @if ($jadwals->hasMorePages())
                                    <a class="kb-page" href="{{ $jadwals->nextPageUrl() }}">&rsaquo;</a>
                                @else
                                    <span class="kb-page kb-page--off" aria-hidden="true">&rsaquo;</span>
                                @endif
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ===================== DIALOG: TAMBAH / UBAH / LIHAT MANDOR ===================== --}}
    <dialog id="dlgMandor" class="kb kb-dlg" aria-labelledby="judulMandor">
        <div class="kb-dlg__box">
            <div class="kb-dlg__head">
                <h2 id="judulMandor" class="kb-dlg__title">Tambah Mandor</h2>
                <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <form id="formMandor" method="post" action="{{ url('admin/mandor') }}"
                data-url-dasar="{{ url('admin/mandor') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="methodMandor" disabled>
                <input type="hidden" name="_tab" value="daftar">
                <input type="hidden" name="_mode" value="{{ old('_mode', 'tambah') }}" id="modeMandor">
                <input type="hidden" name="_id" value="{{ old('_id') }}" id="idMandor">

                <div class="kb-dlg__body">
                    <div class="kb-form">
                        <div class="kb-field kb-field--full">
                            <label class="kb-label" for="fNamaMandor">Nama Mandor</label>
                            <input class="kb-input" id="fNamaMandor" name="nama" type="text" maxlength="100"
                                placeholder="Contoh: Budi Santoso" value="{{ old('nama') }}" required>
                            @error('nama')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kb-field">
                            <label class="kb-label" for="fPhoneMandor">No. HP</label>
                            <input class="kb-input" id="fPhoneMandor" name="phone" type="text" maxlength="20"
                                placeholder="0812-3456-7890" value="{{ old('phone') }}" required>
                            @error('phone')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kb-field">
                            <label class="kb-label" for="fStatusMandor">Status</label>
                            <select class="kb-select" id="fStatusMandor" name="status" required>
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                            @error('status')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <p class="kb-hint kb-field kb-field--full" id="hintAkunBaru">
                            Akun login akan dibuat otomatis (username sementara &amp; password default). Atur username &amp;
                            password final di tab "Kelola Akun". Penempatan blok/afdeling diatur di tab "Jadwal Mandor".
                        </p>
                    </div>
                </div>

                <div class="kb-dlg__actions">
                    <button type="button" class="kb-btn kb-btn--ghost" id="btnTutupMandor"
                        data-aksi="tutup">Batal</button>
                    <button type="submit" class="kb-btn" id="btnSimpanMandor">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- ===================== DIALOG: HAPUS MANDOR ===================== --}}
    <dialog id="dlgHapusMandor" class="kb kb-dlg kb-dlg--sm" aria-labelledby="judulHapusMandor">
        <div class="kb-dlg__box">
            <div class="kb-dlg__head">
                <h2 id="judulHapusMandor" class="kb-dlg__title">Hapus Mandor?</h2>
                <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <form id="formHapusMandor" method="post" action="{{ url('admin/mandor') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="_tab" value="daftar">
                <div class="kb-dlg__body">
                    <p class="kb-dlg__text">
                        Data <strong id="namaHapusMandor"></strong> beserta akun login-nya akan dihapus permanen.
                    </p>
                </div>
                <div class="kb-dlg__actions">
                    <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Batal</button>
                    <button type="submit" class="kb-btn kb-btn--danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- ===================== DIALOG: LIHAT AKUN ===================== --}}
    <dialog id="dlgLihatAkun" class="kb kb-dlg kb-dlg--sm" aria-labelledby="judulLihatAkun">
        <div class="kb-dlg__box">
            <div class="kb-dlg__head">
                <h2 id="judulLihatAkun" class="kb-dlg__title">Detail Akun Mandor</h2>
                <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <div class="kb-dlg__body">
                <div class="kb-form">
                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="laNama">Nama Mandor</label>
                        <input class="kb-input" id="laNama" type="text" disabled>
                    </div>
                    <div class="kb-field">
                        <label class="kb-label" for="laKode">Kode Mandor</label>
                        <input class="kb-input" id="laKode" type="text" disabled>
                    </div>
                    <div class="kb-field">
                        <label class="kb-label" for="laStatus">Status</label>
                        <input class="kb-input" id="laStatus" type="text" disabled>
                    </div>
                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="laUsername">Username</label>
                        <input class="kb-input" id="laUsername" type="text" disabled>
                    </div>
                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="laPassword">Password</label>
                        <input class="kb-input" id="laPassword" type="text" value="••••••••" disabled>
                        <p class="kb-hint">Password disimpan terenkripsi (hash) sehingga tidak bisa ditampilkan. Gunakan
                            tombol Ubah untuk mengatur ulang.</p>
                    </div>
                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="laUpdate">Terakhir diperbarui</label>
                        <input class="kb-input" id="laUpdate" type="text" disabled>
                    </div>
                </div>
            </div>
            <div class="kb-dlg__actions">
                <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Tutup</button>
            </div>
        </div>
    </dialog>

    {{-- ===================== DIALOG: UBAH AKUN ===================== --}}
    <dialog id="dlgAkun" class="kb kb-dlg kb-dlg--sm" aria-labelledby="judulAkun">
        <div class="kb-dlg__box">
            <div class="kb-dlg__head">
                <h2 id="judulAkun" class="kb-dlg__title">Ubah Akun Mandor</h2>
                <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <form id="formAkun" method="post" action="{{ url('admin/mandor') }}"
                data-url-dasar="{{ url('admin/mandor') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_tab" value="akun">
                <input type="hidden" name="_id" id="idAkun" value="{{ old('_id') }}">
                <div class="kb-dlg__body">
                    <div class="kb-form">
                        <div class="kb-field kb-field--full">
                            <label class="kb-label" for="namaAkunTampil">Nama Mandor</label>
                            <input class="kb-input" type="text" id="namaAkunTampil" disabled>
                        </div>
                        <div class="kb-field kb-field--full">
                            <label class="kb-label" for="fUsername">Username</label>
                            <input class="kb-input" id="fUsername" name="username" type="text" maxlength="50"
                                placeholder="username login" required>
                            @error('username')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="kb-field">
                            <label class="kb-label" for="fPassword">Password Baru</label>
                            <input class="kb-input" id="fPassword" name="password" type="password" minlength="6"
                                placeholder="Kosongkan jika tidak diubah">
                            @error('password')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="kb-field">
                            <label class="kb-label" for="fPasswordConfirm">Ulangi Password</label>
                            <input class="kb-input" id="fPasswordConfirm" name="password_confirmation" type="password"
                                minlength="6" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>
                <div class="kb-dlg__actions">
                    <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Batal</button>
                    <button type="submit" class="kb-btn">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- ===================== DIALOG: HAPUS AKUN ===================== --}}
    <dialog id="dlgHapusAkun" class="kb kb-dlg kb-dlg--sm" aria-labelledby="judulHapusAkun">
        <div class="kb-dlg__box">
            <div class="kb-dlg__head">
                <h2 id="judulHapusAkun" class="kb-dlg__title">Hapus Akun?</h2>
                <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <form id="formHapusAkun" method="post" action="{{ url('admin/mandor') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="_tab" value="akun">
                <div class="kb-dlg__body">
                    <p class="kb-dlg__text">
                        Akun login <strong id="namaHapusAkun"></strong> akan dihapus dan mandor tidak bisa login lagi. Data
                        profil mandor ikut terhapus.
                    </p>
                </div>
                <div class="kb-dlg__actions">
                    <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Batal</button>
                    <button type="submit" class="kb-btn kb-btn--danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- ===================== DIALOG: TAMBAH / UBAH JADWAL ===================== --}}
    <dialog id="dlgJadwal" class="kb kb-dlg" aria-labelledby="judulJadwal">
        <div class="kb-dlg__box">
            <div class="kb-dlg__head">
                <h2 id="judulJadwal" class="kb-dlg__title">Tambah Jadwal Mandor</h2>
                <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <form id="formJadwal" method="post" action="{{ url('admin/mandor/jadwal') }}"
                data-url-dasar="{{ url('admin/mandor/jadwal') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="methodJadwal" disabled>
                <input type="hidden" name="_tab" value="jadwal">
                <input type="hidden" name="_id" id="idJadwal">
                <div class="kb-dlg__body">
                    <div class="kb-form">
                        <div class="kb-field kb-field--full">
                            <label class="kb-label" for="fMandorJadwal">Mandor</label>
                            <select class="kb-select" id="fMandorJadwal" name="mandor_id" required>
                                <option value="">Pilih Mandor</option>
                                @foreach ($mandorJadwal as $m)
                                    <option value="{{ $m->id }}"
                                        data-sudah-jadwal="{{ in_array($m->id, $mandorSudahJadwal) ? '1' : '0' }}">
                                        {{ $m->nama }}{{ in_array($m->id, $mandorSudahJadwal) ? ' (sudah bertugas)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mandor_id')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kb-field kb-field--full">
                            <label class="kb-label" for="fAfdelingJadwal">Afdeling</label>
                            <select class="kb-select" id="fAfdelingJadwal" name="_afdeling">
                                <option value="">Pilih Afdeling</option>
                                @foreach ($daftarAfdeling as $afd)
                                    <option value="{{ $afd }}"
                                        {{ old('_afdeling') === $afd ? 'selected' : '' }}>
                                        {{ $afd }}</option>
                                @endforeach
                            </select>
                            <p class="kb-hint">Pilih afdeling dulu untuk menampilkan pilihan blok.</p>
                        </div>

                        <div class="kb-field kb-field--full">
                            <label class="kb-label">Blok yang Dikelola</label>
                            <div id="wrapBlokJadwal" class="kb-blok-wrap">
                                <span id="placeholderBlokJadwal" class="kb-blok-placeholder">Pilih afdeling dulu untuk
                                    menampilkan blok</span>
                                @foreach ($bloks as $b)
                                    @php $dipegang = $pemegangBlok->get($b->id); @endphp
                                    <label class="kb-blok-opsi opsi-blok-jadwal" data-afdeling="{{ $b->afdeling }}"
                                        data-dipegang-id="{{ $dipegang->mandor_id ?? '' }}" style="display:none;">
                                        <input type="checkbox" name="blok_ids[]" value="{{ $b->id }}"
                                            {{ in_array($b->id, old('blok_ids', [])) ? 'checked' : '' }}>
                                        <span>{{ $b->nama_blok }}{{ $dipegang ? ' (dikelola ' . $dipegang->nama . ')' : '' }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="kb-hint" id="hintBlokJadwal">Maksimal 2 blok per jadwal.</p>
                            @error('blok_ids')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                            @error('blok_ids.*')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kb-field">
                            <label class="kb-label" for="fTglMulai">Tanggal Mulai</label>
                            <input class="kb-input" id="fTglMulai" name="tanggal_mulai" type="date" required>
                            @error('tanggal_mulai')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kb-field">
                            <label class="kb-label" for="fTglSelesai">Tanggal Selesai</label>
                            <input class="kb-input" id="fTglSelesai" name="tanggal_selesai" type="date" required>
                            @error('tanggal_selesai')
                                <p class="kb-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kb-field kb-field--full">
                            <label class="kb-label" for="fKeterangan">Keterangan (opsional)</label>
                            <input class="kb-input" id="fKeterangan" name="keterangan" type="text" maxlength="255"
                                placeholder="Catatan tambahan">
                        </div>
                    </div>
                </div>
                <div class="kb-dlg__actions">
                    <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Batal</button>
                    <button type="submit" class="kb-btn">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- ===================== DIALOG: HAPUS JADWAL ===================== --}}
    <dialog id="dlgHapusJadwal" class="kb kb-dlg kb-dlg--sm" aria-labelledby="judulHapusJadwal">
        <div class="kb-dlg__box">
            <div class="kb-dlg__head">
                <h2 id="judulHapusJadwal" class="kb-dlg__title">Hapus Jadwal?</h2>
                <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <form id="formHapusJadwal" method="post" action="{{ url('admin/mandor/jadwal') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="_tab" value="jadwal">
                <div class="kb-dlg__body">
                    <p class="kb-dlg__text">Jadwal penugasan ini akan dihapus permanen.</p>
                </div>
                <div class="kb-dlg__actions">
                    <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Batal</button>
                    <button type="submit" class="kb-btn kb-btn--danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        (function() {
            // ---------- Tab switching ----------
            var tabButtons = document.querySelectorAll('.kb-tab');
            var panels = document.querySelectorAll('.kb-panel-body');


            tabButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    tabButtons.forEach(function(b) {
                        b.classList.remove('kb-tab--on');
                    });
                    btn.classList.add('kb-tab--on');
                    panels.forEach(function(p) {
                        p.hidden = p.dataset.tabPanel !== btn.dataset.tab;
                    });
                });
            });

            // ---------- Dialog: Mandor (tambah/ubah/lihat) ----------
            var fMandorJadwal = document.getElementById('fMandorJadwal');
            var opsiMandorJadwal = Array.prototype.slice.call(fMandorJadwal.options);
            var dlgMandor = document.getElementById('dlgMandor');
            var formMandor = document.getElementById('formMandor');
            var judulMandor = document.getElementById('judulMandor');
            var fTglMulai = document.getElementById('fTglMulai');
            var fTglSelesai = document.getElementById('fTglSelesai');
            var methodMandor = document.getElementById('methodMandor');
            var modeMandor = document.getElementById('modeMandor');
            var idMandor = document.getElementById('idMandor');
            var btnSimpanMandor = document.getElementById('btnSimpanMandor');
            var btnTutupMandor = document.getElementById('btnTutupMandor');
            var hintAkunBaru = document.getElementById('hintAkunBaru');
            var urlMandor = formMandor.dataset.urlDasar;
            var fieldsMandor = ['nama', 'phone', 'status'];
            var judulModeMandor = {
                tambah: 'Tambah Mandor',
                ubah: 'Ubah Mandor',
                lihat: 'Detail Mandor'
            };

            function terapkanFilterMandorJadwal(mandorSaatIni) {
                opsiMandorJadwal.forEach(function(opt) {
                    if (!opt.value) return; // biarkan opsi placeholder "Pilih Mandor"
                    var sudah = opt.dataset.sudahJadwal === '1';
                    var iniSendiri = mandorSaatIni != null && String(opt.value) === String(mandorSaatIni);
                    opt.disabled = sudah && !iniSendiri;
                });
            }

            function bukaFormMandor(mode, data, pertahankan) {
                var lihat = mode === 'lihat';
                judulMandor.textContent = judulModeMandor[mode];
                modeMandor.value = mode;
                idMandor.value = data && data.id ? data.id : '';
                methodMandor.disabled = mode !== 'ubah';
                formMandor.action = mode === 'ubah' ? urlMandor + '/' + data.id : urlMandor;
                hintAkunBaru.style.display = mode === 'tambah' ? 'block' : 'none';

                if (!pertahankan) {
                    formMandor.querySelectorAll('.kb-err').forEach(function(el) {
                        el.remove();
                    });
                    fieldsMandor.forEach(function(f) {
                        var nilai = data ? data[f] : (f === 'status' ? 'aktif' : '');
                        formMandor.elements[f].value = nilai == null ? '' : nilai;
                    });
                }

                fieldsMandor.forEach(function(f) {
                    formMandor.elements[f].disabled = lihat;
                });
                btnSimpanMandor.hidden = lihat;
                btnTutupMandor.textContent = lihat ? 'Tutup' : 'Batal';
                dlgMandor.showModal();
            }

            function hariIniFormatISO() {
                var d = new Date();
                var bulan = String(d.getMonth() + 1).padStart(2, '0');
                var tanggal = String(d.getDate()).padStart(2, '0');
                return d.getFullYear() + '-' + bulan + '-' + tanggal;
            }

            // Tanggal mulai tidak boleh sebelum hari ini
            fTglMulai.min = hariIniFormatISO();

            // Tanggal selesai otomatis ikut dibatasi minimal = tanggal mulai yang dipilih
            fTglMulai.addEventListener('change', function() {
                fTglSelesai.min = this.value;
                // Kalau tanggal selesai yang sudah terisi jadi lebih awal dari tanggal mulai baru, kosongkan
                if (fTglSelesai.value && fTglSelesai.value < this.value) {
                    fTglSelesai.value = '';
                }
            });

            // ---------- Dialog: Hapus Mandor ----------
            var dlgHapusMandor = document.getElementById('dlgHapusMandor');
            var formHapusMandor = document.getElementById('formHapusMandor');
            var namaHapusMandor = document.getElementById('namaHapusMandor');

            function bukaHapusMandor(data) {
                formHapusMandor.action = urlMandor + '/' + data.id;
                namaHapusMandor.textContent = data.nama;
                dlgHapusMandor.showModal();
            }

            // ---------- Dialog: Akun (ubah) ----------
            var dlgAkun = document.getElementById('dlgAkun');
            var formAkun = document.getElementById('formAkun');
            var urlAkun = formAkun.dataset.urlDasar;

            // pertahankan = true -> pesan error dari server tidak dihapus (dipakai saat buka ulang setelah validasi gagal)
            function bukaAkun(data, pertahankan) {
                formAkun.action = urlAkun + '/' + data.id + '/akun';
                document.getElementById('idAkun').value = data.id || '';
                document.getElementById('namaAkunTampil').value = data.nama || '';
                formAkun.elements['username'].value = data.username || '';
                formAkun.elements['password'].value = '';
                formAkun.elements['password_confirmation'].value = '';
                if (!pertahankan) {
                    formAkun.querySelectorAll('.kb-err').forEach(function(el) {
                        el.remove();
                    });
                }
                dlgAkun.showModal();
            }

            // Cari data akun di tabel berdasarkan id mandor
            function cariAkun(id) {
                var hasil = null;
                document.querySelectorAll('tr[data-akun]').forEach(function(tr) {
                    var d = JSON.parse(tr.dataset.akun);
                    if (String(d.id) === String(id)) hasil = d;
                });
                return hasil;
            }

            // ---------- Dialog: Lihat Akun ----------
            var dlgLihatAkun = document.getElementById('dlgLihatAkun');

            function bukaLihatAkun(d) {
                document.getElementById('laNama').value = d.nama || '-';
                document.getElementById('laKode').value = d.kode || '-';
                document.getElementById('laStatus').value = d.status ? d.status.charAt(0).toUpperCase() + d.status
                    .slice(1) : '-';
                document.getElementById('laUsername').value = d.username || '-';
                document.getElementById('laUpdate').value = d.diperbarui || '-';
                dlgLihatAkun.showModal();
            }

            // ---------- Dialog: Hapus Akun ----------
            var dlgHapusAkun = document.getElementById('dlgHapusAkun');
            var formHapusAkun = document.getElementById('formHapusAkun');
            var namaHapusAkun = document.getElementById('namaHapusAkun');

            function bukaHapusAkun(data) {
                formHapusAkun.action = urlAkun + '/' + data.id + '/akun';
                namaHapusAkun.textContent = data.nama;
                dlgHapusAkun.showModal();
            }

            // ---------- Dialog: Jadwal ----------
            var dlgJadwal = document.getElementById('dlgJadwal');
            var formJadwal = document.getElementById('formJadwal');
            var judulJadwal = document.getElementById('judulJadwal');
            var methodJadwal = document.getElementById('methodJadwal');
            var idJadwal = document.getElementById('idJadwal');
            var urlJadwal = formJadwal.dataset.urlDasar;
            var fieldsJadwal = ['mandor_id', 'tanggal_mulai', 'tanggal_selesai', 'keterangan'];

            // ---------- Blok jadwal: tampil sesuai afdeling, boleh pilih maks 2 ----------
            var MAKS_BLOK = 2;
            var fAfdelingJadwal = document.getElementById('fAfdelingJadwal');
            var wrapBlokJadwal = document.getElementById('wrapBlokJadwal');
            var placeholderBlokJadwal = document.getElementById('placeholderBlokJadwal');
            var opsiBlokJadwal = Array.prototype.slice.call(document.querySelectorAll('.opsi-blok-jadwal'));

            function cbBlokJadwal() {
                return opsiBlokJadwal.map(function(l) {
                    return l.querySelector('input');
                });
            }

            function terapkanBatasBlok() {
                var kotak = cbBlokJadwal();
                var jumlah = kotak.filter(function(cb) {
                    return cb.checked;
                }).length;
                var mandorTerpilih = fMandorJadwal.value;

                kotak.forEach(function(cb) {
                    var label = cb.closest('.opsi-blok-jadwal');
                    var tampil = label.style.display !== 'none';
                    var dipegangId = label.dataset.dipegangId || '';

                    // Dipegang mandor LAIN kalau ada id-nya dan bukan mandor yang sedang dipilih di form.
                    // Kalau lagi edit jadwal Ahmad sendiri, blok milik Ahmad tidak ikut ke-disable.
                    var dipegangOrangLain = dipegangId !== '' && String(dipegangId) !== String(mandorTerpilih);

                    cb.disabled = !tampil || dipegangOrangLain || (!cb.checked && jumlah >= MAKS_BLOK);

                    // Jaga-jaga: kalau ternyata sempat kecentang padahal dipegang orang lain, lepas centangnya.
                    if (dipegangOrangLain && cb.checked) {
                        cb.checked = false;
                    }
                });
            }

            function tampilkanBlokJadwal(afdeling, pertahankanCentang) {
                var adaYangTampil = false;
                opsiBlokJadwal.forEach(function(label) {
                    var cocok = !!afdeling && label.dataset.afdeling === afdeling;
                    label.style.display = cocok ? 'flex' : 'none';
                    if (cocok) adaYangTampil = true;
                    if (!cocok && !pertahankanCentang) {
                        label.querySelector('input').checked = false;
                    }
                });
                placeholderBlokJadwal.style.display = adaYangTampil ? 'none' : 'block';
                terapkanBatasBlok();
            }

            fAfdelingJadwal.addEventListener('change', function() {
                tampilkanBlokJadwal(this.value, false);
            });

            // TAMBAHKAN INI: begitu mandor dipilih/diganti, status disable checkbox blok dihitung ulang
            fMandorJadwal.addEventListener('change', terapkanBatasBlok);

            wrapBlokJadwal.addEventListener('change', function(e) {
                if (e.target.name === 'blok_ids[]') terapkanBatasBlok();
            });

            formJadwal.addEventListener('submit', function(e) {
                var adaDipilih = cbBlokJadwal().some(function(cb) {
                    return cb.checked;
                });
                if (!adaDipilih) {
                    e.preventDefault();
                    fAfdelingJadwal.focus();
                    alert('Pilih minimal 1 blok (maksimal ' + MAKS_BLOK + ').');
                }
            });

            function bukaFormJadwal(mode, data, pertahankan) {
                judulJadwal.textContent = mode === 'ubah' ? 'Ubah Jadwal Mandor' : 'Tambah Jadwal Mandor';
                idJadwal.value = data && data.id ? data.id : '';
                methodJadwal.disabled = mode !== 'ubah';
                formJadwal.action = mode === 'ubah' ? urlJadwal + '/' + data.id : urlJadwal;

                if (!pertahankan) {
                    formJadwal.querySelectorAll('.kb-err').forEach(function(el) {
                        el.remove();
                    });
                    fieldsJadwal.forEach(function(f) {
                        var nilai = data ? data[f] : '';
                        formJadwal.elements[f].value = nilai == null ? '' : nilai;
                    });
                }

                var blokTerpilih = (data && data.blok_ids ? data.blok_ids : []).map(String);
                var afd = data && data.afdeling ? data.afdeling : '';

                // Kalau afdeling belum diketahui, ambil dari blok pertama yang terpilih
                if (!afd && blokTerpilih.length) {
                    opsiBlokJadwal.forEach(function(label) {
                        var cb = label.querySelector('input');
                        if (!afd && blokTerpilih.indexOf(cb.value) !== -1) afd = label.dataset.afdeling;
                    });
                }

                fAfdelingJadwal.value = afd;
                opsiBlokJadwal.forEach(function(label) {
                    var cb = label.querySelector('input');
                    cb.checked = blokTerpilih.indexOf(cb.value) !== -1;
                });
                tampilkanBlokJadwal(afd, true);

                // Tambahkan baris ini:
                terapkanFilterMandorJadwal(mode === 'ubah' && data ? data.mandor_id : null);

                dlgJadwal.showModal();
            }

            var dlgHapusJadwal = document.getElementById('dlgHapusJadwal');
            var formHapusJadwal = document.getElementById('formHapusJadwal');

            function bukaHapusJadwal(data) {
                formHapusJadwal.action = urlJadwal + '/' + data.id;
                dlgHapusJadwal.showModal();
            }

            // ---------- Delegasi klik tombol aksi ----------
            document.addEventListener('click', function(e) {
                var tombol = e.target.closest('[data-aksi]');
                if (!tombol) return;
                var aksi = tombol.dataset.aksi;

                if (aksi === 'tutup') {
                    tombol.closest('dialog').close();
                    return;
                }
                if (aksi === 'tambah-mandor') {
                    bukaFormMandor('tambah', null, false);
                    return;
                }
                if (aksi === 'tambah-jadwal') {
                    bukaFormJadwal('tambah', null, false);
                    return;
                }

                var baris = tombol.closest('tr');
                if (!baris) return;

                if (aksi === 'lihat-mandor' || aksi === 'ubah-mandor' || aksi === 'hapus-mandor') {
                    var dMandor = JSON.parse(baris.dataset.mandor);
                    if (aksi === 'hapus-mandor') bukaHapusMandor(dMandor);
                    else bukaFormMandor(aksi === 'lihat-mandor' ? 'lihat' : 'ubah', dMandor, false);
                    return;
                }
                if (aksi === 'lihat-akun' || aksi === 'ubah-akun' || aksi === 'hapus-akun') {
                    var dAkun = JSON.parse(baris.dataset.akun);
                    if (aksi === 'lihat-akun') bukaLihatAkun(dAkun);
                    else if (aksi === 'hapus-akun') bukaHapusAkun(dAkun);
                    else bukaAkun(dAkun, false);
                    return;
                }
                if (aksi === 'ubah-jadwal' || aksi === 'hapus-jadwal') {
                    var dJadwal = JSON.parse(baris.dataset.jadwal);
                    if (aksi === 'hapus-jadwal') bukaHapusJadwal(dJadwal);
                    else bukaFormJadwal('ubah', dJadwal, false);
                    return;
                }
            });

            // Klik area gelap di luar kotak dialog = tutup
            [dlgMandor, dlgHapusMandor, dlgAkun, dlgLihatAkun, dlgHapusAkun, dlgJadwal, dlgHapusJadwal].forEach(
                function(d) {
                    d.addEventListener('click', function(e) {
                        if (e.target === d) d.close();
                    });
                });

            // Jika validasi gagal, buka lagi dialog + tab yang sesuai
            @if ($errors->any())
                var tabError = @json(old('_tab', 'daftar'));
                tabButtons.forEach(function(b) {
                    b.classList.toggle('kb-tab--on', b.dataset.tab === tabError);
                });
                panels.forEach(function(p) {
                    p.hidden = p.dataset.tabPanel !== tabError;
                });

                @if (old('_tab') === 'akun')
                    var akunLama = cariAkun(@json(old('_id'))) || {};
                    bukaAkun({
                        id: @json(old('_id')),
                        nama: akunLama.nama || '',
                        username: @json(old('username'))
                    }, true);
                @elseif (old('_tab') === 'jadwal')
                    bukaFormJadwal(@json(old('_id') ? 'ubah' : 'tambah'), {
                        id: @json(old('_id')),
                        mandor_id: @json(old('mandor_id')),
                        afdeling: @json(old('_afdeling')),
                        blok_ids: @json(old('blok_ids', [])),
                        tanggal_mulai: @json(old('tanggal_mulai')),
                        tanggal_selesai: @json(old('tanggal_selesai')),
                        keterangan: @json(old('keterangan')),
                    }, true);
                @else
                    bukaFormMandor(@json(old('_mode', 'tambah')), {
                        id: @json(old('_id'))
                    }, true);
                @endif
            @endif
        })();
    </script>
@endsection
