@extends('mandor.layouts.mandor')

@section('title', 'Riwayat Data')

@section('content')
@php
    $title = 'Riwayat Data';
    $periodeAktif = request('periode', 'semua');
@endphp

<style>
    .rw {
        --rw-deep: #5f6f52;
        --rw-ink: #1f2419;
        --rw-card: #f2f0d9;
        --rw-tan: #b8926e;

        color: var(--rw-ink);
        font-family: 'Poppins', system-ui, sans-serif;
    }
    .rw, .rw *, .rw *::before, .rw *::after { box-sizing: border-box; }

    .rw-unduh {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        height: 44px;
        border-radius: 12px;
        border: 0;
        background: var(--rw-deep);
        color: #fff;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        margin-bottom: 14px;
    }
    .rw-unduh:hover { background: #4d5b42; }

    .rw-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }
    .rw-select {
        flex: 1 1 150px;
        min-width: 150px;
        height: 42px;
        border: 1px solid #d5d3bb;
        border-radius: 12px;
        background: #fff;
        padding: 0 34px 0 14px;
        font: inherit;
        font-size: 12.5px;
        color: var(--rw-ink);
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235f6f52' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        cursor: pointer;
    }
    .rw-select:focus-visible { outline: 2px solid var(--rw-deep); outline-offset: 1px; }

    .rw-btn-reset {
        flex: 1 1 130px;
        min-width: 130px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid #d5d3bb;
        background: #fff;
        color: var(--rw-ink);
        font: inherit;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    .rw-btn-reset:hover { background: #f3f2e6; }

    .rw-search {
        position: relative;
        margin-bottom: 14px;
    }
    .rw-search svg {
        position: absolute;
        top: 50%;
        left: 14px;
        color: #7a7a6c;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .rw-input {
        width: 100%;
        height: 42px;
        border: 1px solid #d5d3bb;
        border-radius: 12px;
        background: #fff;
        padding: 0 14px 0 38px;
        font: inherit;
        font-size: 13px;
        color: var(--rw-ink);
    }
    .rw-input:focus-visible { outline: 2px solid var(--rw-deep); outline-offset: 1px; }

    .rw-wrap {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(95,111,82,.1);
    }
    .rw-scroll { overflow-x: auto; }
    .rw-table {
        width: 100%;
        min-width: 560px;
        border-collapse: collapse;
        font-size: 12px;
    }
    .rw-table th {
        padding: 12px 14px;
        background: var(--rw-card);
        color: #4a4633;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }
    .rw-table td {
        padding: 12px 14px;
        border-top: 1px solid #f0efe6;
        white-space: nowrap;
    }
    .rw-table td.rw-nama { font-weight: 600; }
    .rw-empty {
        padding: 30px 16px !important;
        text-align: center;
        color: #8a8a7a;
        white-space: normal !important;
    }

    .rw-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px;
        font-size: 11.5px;
        color: #6b6b60;
    }
    .rw-pager { display: flex; gap: 6px; }
    .rw-pg {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #d3d2c8;
        background: #fff;
        color: var(--rw-ink);
        text-decoration: none;
    }
    .rw-pg:hover { background: #f1f0e6; }
    .rw-pg--off { opacity: .4; pointer-events: none; }
</style>

<div class="rw" style="padding: 16px 4px 90px;">

    {{-- Unduh laporan: ikut semua filter aktif --}}
    <a href="{{ url('mandor/riwayat/unduh') }}?{{ http_build_query(request()->only(['periode', 'blok_id', 'jenis_pekerjaan_id', 'q'])) }}"
       class="rw-unduh">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
        </svg>
        Unduh Laporan
    </a>

    <form method="get" action="{{ url('mandor/riwayat') }}" id="formRiwayat">

        {{-- Baris filter: Periode, Blok, Jenis Pekerjaan, Reset --}}
        <div class="rw-filter-row">
            <select name="periode" class="rw-select" aria-label="Filter periode" onchange="this.form.submit()">
                <option value="semua" {{ $periodeAktif === 'semua' ? 'selected' : '' }}>Semua Periode</option>
                <option value="minggu" {{ $periodeAktif === 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="bulan" {{ $periodeAktif === 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="tahun" {{ $periodeAktif === 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
            </select>

            <select name="blok_id" class="rw-select" aria-label="Filter blok" onchange="this.form.submit()">
                <option value="">Semua Blok</option>
                @foreach ($bloks as $b)
                    <option value="{{ $b->id }}" {{ (string) request('blok_id') === (string) $b->id ? 'selected' : '' }}>
                        {{ $b->nama_blok }}</option>
                @endforeach
            </select>

            <select name="jenis_pekerjaan_id" class="rw-select" aria-label="Filter jenis pekerjaan" onchange="this.form.submit()">
                <option value="">Semua Jenis Pekerjaan</option>
                @foreach ($jenisPekerjaans as $jp)
                    <option value="{{ $jp->id }}" {{ (string) request('jenis_pekerjaan_id') === (string) $jp->id ? 'selected' : '' }}>
                        {{ $jp->jenis }}</option>
                @endforeach
            </select>

            <a href="{{ url('mandor/riwayat') }}" class="rw-btn-reset">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                </svg>
                Reset Filter
            </a>
        </div>

        {{-- Search nama pekerja --}}
        <div class="rw-search">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}" class="rw-input"
                   placeholder="Cari Nama Pekerja..." onchange="this.form.submit()">
        </div>
    </form>

    {{-- Tabel riwayat --}}
    <div class="rw-wrap">
        <div class="rw-scroll">
            <table class="rw-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Pekerja</th>
                        <th>Pekerjaan</th>
                        <th>Blok</th>
                        <th>Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $r)
                        <tr>
                            <td>{{ $r->tanggal->translatedFormat('d F Y') }}</td>
                            <td class="rw-nama">{{ optional($r->pekerja)->nama ?? '-' }}</td>
                            <td>{{ optional($r->jenisPekerjaan)->jenis ?? '-' }}</td>
                            <td>{{ optional($r->blok)->nama_blok ?? '-' }}</td>
                            <td>{{ number_format($r->jumlah, 0, ',', '.') }} {{ optional($r->jenisPekerjaan)->satuan ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="rw-empty">Tidak ada data riwayat untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($riwayat->total() > 0)
            <div class="rw-foot">
                <span>Menampilkan {{ $riwayat->firstItem() }} - {{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} data</span>
                <span class="rw-pager">
                    @if ($riwayat->onFirstPage())
                        <span class="rw-pg rw-pg--off" aria-hidden="true">&lsaquo;</span>
                    @else
                        <a class="rw-pg" href="{{ $riwayat->previousPageUrl() }}">&lsaquo;</a>
                    @endif
                    @if ($riwayat->hasMorePages())
                        <a class="rw-pg" href="{{ $riwayat->nextPageUrl() }}">&rsaquo;</a>
                    @else
                        <span class="rw-pg rw-pg--off" aria-hidden="true">&rsaquo;</span>
                    @endif
                </span>
            </div>
        @endif
    </div>
</div>
@endsection