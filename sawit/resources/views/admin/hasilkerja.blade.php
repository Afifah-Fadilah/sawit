{{-- resources/views/admin/hasilkerja.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Hasil Kerja')

@section('content')
@php
    $stats = [
        [
            'label' => 'Total Hasil Kerja',
            'value' => number_format($totalHasilKerja, 0, ',', '.'),
            'unit'  => 'Data',
            'icon'  => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>',
        ],
        [
            'label' => 'Total Produksi',
            'value' => number_format($totalProduksi, 0, ',', '.'),
            'unit'  => $satuanProduksi,
            'icon'  => '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>',
        ],
        [
            'label' => 'Total Pekerja Aktif',
            'value' => number_format($totalPekerjaAktif, 0, ',', '.'),
            'unit'  => 'Orang',
            'icon'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        ],
    ];

    $pesanSukses = session('sukses');

    $hal  = $hasilKerjas->currentPage();
    $last = $hasilKerjas->lastPage();
    $mul  = max(1, $hal - 2);
    $akh  = min($last, $hal + 2);
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
    .kb-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }

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
    .kb-filterbar .kb-input--date { width: 170px; flex: 0 0 auto; }
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
        transition: background-color .15s ease;
        flex: 0 0 auto;
        white-space: nowrap;
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

    .kb-wrap { border-radius: 12px; background: #fff; overflow: hidden; }
    .kb-scroll { overflow-x: auto; }
    .kb-table { width: 100%; min-width: 860px; border-collapse: collapse; font-size: 12px; }
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

    .kb-jenis { display: inline-flex; align-items: center; gap: 7px; }
    .kb-jenis__dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .kb-sub { display: block; margin-top: 2px; font-size: 10px; color: #8a8a7a; font-weight: 500; }

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
    .kb-jumlah-wrap { display: flex; align-items: center; gap: 10px; }
    .kb-jumlah-wrap .kb-input { flex: 1 1 auto; }
    .kb-satuan-tampil {
        flex: 0 0 auto;
        font-size: 12px;
        font-weight: 600;
        color: var(--kb-deep);
        background: #eef0e4;
        border-radius: 8px;
        padding: 0 12px;
        height: 44px;
        display: grid;
        place-items: center;
        min-width: 64px;
    }
    .kb-hint { margin: 6px 0 0; color: #6b6b60; font-size: 11px; }
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
        .kb-filterbar .kb-input--date { width: 100%; }
    }
</style>

<div class="kb">
    @if ($pesanSukses)
        <p class="kb-flash" role="status">{{ $pesanSukses }}</p>
    @endif

    {{-- Kartu statistik --}}
    <section class="kb-stats" aria-label="Ringkasan hasil kerja">
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

    <section class="kb-card kb-panel">
        <div class="kb-head">
            <h2 class="kb-title">Daftar Hasil Kerja</h2>
            <button type="button" class="kb-btn" data-aksi="tambah-hasil">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Tambah Hasil Kerja
            </button>
        </div>

        {{-- Filter --}}
        <form method="get" action="{{ url('admin/hasil-kerja') }}" class="kb-filterbar" role="search">
            <input class="kb-input kb-input--date" type="date" name="tanggal" value="{{ request('tanggal') }}"
                   aria-label="Filter tanggal" onchange="this.form.submit()">

            <select name="blok" class="kb-select" aria-label="Filter blok" onchange="this.form.submit()">
                <option value="">Semua Blok</option>
                @foreach ($bloks as $b)
                    <option value="{{ $b->id }}" {{ (string) request('blok') === (string) $b->id ? 'selected' : '' }}>{{ $b->nama_blok }}</option>
                @endforeach
            </select>

            <select name="jenis" class="kb-select" aria-label="Filter jenis pekerjaan" onchange="this.form.submit()">
                <option value="">Semua Jenis Pekerjaan</option>
                @foreach ($jenisPekerjaans as $jp)
                    <option value="{{ $jp->id }}" {{ (string) request('jenis') === (string) $jp->id ? 'selected' : '' }}>{{ $jp->jenis }}</option>
                @endforeach
            </select>

            <div class="kb-search">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="search" name="q" value="{{ request('q') }}" class="kb-input"
                       placeholder="Cari nama pekerja atau mandor..." aria-label="Cari hasil kerja">
            </div>

            <button type="submit" class="kb-sr">Cari</button>

            @if (request()->anyFilled(['tanggal', 'blok', 'jenis', 'q']))
                <a href="{{ url('admin/hasil-kerja') }}" class="kb-btn kb-btn--ghost">
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
                            <th scope="col">Blok</th>
                            <th scope="col">Jenis Pekerjaan</th>
                            <th scope="col">Pekerja</th>
                            <th scope="col">Mandor</th>
                            <th scope="col">Hasil</th>
                            <th scope="col" class="kb-c">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hasilKerjas as $h)
                            <tr data-hasil="{{ json_encode([
                                'id'                 => $h->id,
                                'pekerja_id'         => $h->pekerja_id,
                                'blok_id'            => $h->blok_id,
                                'jenis_pekerjaan_id' => $h->jenis_pekerjaan_id,
                                'mandor_id'          => $h->mandor_id,
                                'tanggal'            => optional($h->tanggal)->format('Y-m-d'),
                                'jumlah'             => $h->jumlah,
                                'keterangan'         => $h->keterangan,
                                'pekerja_nama'       => optional($h->pekerja)->nama,
                                'blok_nama'          => optional($h->blok)->nama_blok,
                                'jenis_nama'         => optional($h->jenisPekerjaan)->jenis,
                                'satuan'             => optional($h->jenisPekerjaan)->satuan,
                                'mandor_nama'        => optional($h->mandor)->nama,
                            ]) }}">
                                <td>{{ $hasilKerjas->firstItem() + $loop->index }}</td>
                                <td>{{ optional($h->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ optional($h->blok)->nama_blok ?? '-' }}</td>
                                <td>
                                    <span class="kb-jenis">
                                        <span class="kb-jenis__dot" style="background:{{ optional($h->jenisPekerjaan)->warna ?? '#5f6f52' }}" aria-hidden="true"></span>
                                        {{ optional($h->jenisPekerjaan)->jenis ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    {{ optional($h->pekerja)->nama ?? '-' }}
                                    <span class="kb-sub">{{ optional($h->pekerja)->kode_pekerja }}</span>
                                </td>
                                <td>{{ optional($h->mandor)->nama ?? '-' }}</td>
                                <td>{{ number_format($h->jumlah, 2, ',', '.') }} {{ optional($h->jenisPekerjaan)->satuan }}</td>
                                <td class="kb-c">
                                    <span class="kb-actions">
                                        <button type="button" class="kb-icon" data-aksi="lihat-hasil" aria-label="Lihat detail">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="kb-icon" data-aksi="ubah-hasil" aria-label="Ubah data">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="kb-icon kb-icon--del" data-aksi="hapus-hasil" aria-label="Hapus data">
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
                                    @if (request()->anyFilled(['tanggal', 'blok', 'jenis', 'q']))
                                        Tidak ada data hasil kerja yang cocok dengan filter ini.
                                    @else
                                        Belum ada data hasil kerja. Klik "Tambah Hasil Kerja" untuk menambahkan.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="kb-foot">
                <span>
                    @if ($hasilKerjas->total() > 0)
                        Menampilkan {{ $hasilKerjas->firstItem() }} - {{ $hasilKerjas->lastItem() }} dari {{ $hasilKerjas->total() }} data
                    @else
                        Menampilkan 0 data
                    @endif
                </span>

                @if ($hasilKerjas->hasPages())
                    <nav class="kb-pager" aria-label="Halaman">
                        @if ($hasilKerjas->onFirstPage())
                            <span class="kb-page kb-page--off" aria-hidden="true">&lsaquo;</span>
                        @else
                            <a class="kb-page" href="{{ $hasilKerjas->previousPageUrl() }}">&lsaquo;</a>
                        @endif
                        @for ($i = $mul; $i <= $akh; $i++)
                            @if ($i === $hal)
                                <span class="kb-page kb-page--on" aria-current="page">{{ $i }}</span>
                            @else
                                <a class="kb-page" href="{{ $hasilKerjas->url($i) }}">{{ $i }}</a>
                            @endif
                        @endfor
                        @if ($hasilKerjas->hasMorePages())
                            <a class="kb-page" href="{{ $hasilKerjas->nextPageUrl() }}">&rsaquo;</a>
                        @else
                            <span class="kb-page kb-page--off" aria-hidden="true">&rsaquo;</span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </section>
</div>

{{-- ===================== DIALOG: TAMBAH / UBAH / LIHAT HASIL KERJA ===================== --}}
<dialog id="dlgHasil" class="kb kb-dlg" aria-labelledby="judulHasil">
    <div class="kb-dlg__box">
        <div class="kb-dlg__head">
            <h2 id="judulHasil" class="kb-dlg__title">Tambah Hasil Kerja</h2>
            <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <form id="formHasil" method="post" action="{{ url('admin/hasil-kerja') }}" data-url-dasar="{{ url('admin/hasil-kerja') }}">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="methodHasil" disabled>
            <input type="hidden" name="_mode" value="{{ old('_mode', 'tambah') }}" id="modeHasil">
            <input type="hidden" name="_id" value="{{ old('_id') }}" id="idHasil">

            <div class="kb-dlg__body">
                <div class="kb-form">
                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="fPekerja">Pekerja</label>
                        <select class="kb-select" id="fPekerja" name="pekerja_id" required>
                            <option value="">Pilih Pekerja</option>
                            @foreach ($pekerjas as $p)
                                <option value="{{ $p->id }}"
                                    data-blok="{{ $p->blok_id }}"
                                    data-jenis="{{ $p->jenis_pekerjaan_id }}"
                                    {{ (string) old('pekerja_id') === (string) $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} ({{ $p->kode_pekerja }})
                                </option>
                            @endforeach
                        </select>
                        <p class="kb-hint">Memilih pekerja akan mengisi otomatis blok &amp; jenis pekerjaan sesuai data pekerja (masih bisa diubah manual).</p>
                        @error('pekerja_id') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field">
                        <label class="kb-label" for="fBlokHasil">Blok</label>
                        <select class="kb-select" id="fBlokHasil" name="blok_id" required>
                            <option value="">Pilih Blok</option>
                            @foreach ($bloks as $b)
                                <option value="{{ $b->id }}" {{ (string) old('blok_id') === (string) $b->id ? 'selected' : '' }}>{{ $b->nama_blok }}</option>
                            @endforeach
                        </select>
                        @error('blok_id') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field">
                        <label class="kb-label" for="fJenisHasil">Jenis Pekerjaan</label>
                        <select class="kb-select" id="fJenisHasil" name="jenis_pekerjaan_id" required>
                            <option value="">Pilih Jenis Pekerjaan</option>
                            @foreach ($jenisPekerjaans as $jp)
                                <option value="{{ $jp->id }}" data-satuan="{{ $jp->satuan }}" {{ (string) old('jenis_pekerjaan_id') === (string) $jp->id ? 'selected' : '' }}>
                                    {{ $jp->jenis }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_pekerjaan_id') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field">
                        <label class="kb-label" for="fMandorHasil">Mandor</label>
                        <select class="kb-select" id="fMandorHasil" name="mandor_id" required>
                            <option value="">Pilih Mandor</option>
                            @foreach ($mandors as $m)
                                <option value="{{ $m->id }}" {{ (string) old('mandor_id') === (string) $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                        @error('mandor_id') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field">
                        <label class="kb-label" for="fTanggalHasil">Tanggal</label>
                        <input class="kb-input" id="fTanggalHasil" name="tanggal" type="date" value="{{ old('tanggal') }}" required>
                        @error('tanggal') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="fJumlahHasil">Jumlah Hasil</label>
                        <div class="kb-jumlah-wrap">
                            <input class="kb-input" id="fJumlahHasil" name="jumlah" type="number" step="0.01" min="0"
                                   placeholder="0" value="{{ old('jumlah') }}" required>
                            <span class="kb-satuan-tampil" id="satuanTampil">&ndash;</span>
                        </div>
                        @error('jumlah') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="fKeteranganHasil">Keterangan (opsional)</label>
                        <input class="kb-input" id="fKeteranganHasil" name="keterangan" type="text" maxlength="255" placeholder="Catatan tambahan" value="{{ old('keterangan') }}">
                        @error('keterangan') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="kb-dlg__actions">
                <button type="button" class="kb-btn kb-btn--ghost" id="btnTutupHasil" data-aksi="tutup">Batal</button>
                <button type="submit" class="kb-btn" id="btnSimpanHasil">Simpan</button>
            </div>
        </form>
    </div>
</dialog>

{{-- ===================== DIALOG: HAPUS HASIL KERJA ===================== --}}
<dialog id="dlgHapusHasil" class="kb kb-dlg kb-dlg--sm" aria-labelledby="judulHapusHasil">
    <div class="kb-dlg__box">
        <div class="kb-dlg__head">
            <h2 id="judulHapusHasil" class="kb-dlg__title">Hapus Data Hasil Kerja?</h2>
            <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
        <form id="formHapusHasil" method="post" action="{{ url('admin/hasil-kerja') }}">
            @csrf
            @method('DELETE')
            <div class="kb-dlg__body">
                <p class="kb-dlg__text">
                    Data hasil kerja <strong id="namaHapusHasil"></strong> akan dihapus permanen.
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
        var dlgHasil = document.getElementById('dlgHasil');
        var formHasil = document.getElementById('formHasil');
        var judulHasil = document.getElementById('judulHasil');
        var methodHasil = document.getElementById('methodHasil');
        var modeHasil = document.getElementById('modeHasil');
        var idHasil = document.getElementById('idHasil');
        var btnSimpanHasil = document.getElementById('btnSimpanHasil');
        var btnTutupHasil = document.getElementById('btnTutupHasil');
        var urlHasil = formHasil.dataset.urlDasar;
        var fieldsHasil = ['pekerja_id', 'blok_id', 'jenis_pekerjaan_id', 'mandor_id', 'tanggal', 'jumlah', 'keterangan'];
        var judulModeHasil = { tambah: 'Tambah Hasil Kerja', ubah: 'Ubah Hasil Kerja', lihat: 'Detail Hasil Kerja' };

        var selectPekerja = document.getElementById('fPekerja');
        var selectBlok = document.getElementById('fBlokHasil');
        var selectJenis = document.getElementById('fJenisHasil');
        var satuanTampil = document.getElementById('satuanTampil');

        function perbaruiSatuanTampil() {
            var opt = selectJenis.selectedOptions[0];
            satuanTampil.textContent = (opt && opt.dataset.satuan) ? opt.dataset.satuan : '\u2013';
        }

        // Isi otomatis blok & jenis pekerjaan saat pekerja dipilih (hanya pada mode tambah/ubah)
        selectPekerja.addEventListener('change', function (e) {
            if (selectPekerja.disabled) return;
            var opt = e.target.selectedOptions[0];
            if (!opt) return;
            if (opt.dataset.blok) selectBlok.value = opt.dataset.blok;
            if (opt.dataset.jenis) selectJenis.value = opt.dataset.jenis;
            perbaruiSatuanTampil();
        });

        selectJenis.addEventListener('change', perbaruiSatuanTampil);

        function bukaFormHasil(mode, data, pertahankan) {
            var lihat = mode === 'lihat';
            judulHasil.textContent = judulModeHasil[mode];
            modeHasil.value = mode;
            idHasil.value = data && data.id ? data.id : '';
            methodHasil.disabled = mode !== 'ubah';
            formHasil.action = mode === 'ubah' ? urlHasil + '/' + data.id : urlHasil;

            if (!pertahankan) {
                formHasil.querySelectorAll('.kb-err').forEach(function (el) { el.remove(); });
                fieldsHasil.forEach(function (f) {
                    var nilai = data ? data[f] : '';
                    formHasil.elements[f].value = nilai == null ? '' : nilai;
                });
            }

            fieldsHasil.forEach(function (f) { formHasil.elements[f].disabled = lihat; });
            perbaruiSatuanTampil();
            btnSimpanHasil.hidden = lihat;
            btnTutupHasil.textContent = lihat ? 'Tutup' : 'Batal';
            dlgHasil.showModal();
        }

        // ---------- Dialog: Hapus Hasil Kerja ----------
        var dlgHapusHasil = document.getElementById('dlgHapusHasil');
        var formHapusHasil = document.getElementById('formHapusHasil');
        var namaHapusHasil = document.getElementById('namaHapusHasil');

        function bukaHapusHasil(data) {
            formHapusHasil.action = urlHasil + '/' + data.id;
            namaHapusHasil.textContent = (data.pekerja_nama || '-') + ' \u2013 ' + (data.tanggal || '');
            dlgHapusHasil.showModal();
        }

        // ---------- Delegasi klik tombol aksi ----------
        document.addEventListener('click', function (e) {
            var tombol = e.target.closest('[data-aksi]');
            if (!tombol) return;
            var aksi = tombol.dataset.aksi;

            if (aksi === 'tutup') {
                tombol.closest('dialog').close();
                return;
            }
            if (aksi === 'tambah-hasil') { bukaFormHasil('tambah', null, false); return; }

            var baris = tombol.closest('tr');
            if (!baris) return;

            if (aksi === 'lihat-hasil' || aksi === 'ubah-hasil' || aksi === 'hapus-hasil') {
                var d = JSON.parse(baris.dataset.hasil);
                if (aksi === 'hapus-hasil') bukaHapusHasil(d);
                else bukaFormHasil(aksi === 'lihat-hasil' ? 'lihat' : 'ubah', d, false);
                return;
            }
        });

        // Klik area gelap di luar kotak dialog = tutup
        [dlgHasil, dlgHapusHasil].forEach(function (d) {
            d.addEventListener('click', function (e) { if (e.target === d) d.close(); });
        });

        // Jika validasi gagal, buka lagi dialog form
        @if ($errors->any())
            bukaFormHasil(@json(old('_mode', 'tambah')), {
                id: @json(old('_id')),
                pekerja_id: @json(old('pekerja_id')),
                blok_id: @json(old('blok_id')),
                jenis_pekerjaan_id: @json(old('jenis_pekerjaan_id')),
                mandor_id: @json(old('mandor_id')),
                tanggal: @json(old('tanggal')),
                jumlah: @json(old('jumlah')),
                keterangan: @json(old('keterangan')),
            }, true);
        @endif
    })();
</script>
@endsection