@extends('admin.layouts.admin')

@section('title', 'Laporan Upah')

@section('content')
@php
    $rp  = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',');

    $pesanSukses = session('sukses');
    $bukaFormBuat = $errors->any() && old('_mode') === 'buat';

    if ($laporan) {
        $stats = [
            ['label' => 'Total Upah Kotor',     'value' => $rp($ringkas['kotor']),    'unit' => 'Sebelum potongan', 'icon' => '<rect x="2" y="7" width="20" height="13" rx="2"/><path d="M16 11h.01"/><path d="M2 10h20"/>'],
            ['label' => 'Potongan Kasbon',      'value' => $rp($ringkas['potongan']), 'unit' => $laporan->isFinal() ? 'Sudah dipotong' : 'Rencana potongan', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15h6"/>'],
            ['label' => 'Total Dibayarkan',     'value' => $rp($ringkas['bersih']),   'unit' => 'Upah bersih', 'icon' => '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>'],
            ['label' => 'Penerima Upah',        'value' => number_format($jumlahTab['pekerja'] + $jumlahTab['mandor'], 0, ',', '.'), 'unit' => $jumlahTab['pekerja'] . ' pekerja, ' . $jumlahTab['mandor'] . ' mandor', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ];
    } else {
        $stats = [
            ['label' => 'Total Laporan Periode', 'value' => number_format($stat['total'], 0, ',', '.'), 'unit' => 'Periode', 'icon' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>'],
            ['label' => 'Masih Draft',           'value' => number_format($stat['draft'], 0, ',', '.'), 'unit' => 'Belum difinalisasi', 'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
            ['label' => 'Total Dibayarkan',      'value' => $rp($stat['dibayar']), 'unit' => 'Laporan final', 'icon' => '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>'],
            ['label' => 'Potongan Kasbon',       'value' => $rp($stat['potongan']), 'unit' => 'Laporan final', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15h6"/>'],
        ];
    }

    if ($laporans) {
        $hal = $laporans->currentPage(); $last = $laporans->lastPage();
        $mul = max(1, $hal - 2);         $akh  = min($last, $hal + 2);
    }
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

    .kb-card { border-radius: 20px; background: var(--kb-card); box-shadow: 0 6px 16px rgba(95, 111, 82, .16); }

    .kb-flash { margin: 0 0 18px; padding: 12px 16px; border-radius: 12px; background: #e3f2d4; color: #23652d; font-size: 13px; font-weight: 500; }
    .kb-flash--err { background: #fbe1de; color: #a33a2f; }
    .kb-flash--warn { background: #fdf0d2; color: #7a5410; }

    .kb-stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-bottom: 22px; }
    .kb-stat { display: grid; grid-template-columns: auto 1fr; column-gap: 14px; align-items: start; padding: 18px 18px; }
    .kb-stat__icon { display: grid; place-items: center; grid-row: 1 / span 3; width: 40px; height: 40px; border-radius: 10px; background: var(--kb-tan); color: var(--kb-card); }
    .kb-stat__label { margin: 0; font-size: 11px; font-weight: 600; }
    .kb-stat__value { margin: 4px 0 0; font-size: 22px; font-weight: 700; line-height: 1.25; }
    .kb-stat__unit  { margin: 2px 0 0; font-size: 10.5px; font-weight: 500; color: #6b6b60; }

    .kb-panel { padding: 18px 16px 16px; }
    .kb-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
    .kb-title { margin: 0; color: var(--kb-deep); font-size: 15px; font-weight: 600; }
    .kb-periode { margin: 2px 0 0; font-size: 11.5px; color: #6b6b60; }
    .kb-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
    .kb-back { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 14px; color: var(--kb-deep); font-size: 12px; font-weight: 600; text-decoration: none; }
    .kb-back:hover { text-decoration: underline; }

    .kb-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        height: 38px; padding: 0 16px; border: 0; border-radius: 10px;
        background: var(--kb-deep); color: #fff; font: inherit; font-size: 12px; font-weight: 500;
        cursor: pointer; text-decoration: none; transition: background-color .15s ease; white-space: nowrap;
    }
    .kb-btn:hover { background: #4d5b42; color: #fff; }
    .kb-btn[hidden] { display: none; }
    .kb-btn--ghost { border: 1px solid #cfcdb4; background: #fff; color: var(--kb-ink); }
    .kb-btn--ghost:hover { background: #f3f2e6; color: var(--kb-ink); }
    .kb-btn--danger { background: #b3382c; }
    .kb-btn--danger:hover { background: #952d23; }
    .kb-btn:focus-visible, .kb-icon:focus-visible, .kb-page:focus-visible, .kb-tab:focus-visible { outline: 2px solid var(--kb-deep); outline-offset: 2px; }
    .kb-inline { display: inline; margin: 0; }

    .kb-tabs { display: flex; gap: 6px; margin-bottom: 12px; border-bottom: 1px solid #e1dfc8; }
    .kb-tab { padding: 10px 16px; margin-bottom: -1px; border-bottom: 2px solid transparent; color: #6b6b60; font-size: 12.5px; font-weight: 500; text-decoration: none; }
    .kb-tab:hover { color: var(--kb-ink); }
    .kb-tab--on { border-bottom-color: var(--kb-deep); color: var(--kb-deep); font-weight: 600; }

    .kb-wrap { border-radius: 12px; background: #fff; overflow: hidden; }
    .kb-scroll { overflow-x: auto; }
    .kb-table { width: 100%; min-width: 860px; border-collapse: collapse; font-size: 12px; }
    .kb-table th { padding: 14px 16px; background: var(--kb-head); color: #4a4633; font-weight: 600; text-align: left; white-space: nowrap; }
    .kb-table td { padding: 14px 16px; border-bottom: 1px solid #ecebe3; white-space: nowrap; vertical-align: top; }
    .kb-table .kb-c { text-align: center; }
    .kb-table .kb-r { text-align: right; }
    .kb-empty { padding: 34px 16px !important; color: #6b6b60; text-align: center; white-space: normal !important; }
    .kb-bold { font-weight: 600; }
    .kb-minus { color: #b3382c; }
    .kb-ok { color: #23652d; font-weight: 700; }

    .kb-sub { display: block; margin-top: 2px; font-size: 10px; color: #8a8a7a; font-weight: 500; }
    .kb-sub--warn { color: #b3382c; }
    .kb-baris { display: block; font-size: 11px; line-height: 1.6; }

    .kb-badge { display: inline-block; min-width: 56px; padding: 3px 12px; border-radius: 6px; font-size: 10.5px; font-weight: 600; text-align: center; }
    .kb-badge--draft { background: #fdf0d2; color: #7a5410; }
    .kb-badge--final { background: #e3f4d6; color: #23652d; }

    .kb-actions { display: inline-flex; gap: 6px; }
    .kb-icon { display: grid; place-items: center; width: 28px; height: 28px; padding: 0; border: 1px solid #d3d2c8; border-radius: 8px; background: #fff; color: var(--kb-ink); cursor: pointer; text-decoration: none; transition: background-color .15s ease; }
    .kb-icon:hover { background: #f1f0e6; }
    .kb-icon--del { border-color: #efc5bf; background: #fdf1ef; color: #b3382c; }
    .kb-icon--del:hover { background: #fadfdb; }

    .kb-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 18px; font-size: 11.5px; }
    .kb-pager { display: flex; gap: 4px; }
    .kb-page { display: grid; place-items: center; min-width: 26px; height: 26px; padding: 0 6px; border: 1px solid #d3d2c8; border-radius: 6px; background: #fff; color: var(--kb-ink); font-size: 11.5px; text-decoration: none; }
    a.kb-page:hover { background: #f1f0e6; }
    .kb-page--on { border-color: var(--kb-deep); background: var(--kb-deep); color: #fff; }
    .kb-page--off { opacity: .4; }

    /* Dialog */
    .kb-dlg { width: min(520px, calc(100vw - 32px)); padding: 0; border: 0; border-radius: 20px; background: #fbfaf1; color: var(--kb-ink); box-shadow: 0 24px 60px rgba(30, 40, 20, .38); font-family: 'Poppins', system-ui, sans-serif; overflow-x: hidden; }
    .kb-dlg::backdrop { background: rgba(30, 38, 22, .5); }
    .kb-dlg__head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 22px 28px 18px; border-bottom: 1px solid #e8e6d0; }
    .kb-dlg__title { margin: 0; color: var(--kb-deep); font-size: 18px; font-weight: 600; }
    .kb-dlg__x { display: grid; place-items: center; width: 34px; height: 34px; padding: 0; border: 0; border-radius: 10px; background: transparent; color: #6b6b60; cursor: pointer; }
    .kb-dlg__x:hover { background: #eeecd8; }
    .kb-dlg__body { padding: 24px 28px 26px; }
    .kb-dlg__actions { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 28px; border-top: 1px solid #e8e6d0; background: #f6f5e6; }
    .kb-dlg__actions .kb-btn { height: 40px; padding: 0 22px; }

    .kb-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 18px; }
    .kb-field { min-width: 0; }
    .kb-field--full { grid-column: 1 / -1; }
    .kb-label { display: block; margin-bottom: 7px; font-size: 12.5px; font-weight: 500; }
    .kb-input { display: block; width: 100%; height: 44px; padding: 0 14px; border: 1px solid #d5d3bb; border-radius: 10px; background: #fff; color: var(--kb-ink); font: inherit; font-size: 13.5px; }
    .kb-input:focus-visible { outline: none; border-color: var(--kb-deep); box-shadow: 0 0 0 3px rgba(95, 111, 82, .2); }
    .kb-hint { margin: 6px 0 0; color: #6b6b60; font-size: 11px; }
    .kb-err { margin: 6px 0 0; color: #a33a2f; font-size: 11.5px; }

    @media (max-width: 520px) {
        .kb-form { grid-template-columns: minmax(0, 1fr); }
        .kb-dlg__head { padding: 18px 20px 14px; }
        .kb-dlg__body { padding: 20px; }
        .kb-dlg__actions { padding: 14px 20px; }
    }
</style>

<div class="kb">
    @if ($pesanSukses)
        <p class="kb-flash" role="status">{{ $pesanSukses }}</p>
    @endif

    @if ($errors->any() && ! old('_mode'))
        <p class="kb-flash kb-flash--err" role="alert">{{ $errors->first() }}</p>
    @endif

    @if ($laporan)
        <a class="kb-back" href="{{ route('admin.laporanupah') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke daftar laporan
        </a>
    @endif

    {{-- Kartu ringkasan --}}
    <section class="kb-stats" aria-label="Ringkasan laporan upah">
        @foreach ($stats as $s)
            <article class="kb-card kb-stat">
                <span class="kb-stat__icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $s['icon'] !!}</svg>
                </span>
                <h2 class="kb-stat__label">{{ $s['label'] }}</h2>
                <p class="kb-stat__value">{{ $s['value'] }}</p>
                <p class="kb-stat__unit">{{ $s['unit'] }}</p>
            </article>
        @endforeach
    </section>

    @if (! $laporan)
        {{-- ===================== DAFTAR LAPORAN PER PERIODE ===================== --}}
        <section class="kb-card kb-panel">
            <div class="kb-head">
                <h2 class="kb-title">Daftar Laporan Upah</h2>
                <button type="button" class="kb-btn" data-aksi="buat-laporan">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Buat Laporan Periode
                </button>
            </div>

            <div class="kb-wrap">
                <div class="kb-scroll">
                    <table class="kb-table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Periode</th>
                                <th scope="col" class="kb-c">Pekerja</th>
                                <th scope="col" class="kb-c">Mandor</th>
                                <th scope="col" class="kb-r">Upah Kotor</th>
                                <th scope="col" class="kb-r">Potongan Kasbon</th>
                                <th scope="col" class="kb-r">Dibayarkan</th>
                                <th scope="col" class="kb-c">Status</th>
                                <th scope="col" class="kb-c">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporans as $l)
                                <tr>
                                    <td>{{ $laporans->firstItem() + $loop->index }}</td>
                                    <td>
                                        <span class="kb-bold">{{ $l->periode_teks }}</span>
                                        <span class="kb-sub">{{ $l->kode }}@if ($l->keterangan) &bull; {{ $l->keterangan }}@endif</span>
                                    </td>
                                    <td class="kb-c">{{ $l->jumlah_pekerja }}</td>
                                    <td class="kb-c">{{ $l->jumlah_mandor }}</td>
                                    <td class="kb-r">{{ $rp($l->total_kotor) }}</td>
                                    <td class="kb-r {{ $l->total_potongan > 0 ? 'kb-minus' : '' }}">{{ $l->total_potongan > 0 ? '-' : '' }}{{ $rp($l->total_potongan) }}</td>
                                    <td class="kb-r kb-bold">{{ $rp($l->total_bersih) }}</td>
                                    <td class="kb-c"><span class="kb-badge kb-badge--{{ $l->status }}">{{ $l->isFinal() ? 'Final' : 'Draft' }}</span></td>
                                    <td class="kb-c">
                                        <span class="kb-actions">
                                            {{-- Tombol lihat detail --}}
                                            <a class="kb-icon"
                                            href="{{ route('admin.laporanupah', ['periode' => $l->id]) }}"
                                            aria-label="Lihat detail laporan">
                                                <svg width="15" height="15" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>

                                            {{-- Tombol hapus --}}
                                            <form class="kb-inline"
                                                method="POST"
                                                action="{{ route('admin.laporanupah.destroy', $l) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus laporan {{ $l->kode }}?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="kb-icon kb-icon--del"
                                                        aria-label="Hapus laporan">
                                                    <svg width="15" height="15" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 6h18"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </span>
                                    </td>
                                </tr> 
                            @empty
                                <tr>
                                    <td colspan="9" class="kb-empty">Belum ada laporan upah. Klik "Buat Laporan Periode" untuk memulai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="kb-foot">
                    <span>
                        @if ($laporans->total() > 0)
                            Menampilkan {{ $laporans->firstItem() }} - {{ $laporans->lastItem() }} dari {{ $laporans->total() }} periode
                        @else
                            Menampilkan 0 data
                        @endif
                    </span>

                    @if ($laporans->hasPages())
                        <nav class="kb-pager" aria-label="Halaman">
                            @if ($laporans->onFirstPage())
                                <span class="kb-page kb-page--off" aria-hidden="true">&lsaquo;</span>
                            @else
                                <a class="kb-page" href="{{ $laporans->previousPageUrl() }}">&lsaquo;</a>
                            @endif
                            @for ($i = $mul; $i <= $akh; $i++)
                                @if ($i === $hal)
                                    <span class="kb-page kb-page--on" aria-current="page">{{ $i }}</span>
                                @else
                                    <a class="kb-page" href="{{ $laporans->url($i) }}">{{ $i }}</a>
                                @endif
                            @endfor
                            @if ($laporans->hasMorePages())
                                <a class="kb-page" href="{{ $laporans->nextPageUrl() }}">&rsaquo;</a>
                            @else
                                <span class="kb-page kb-page--off" aria-hidden="true">&rsaquo;</span>
                            @endif
                        </nav>
                    @endif
                </div>
            </div>
        </section>
    @else
        {{-- ===================== DETAIL LAPORAN PERIODE ===================== --}}
        @if ($adaTanpaTarif)
            <p class="kb-flash kb-flash--warn" role="alert">
                Ada jenis pekerjaan (termasuk jenis untuk mandor) yang belum punya tarif aktif, upahnya dihitung Rp0. Atur dulu di Kelola Tarif Upah, lalu klik "Hitung Ulang".
            </p>
        @endif

        <section class="kb-card kb-panel">
            <div class="kb-head">
                <div>
                    <h2 class="kb-title">
                        Periode {{ $laporan->periode_teks }}
                        <span class="kb-badge kb-badge--{{ $laporan->status }}">{{ $laporan->isFinal() ? 'Final' : 'Draft' }}</span>
                    </h2>
                    <p class="kb-periode">{{ $laporan->kode }}@if ($laporan->keterangan) &bull; {{ $laporan->keterangan }}@endif</p>
                </div>

                <div class="kb-toolbar">
                    <a class="kb-btn kb-btn--ghost" href="{{ route('admin.laporanupah.slip-semua', $laporan) }}" target="_blank" rel="noopener">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Cetak Semua Slip
                    </a>

                    @if ($laporan->isFinal())
                        <form class="kb-inline" method="post" action="{{ route('admin.laporanupah.batal', $laporan) }}"
                              onsubmit="return confirm('Batalkan finalisasi? Potongan kasbon akan dikembalikan dan laporan kembali menjadi draft.');">
                            @csrf
                            <button type="submit" class="kb-btn kb-btn--danger">Batalkan Finalisasi</button>
                        </form>
                    @else
                        <form class="kb-inline" method="post" action="{{ route('admin.laporanupah.hitung', $laporan) }}"
                              onsubmit="return confirm('Hitung ulang dari data hasil kerja, tarif, dan kasbon terbaru?');">
                            @csrf
                            <button type="submit" class="kb-btn kb-btn--ghost">Hitung Ulang</button>
                        </form>
                        <form class="kb-inline" method="post" action="{{ route('admin.laporanupah.finalisasi', $laporan) }}"
                              onsubmit="return confirm('Finalisasi laporan ini? Potongan kasbon akan dicatat dan sisa pinjaman berkurang.');">
                            @csrf
                            <button type="submit" class="kb-btn">Finalisasi</button>
                        </form>
                    @endif
                </div>
            </div>

            <nav class="kb-tabs" aria-label="Penerima upah">
                <a class="kb-tab {{ $tab === 'pekerja' ? 'kb-tab--on' : '' }}" href="{{ route('admin.laporanupah', ['periode' => $laporan->id, 'tab' => 'pekerja']) }}" @if ($tab === 'pekerja') aria-current="page" @endif>Pekerja ({{ $jumlahTab['pekerja'] }})</a>
                <a class="kb-tab {{ $tab === 'mandor' ? 'kb-tab--on' : '' }}" href="{{ route('admin.laporanupah', ['periode' => $laporan->id, 'tab' => 'mandor']) }}" @if ($tab === 'mandor') aria-current="page" @endif>Mandor ({{ $jumlahTab['mandor'] }})</a>
            </nav>
            
            <div style="margin-bottom: 14px;">
                <input
                    type="search"
                    id="cariPenerimaUpah"
                    class="kb-input"
                    style="max-width: 360px;"
                    placeholder="Cari nama pekerja atau mandor..."
                    autocomplete="off"
                    aria-label="Cari nama pekerja atau mandor"
                >
            </div>

            <p id="pesanPencarianUpah"
            class="kb-periode"
            style="display:none; margin-bottom:12px;">
                Nama pekerja atau mandor tidak ditemukan.
            </p>

            <div class="kb-wrap">
                <div class="kb-scroll">
                    <table class="kb-table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">{{ $tab === 'pekerja' ? 'Pekerja' : 'Mandor' }}</th>
                                <th scope="col">{{ $tab === 'pekerja' ? 'Rincian Hasil' : 'Rincian Kehadiran' }}</th>
                                <th scope="col" class="kb-r">Upah Kotor</th>
                                <th scope="col" class="kb-r">Potongan Kasbon</th>
                                <th scope="col" class="kb-r">Upah Bersih</th>
                                <th scope="col" class="kb-c">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $it)
                                <tr data-baris-upah data-nama="{{ strtolower($it->nama . ' ' . $it->kode) }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="kb-bold">{{ $it->nama }}</span>
                                        <span class="kb-sub">{{ $it->kode }}</span>
                                    </td>
                                    <td>
                                        @forelse ($it->rincian ?? [] as $r)
                                            <span class="kb-baris">
                                                {{ $r['jenis'] ?? ($r['label'] ?? '-') }}: {{ $fmt($r['jumlah'] ?? 0) }} {{ $r['satuan'] ?? '' }}
                                                @if (! empty($r['tanpa_tarif']))
                                                    <span class="kb-sub--warn">&times; tarif belum diatur</span>
                                                @else
                                                    &times; {{ $rp($r['tarif'] ?? 0) }} = <strong>{{ $rp($r['subtotal'] ?? 0) }}</strong>
                                                @endif
                                            </span>
                                        @empty
                                            <span class="kb-sub">{{ $tab === 'mandor' ? 'Tidak hadir pada periode ini' : '-' }}</span>
                                        @endforelse
                                    </td>
                                    <td class="kb-r">{{ $rp($it->upah_kotor) }}</td>
                                    <td class="kb-r {{ $it->potongan_kasbon > 0 ? 'kb-minus' : '' }}">
                                        {{ $it->potongan_kasbon > 0 ? '-' : '' }}{{ $rp($it->potongan_kasbon) }}
                                        @foreach ($it->rincian_kasbon ?? [] as $kb)
                                            <span class="kb-sub">Kasbon {{ \Illuminate\Support\Carbon::parse($kb['tanggal'])->translatedFormat('d M Y') }} (sisa {{ $rp($kb['sisa_setelah']) }})</span>
                                        @endforeach
                                    </td>
                                    <td class="kb-r kb-bold">{{ $rp($it->upah_bersih) }}</td>
                                    <td class="kb-c">
                                        <span class="kb-actions">
                                            @if ($it->upah_kotor > 0)
                                                <a class="kb-icon" href="{{ route('admin.laporanupah.slip', $it) }}" target="_blank" rel="noopener" aria-label="Cetak slip upah">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                                </a>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="kb-empty">
                                        {{ $tab === 'pekerja' ? 'Tidak ada pekerja dengan hasil kerja pada periode ini.' : 'Belum ada mandor aktif.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif
</div>

{{-- ===================== DIALOG: BUAT LAPORAN PERIODE ===================== --}}
<dialog id="dlgBuat" class="kb kb-dlg" aria-labelledby="judulBuat">
    <div class="kb-dlg__box">
        <div class="kb-dlg__head">
            <h2 id="judulBuat" class="kb-dlg__title">Buat Laporan Periode</h2>
            <button type="button" class="kb-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <form method="post" action="{{ route('admin.laporanupah.store') }}" novalidate>
            @csrf
            <input type="hidden" name="_mode" value="buat">
            <div class="kb-dlg__body">
                <div class="kb-form">
                    <div class="kb-field">
                        <label class="kb-label" for="fMulai">Tanggal Mulai</label>
                        <input class="kb-input" type="date" id="fMulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $saran['mulai']) }}" required>
                        @error('tanggal_mulai') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>
                    <div class="kb-field">
                        <label class="kb-label" for="fSelesai">Tanggal Selesai</label>
                        <input class="kb-input" type="date" id="fSelesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $saran['sampai']) }}" required>
                        @error('tanggal_selesai') <p class="kb-err">{{ $message }}</p> @enderror
                    </div>
                    <div class="kb-field kb-field--full">
                        <label class="kb-label" for="fKet">Keterangan (opsional)</label>
                        <input class="kb-input" type="text" id="fKet" name="keterangan" maxlength="255" placeholder="Mis. Upah minggu ke-2 Oktober" value="{{ old('keterangan') }}">
                        <p class="kb-hint">Rentang tanggal bebas (1 minggu, 2 minggu, dst). Periode tidak boleh bertabrakan dengan laporan lain. Upah mandor dihitung dari jumlah hari hadir &times; tarif harian.</p>
                    </div>
                </div>
            </div>
            <div class="kb-dlg__actions">
                <button type="button" class="kb-btn kb-btn--ghost" data-aksi="tutup">Batal</button>
                <button type="submit" class="kb-btn">Buat &amp; Hitung</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    (function () {
        var dlgBuat = document.getElementById('dlgBuat');
        var inputCari = document.getElementById('cariPenerimaUpah');
        var pesanKosong = document.getElementById('pesanPencarianUpah');

        if (inputCari) {
            inputCari.addEventListener('input', function () {
                var kata = inputCari.value.trim().toLowerCase();
                var baris = document.querySelectorAll('[data-baris-upah]');
                var jumlahTampil = 0;

                baris.forEach(function (tr) {
                    var nama = tr.dataset.nama || '';
                    var cocok = nama.includes(kata);

                    tr.style.display = cocok ? '' : 'none';

                    if (cocok) {
                        jumlahTampil++;
                    }
                });

                if (pesanKosong) {
                    pesanKosong.style.display =
                        jumlahTampil === 0 ? 'block' : 'none';
                }
            });
        }

        document.addEventListener('click', function (e) {
            var tombol = e.target.closest('[data-aksi]');
            if (!tombol) return;
            var aksi = tombol.dataset.aksi;

            if (aksi === 'tutup') { tombol.closest('dialog').close(); return; }
            if (aksi === 'buat-laporan') { dlgBuat.showModal(); }
        });

        dlgBuat.addEventListener('click', function (e) { if (e.target === dlgBuat) dlgBuat.close(); });

        @if ($bukaFormBuat)
            dlgBuat.showModal();
        @endif
    })();
</script>
@endsection