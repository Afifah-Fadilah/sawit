{{-- resources/views/admin/kelolablok.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Kelola Blok')

@section('content')
@php
    $stats = [
        [
            'label' => 'Total Blok',
            'value' => number_format($totalBlok, 0, ',', '.'),
            'unit'  => 'Blok',
            'icon'  => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/>',
        ],
        [
            'label' => 'Total Luas',
            'value' => number_format($totalLuas, 2, ',', '.'),
            'unit'  => 'Hektar',
            'icon'  => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        ],
        [
            'label' => 'Total Afdeling',
            'value' => number_format($totalAfdeling, 0, ',', '.'),
            'unit'  => 'Afdeling',
            'icon'  => '<polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" x2="9" y1="3" y2="18"/><line x1="15" x2="15" y1="6" y2="21"/>',
        ],
    ];

    $sedangCari = request()->filled('q') || request()->filled('afdeling');

    // Nomor halaman yang ditampilkan: halaman sekarang +/- 2
    $halaman  = $bloks->currentPage();
    $terakhir = $bloks->lastPage();
    $mulai    = max(1, $halaman - 2);
    $akhir    = min($terakhir, $halaman + 2);
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
    /* Semua elemen memakai border-box supaya lebar input tidak melebihi kolomnya */
    .kb, .kb *, .kb *::before, .kb *::after { box-sizing: border-box; }

    .kb-card {
        border-radius: 20px;
        background: var(--kb-card);
        box-shadow: 0 6px 16px rgba(95, 111, 82, .16);
    }

    /* Notifikasi */
    .kb-flash {
        margin: 0 0 18px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #e3f2d4;
        color: #23652d;
        font-size: 13px;
        font-weight: 500;
    }

    /* Kartu statistik */
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
    .kb-stat__label { margin: 0; font-size: 11.5px; font-weight: 600; }
    .kb-stat__value { margin: 4px 0 0; font-size: 32px; font-weight: 700; line-height: 1.2; }
    .kb-stat__unit  { margin: 2px 0 0; font-size: 11.5px; font-weight: 500; }

    /* Panel daftar */
    .kb-panel { padding: 18px 16px 16px; }
    .kb-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .kb-title { margin: 0; color: var(--kb-deep); font-size: 17px; font-weight: 600; }
    .kb-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
    .kb-search { position: relative; }
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
    .kb-input { width: 230px; padding: 0 12px 0 34px; }
    .kb-select {
        min-width: 160px;
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
    .kb-btn:hover { background: #4d5b42; }
    .kb-btn[hidden] { display: none; }
    .kb-btn--ghost { border: 1px solid #cfcdb4; background: #fff; color: var(--kb-ink); }
    .kb-btn--ghost:hover { background: #f3f2e6; }
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

    /* Tabel */
    .kb-wrap { border-radius: 12px; background: #fff; overflow: hidden; }
    .kb-scroll { overflow-x: auto; }
    .kb-table { width: 100%; min-width: 740px; border-collapse: collapse; font-size: 12px; }
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

    .kb-badge {
        display: inline-block;
        min-width: 54px;
        padding: 3px 12px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 600;
        text-align: center;
    }
    .kb-badge--aktif { background: #e3f4d6; color: #23652d; }
    .kb-badge--nonaktif { background: #eeeeea; color: #5b5b52; }

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
        transition: background-color .15s ease;
    }
    .kb-icon:hover { background: #f1f0e6; }
    .kb-icon--del { border-color: #efc5bf; background: #fdf1ef; color: #b3382c; }
    .kb-icon--del:hover { background: #fadfdb; }

    /* Footer tabel + pagination */
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

    .kb-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px 22px; }
    .kb-field { min-width: 0; }
    .kb-field--full { grid-column: 1 / -1; }
    .kb-label { display: block; margin-bottom: 7px; font-size: 12.5px; font-weight: 500; }

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
    .kb-field .kb-input::placeholder { color: #a3a293; }
    .kb-field .kb-input:focus-visible,
    .kb-field .kb-select:focus-visible {
        outline: none;
        border-color: var(--kb-deep);
        box-shadow: 0 0 0 3px rgba(95, 111, 82, .2);
    }
    .kb-field .kb-select { padding-right: 38px; }
    .kb-field .kb-input:disabled,
    .kb-field .kb-select:disabled {
        background-color: #f3f2e6;
        color: #3f4a35;
        -webkit-text-fill-color: #3f4a35;
        opacity: 1;
    }
    .kb-err { margin: 6px 0 0; color: #a33a2f; font-size: 11.5px; }

    .kb-dlg__actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 28px;
        border-top: 1px solid #e8e6d0;
        background: #f6f5e6;
    }
    .kb-dlg__actions .kb-btn { height: 40px; padding: 0 22px; }

    @media (max-width: 520px) {
        .kb-form { grid-template-columns: minmax(0, 1fr); gap: 16px; }
        .kb-dlg__head { padding: 18px 20px 14px; }
        .kb-dlg__body { padding: 20px; }
        .kb-dlg__actions { padding: 14px 20px; }
    }
</style>

<div class="kb">
    @if (session('sukses'))
        <p class="kb-flash" role="status">{{ session('sukses') }}</p>
    @endif

    {{-- Kartu statistik --}}
    <section class="kb-stats" aria-label="Ringkasan blok">
        @foreach ($stats as $stat)
            <article class="kb-card kb-stat">
                <span class="kb-stat__icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        {!! $stat['icon'] !!}
                    </svg>
                </span>
                <h2 class="kb-stat__label">{{ $stat['label'] }}</h2>
                <p class="kb-stat__value">{{ $stat['value'] }}</p>
                <p class="kb-stat__unit">{{ $stat['unit'] }}</p>
            </article>
        @endforeach
    </section>

    {{-- Daftar blok --}}
    <section class="kb-card kb-panel" aria-labelledby="judulDaftar">
        <div class="kb-head">
            <h2 id="judulDaftar" class="kb-title">Daftar Blok</h2>

            <div class="kb-toolbar">
                <form method="get" action="{{ url('admin/blok') }}" class="kb-toolbar" role="search">
                    <div class="kb-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}" class="kb-input"
                               placeholder="Cari blok, afdeling..." aria-label="Cari blok atau afdeling">
                    </div>

                    <select name="afdeling" class="kb-select" aria-label="Filter afdeling" onchange="this.form.submit()">
                        <option value="">Semua Afdeling</option>
                        @foreach ($daftarAfdeling as $afd)
                            <option value="{{ $afd }}" {{ request('afdeling') === $afd ? 'selected' : '' }}>{{ $afd }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="kb-sr">Cari</button>
                </form>

                <button type="button" class="kb-btn" data-aksi="tambah">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                        <path d="M5 12h14"/><path d="M12 5v14"/>
                    </svg>
                    Tambah Blok
                </button>
            </div>
        </div>

        <div class="kb-wrap">
            <div class="kb-scroll">
                <table class="kb-table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama Blok</th>
                            <th scope="col">Afdeling</th>
                            <th scope="col">Luas (Ha)</th>
                            <th scope="col">Tahun Tanam</th>
                            <th scope="col" class="kb-c">Status</th>
                            <th scope="col" class="kb-c">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bloks as $blok)
                            <tr data-blok="{{ json_encode([
                                'id'          => $blok->id,
                                'nama_blok'   => $blok->nama_blok,
                                'afdeling'    => $blok->afdeling,
                                'luas'        => $blok->luas,
                                'tahun_tanam' => $blok->tahun_tanam,
                                'status'      => $blok->status,
                            ]) }}">
                                <td>{{ $bloks->firstItem() + $loop->index }}</td>
                                <td>{{ $blok->nama_blok }}</td>
                                <td>{{ $blok->afdeling }}</td>
                                <td>{{ number_format((float) $blok->luas, 2, ',', '.') }}</td>
                                <td>{{ $blok->tahun_tanam }}</td>
                                <td class="kb-c">
                                    <span class="kb-badge kb-badge--{{ $blok->status }}">{{ ucfirst($blok->status) }}</span>
                                </td>
                                <td class="kb-c">
                                    <span class="kb-actions">
                                        <button type="button" class="kb-icon" data-aksi="lihat" aria-label="Lihat {{ $blok->nama_blok }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="kb-icon" data-aksi="ubah" aria-label="Ubah {{ $blok->nama_blok }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="kb-icon kb-icon--del" data-aksi="hapus" aria-label="Hapus {{ $blok->nama_blok }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="kb-empty">
                                    @if ($sedangCari)
                                        Tidak ada blok yang cocok dengan pencarian atau filter.
                                    @else
                                        Belum ada data blok. Klik "Tambah Blok" untuk menambahkan.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="kb-foot">
                <span>
                    @if ($bloks->total() > 0)
                        Menampilkan {{ $bloks->firstItem() }} - {{ $bloks->lastItem() }} dari {{ $bloks->total() }} data
                    @else
                        Menampilkan 0 data
                    @endif
                </span>

                @if ($bloks->hasPages())
                    <nav class="kb-pager" aria-label="Halaman">
                        @if ($bloks->onFirstPage())
                            <span class="kb-page kb-page--off" aria-hidden="true">&lsaquo;</span>
                        @else
                            <a class="kb-page" href="{{ $bloks->previousPageUrl() }}" aria-label="Halaman sebelumnya">&lsaquo;</a>
                        @endif

                        @for ($i = $mulai; $i <= $akhir; $i++)
                            @if ($i === $halaman)
                                <span class="kb-page kb-page--on" aria-current="page">{{ $i }}</span>
                            @else
                                <a class="kb-page" href="{{ $bloks->url($i) }}" aria-label="Halaman {{ $i }}">{{ $i }}</a>
                            @endif
                        @endfor

                        @if ($bloks->hasMorePages())
                            <a class="kb-page" href="{{ $bloks->nextPageUrl() }}" aria-label="Halaman berikutnya">&rsaquo;</a>
                        @else
                            <span class="kb-page kb-page--off" aria-hidden="true">&rsaquo;</span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </section>
</div>

{{-- Dialog tambah / ubah / lihat --}}
<dialog id="dlgBlok" class="kb kb-dlg" aria-labelledby="judulBlok">
    <div class="kb-dlg__box">
        <div class="kb-dlg__head">
            <h2 id="judulBlok" class="kb-dlg__title">Tambah Blok</h2>
            <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <form id="formBlok" method="post" action="{{ url('admin/blok') }}" data-url-dasar="{{ url('admin/blok') }}">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="methodBlok" disabled>
            <input type="hidden" name="_mode" value="{{ old('_mode', 'tambah') }}" id="modeBlok">
            <input type="hidden" name="_id" value="{{ old('_id') }}" id="idBlok">

            <div class="kb-dlg__body">
                <div class="kb-form">
                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="fNamaBlok">Nama Blok</label>
                        <input class="kb-input" id="fNamaBlok" name="nama_blok" type="text" maxlength="100" placeholder="Contoh: Blok 1" value="{{ old('nama_blok') }}" required>
                        @error('nama_blok') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field">
                        <label class="kb-label" for="fAfdeling">Afdeling</label>
                        <input class="kb-input" id="fAfdeling" name="afdeling" type="text" maxlength="100" list="daftarAfdeling" placeholder="Contoh: Afdeling I" value="{{ old('afdeling') }}" required>
                        <datalist id="daftarAfdeling">
                            @foreach ($daftarAfdeling as $afd)
                                <option value="{{ $afd }}"></option>
                            @endforeach
                        </datalist>
                        @error('afdeling') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field">
                        <label class="kb-label" for="fLuas">Luas (Ha)</label>
                        <input class="kb-input" id="fLuas" name="luas" type="number" step="0.01" min="0" placeholder="Contoh: 18.20" value="{{ old('luas') }}" required>
                        @error('luas') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field">
                        <label class="kb-label" for="fTahun">Tahun Tanam</label>
                        <input class="kb-input" id="fTahun" name="tahun_tanam" type="number" min="1900" max="{{ date('Y') }}" placeholder="Contoh: 2018" value="{{ old('tahun_tanam') }}" required>
                        @error('tahun_tanam') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field">
                        <label class="kb-label" for="fStatus">Status</label>
                        <select class="kb-select" id="fStatus" name="status" required>
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="kb-dlg__actions">
                <button type="button" class="kb-btn kb-btn--ghost" id="btnTutupBlok" data-aksi="tutup">Batal</button>
                <button type="submit" class="kb-btn" id="btnSimpanBlok">Simpan</button>
            </div>
        </form>
    </div>
</dialog>

{{-- Dialog konfirmasi hapus --}}
<dialog id="dlgHapus" class="kb kb-dlg kb-dlg--sm" aria-labelledby="judulHapus">
    <div class="kb-dlg__box">
        <div class="kb-dlg__head">
            <h2 id="judulHapus" class="kb-dlg__title">Hapus Blok?</h2>
            <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <form id="formHapus" method="post" action="{{ url('admin/blok') }}">
            @csrf
            @method('DELETE')

            <div class="kb-dlg__body">
                <p class="kb-dlg__text">
                    Data <strong id="namaHapus"></strong> akan dihapus permanen dan tidak bisa dikembalikan.
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
        var dlgBlok = document.getElementById('dlgBlok');
        var dlgHapus = document.getElementById('dlgHapus');
        var form = document.getElementById('formBlok');
        var formHapus = document.getElementById('formHapus');
        var judul = document.getElementById('judulBlok');
        var methodInput = document.getElementById('methodBlok');
        var modeInput = document.getElementById('modeBlok');
        var idInput = document.getElementById('idBlok');
        var btnSimpan = document.getElementById('btnSimpanBlok');
        var btnTutup = document.getElementById('btnTutupBlok');
        var namaHapus = document.getElementById('namaHapus');
        var urlDasar = form.dataset.urlDasar;
        var fields = ['nama_blok', 'afdeling', 'luas', 'tahun_tanam', 'status'];
        var judulMode = { tambah: 'Tambah Blok', ubah: 'Ubah Blok', lihat: 'Detail Blok' };

        // mode: 'tambah' | 'ubah' | 'lihat'. pertahankan = true saat dibuka ulang karena error validasi.
        function bukaForm(mode, data, pertahankan) {
            var lihat = mode === 'lihat';

            judul.textContent = judulMode[mode];
            modeInput.value = mode;
            idInput.value = data && data.id ? data.id : '';
            methodInput.disabled = mode !== 'ubah';
            form.action = mode === 'ubah' ? urlDasar + '/' + data.id : urlDasar;

            if (!pertahankan) {
                form.querySelectorAll('.kb-err').forEach(function (el) { el.remove(); });
                fields.forEach(function (f) {
                    var nilai = data ? data[f] : (f === 'status' ? 'aktif' : '');
                    form.elements[f].value = nilai == null ? '' : nilai;
                });
            }

            fields.forEach(function (f) { form.elements[f].disabled = lihat; });
            btnSimpan.hidden = lihat;
            btnTutup.textContent = lihat ? 'Tutup' : 'Batal';
            dlgBlok.showModal();
        }

        function bukaHapus(data) {
            formHapus.action = urlDasar + '/' + data.id;
            namaHapus.textContent = data.nama_blok;
            dlgHapus.showModal();
        }

        document.addEventListener('click', function (e) {
            var tombol = e.target.closest('[data-aksi]');
            if (!tombol) return;

            var aksi = tombol.dataset.aksi;
            if (aksi === 'tutup') {
                tombol.closest('dialog').close();
                return;
            }
            if (aksi === 'tambah') {
                bukaForm('tambah', null, false);
                return;
            }

            var baris = tombol.closest('tr');
            var data = baris ? JSON.parse(baris.dataset.blok) : null;
            if (!data) return;

            if (aksi === 'hapus') bukaHapus(data);
            else bukaForm(aksi, data, false);
        });

        // Klik area gelap di luar kotak dialog = tutup
        [dlgBlok, dlgHapus].forEach(function (d) {
            d.addEventListener('click', function (e) {
                if (e.target === d) d.close();
            });
        });

        // Jika validasi gagal, buka lagi form dengan isian sebelumnya + pesan error
        @if ($errors->any())
            bukaForm(@json(old('_mode', 'tambah')), { id: @json(old('_id')) }, true);
        @endif
    })();
</script>
@endsection