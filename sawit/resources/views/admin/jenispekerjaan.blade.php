@extends('admin.layouts.admin')
{{-- resources/views/admin/jenispekerjaan.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Kelola Jenis Pekerjaan')

@section('content')

@php
$sedangCari = request()->filled('q') || request()->filled('status');

$halaman = $jenisPekerjaan->currentPage();
$terakhir = $jenisPekerjaan->lastPage();
$mulai = max(1, $halaman - 2);
$akhir = min($terakhir, $halaman + 2);
@endphp

<style>
    .jp {
        --jp-deep: #5f6f52;
        --jp-ink: #1f2419;
        --jp-card: #f2f0d9;
        --jp-tan: #b8926e;
        --jp-head: #e2d8bc;

        color: var(--jp-ink);
        font-family: 'Poppins', system-ui, sans-serif;
    }

    .jp,
    .jp *,
    .jp *::before,
    .jp *::after {
        box-sizing: border-box;
    }

    /* Notifikasi */
    .jp-flash {
        margin: 0 0 18px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #e3f2d4;
        color: #23652d;
        font-size: 13px;
        font-weight: 500;
    }

    /* Card */
    .jp-card {
        border-radius: 20px;
        background: var(--jp-card);
        box-shadow: 0 6px 16px rgba(95, 111, 82, .16);
    }

    /* Statistik */
    .jp-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(210px, 1fr));
        gap: 20px;
        margin-bottom: 22px;
    }

    .jp-stat {
        display: grid;
        grid-template-columns: auto 1fr;
        column-gap: 14px;
        align-items: start;
        padding: 20px 18px;
    }

    .jp-stat__icon {
        display: grid;
        place-items: center;
        grid-row: 1 / span 3;
        width: 48px;
        height: 48px;
        border-radius: 10px;
    }

    .jp-stat__icon--total {
        background: #e4efdf;
        color: #5f8d5d;
    }

    .jp-stat__icon--aktif {
        background: #eee6f4;
        color: #6d5ca8;
    }

    .jp-stat__icon--nonaktif {
        background: #f8dddd;
        color: #df5b5b;
    }

    .jp-stat__label {
        margin: 0;
        font-size: 12px;
        font-weight: 600;
    }

    .jp-stat__value {
        margin: 4px 0 0;
        font-size: 32px;
        font-weight: 700;
        line-height: 1.2;
    }

    .jp-stat__unit {
        margin: 2px 0 0;
        font-size: 11.5px;
        font-weight: 500;
    }

    /* Panel */
    .jp-panel {
        padding: 18px 16px 16px;
    }

    .jp-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .jp-title {
        margin: 0;
        color: var(--jp-deep);
        font-size: 17px;
        font-weight: 600;
    }

    .jp-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .jp-search {
        position: relative;
    }

    .jp-search svg {
        position: absolute;
        top: 50%;
        left: 12px;
        color: #7a7a6c;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .jp-input,
    .jp-select {
        height: 38px;
        border: 1px solid #d5d3bb;
        border-radius: 10px;
        background: #fff;
        color: var(--jp-ink);
        font: inherit;
        font-size: 12px;
    }

    .jp-input {
        width: 230px;
        padding: 0 12px 0 34px;
    }

    .jp-select {
        min-width: 160px;
        padding: 0 38px 0 14px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235f6f52' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        cursor: pointer;
    }

    .jp-input:focus-visible,
    .jp-select:focus-visible,
    .jp-btn:focus-visible,
    .jp-icon:focus-visible,
    .jp-page:focus-visible {
        outline: 2px solid var(--jp-deep);
        outline-offset: 2px;
    }

    /* Button */
    .jp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 38px;
        padding: 0 18px;
        border: 0;
        border-radius: 10px;
        background: var(--jp-deep);
        color: #fff;
        font: inherit;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color .15s ease;
    }

    .jp-btn:hover {
        background: #4d5b42;
    }

    .jp-btn--ghost {
        border: 1px solid #cfcdb4;
        background: #fff;
        color: var(--jp-ink);
    }

    .jp-btn--ghost:hover {
        background: #f3f2e6;
    }

    .jp-btn--danger {
        background: #b3382c;
    }

    .jp-btn--danger:hover {
        background: #952d23;
    }

    /* Tabel */
    .jp-wrap {
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
    }

    .jp-scroll {
        overflow-x: auto;
    }

    .jp-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
        font-size: 12px;
    }

    .jp-table th {
        padding: 14px 16px;
        background: var(--jp-head);
        color: #4a4633;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }

    .jp-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #ecebe3;
    }

    .jp-table .jp-c {
        text-align: center;
    }

    .jp-table__nama {
        font-weight: 500;
    }

    .jp-table__kode {
        display: inline-block;
        margin-top: 4px;
        padding: 2px 7px;
        border-radius: 4px;
        background: #eeeeeb;
        color: #66665d;
        font-size: 9.5px;
    }

    .jp-keterangan {
        max-width: 280px;
        line-height: 1.5;
    }

    .jp-empty {
        padding: 34px 16px !important;
        color: #6b6b60;
        text-align: center;
    }

    /* Status */
    .jp-badge {
        display: inline-block;
        min-width: 60px;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 600;
        text-align: center;
    }

    .jp-badge--aktif {
        background: #e3f4d6;
        color: #23652d;
    }

    .jp-badge--nonaktif {
        background: #eeeeea;
        color: #5b5b52;
    }

    /* Action */
    .jp-actions {
        display: inline-flex;
        gap: 6px;
    }

    .jp-icon {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border: 1px solid #d3d2c8;
        border-radius: 8px;
        background: #fff;
        color: var(--jp-ink);
        cursor: pointer;
    }

    .jp-icon:hover {
        background: #f1f0e6;
    }

    .jp-icon--del {
        border-color: #efc5bf;
        background: #fdf1ef;
        color: #b3382c;
    }

    .jp-icon--del:hover {
        background: #fadfdb;
    }

    /* Footer + pagination */
    .jp-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 18px;
        font-size: 11.5px;
    }

    .jp-pager {
        display: flex;
        gap: 4px;
    }

    .jp-page {
        display: grid;
        place-items: center;
        min-width: 26px;
        height: 26px;
        padding: 0 6px;
        border: 1px solid #d3d2c8;
        border-radius: 6px;
        background: #fff;
        color: var(--jp-ink);
        font-size: 11.5px;
        text-decoration: none;
    }

    .jp-page:hover {
        background: #f1f0e6;
    }

    .jp-page--on {
        border-color: var(--jp-deep);
        background: var(--jp-deep);
        color: #fff;
    }

    .jp-page--off {
        opacity: .4;
    }

    /* Dialog */
    .jp-dlg {
        width: min(560px, calc(100vw - 32px));
        padding: 0;
        border: 0;
        border-radius: 20px;
        background: #fbfaf1;
        color: var(--jp-ink);
        box-shadow: 0 24px 60px rgba(30, 40, 20, .38);
        font-family: 'Poppins', system-ui, sans-serif;
        overflow: hidden;
    }

    .jp-dlg--sm {
        width: min(420px, calc(100vw - 32px));
    }

    .jp-dlg::backdrop {
        background: rgba(30, 38, 22, .5);
    }

    .jp-dlg__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 22px 28px 18px;
        border-bottom: 1px solid #e8e6d0;
    }

    .jp-dlg__title {
        margin: 0;
        color: var(--jp-deep);
        font-size: 18px;
        font-weight: 600;
    }

    .jp-dlg__x {
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
    }

    .jp-dlg__x:hover {
        background: #eeecd8;
    }

    .jp-dlg__body {
        padding: 24px 28px 26px;
    }

    .jp-dlg__text {
        margin: 0;
        font-size: 13.5px;
        line-height: 1.7;
    }

    .jp-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 22px;
    }

    .jp-field {
        min-width: 0;
    }

    .jp-field--full {
        grid-column: 1 / -1;
    }

    .jp-label {
        display: block;
        margin-bottom: 7px;
        font-size: 12.5px;
        font-weight: 500;
    }

    .jp-field .jp-input,
    .jp-field .jp-select,
    .jp-textarea {
        display: block;
        width: 100%;
        border: 1px solid #d5d3bb;
        border-radius: 10px;
        background: #fff;
        color: var(--jp-ink);
        font: inherit;
        font-size: 13.5px;
    }

    .jp-field .jp-input,
    .jp-field .jp-select {
        height: 44px;
        padding: 0 14px;
    }

    .jp-textarea {
        min-height: 90px;
        padding: 12px 14px;
        resize: vertical;
    }

    .jp-field .jp-input:focus,
    .jp-field .jp-select:focus,
    .jp-textarea:focus {
        outline: none;
        border-color: var(--jp-deep);
        box-shadow: 0 0 0 3px rgba(95, 111, 82, .2);
    }

    .jp-err {
        margin: 6px 0 0;
        color: #a33a2f;
        font-size: 11.5px;
    }

    .jp-dlg__actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 28px;
        border-top: 1px solid #e8e6d0;
        background: #f6f5e6;
    }

    @media (max-width: 800px) {
        .jp-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .jp-form {
            grid-template-columns: 1fr;
        }

        .jp-field--full {
            grid-column: auto;
        }

        .jp-dlg__head,
        .jp-dlg__body,
        .jp-dlg__actions {
            padding-left: 20px;
            padding-right: 20px;
        }
    }
</style>

<div class="jp">

    {{-- Notifikasi --}}
    @if (session('sukses'))
    <p class="jp-flash" role="status">
        {{ session('sukses') }}
    </p>
    @endif

    {{-- Statistik --}}
    <section class="jp-stats">

        <article class="jp-card jp-stat">
            <span class="jp-stat__icon jp-stat__icon--total">
                <svg width="25" height="25" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.7"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M7 5V3h10v2" />
                    <path d="M8 10h8" />
                    <path d="M8 14h5" />
                </svg>
            </span>

            <p class="jp-stat__label">Total Jenis Pekerjaan</p>
            <p class="jp-stat__value">{{ $totalJenis }}</p>
            <p class="jp-stat__unit">Jenis</p>
        </article>

        <article class="jp-card jp-stat">
            <span class="jp-stat__icon jp-stat__icon--aktif">
                <svg width="25" height="25" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m5 12 4 4L19 6" />
                </svg>
            </span>

            <p class="jp-stat__label">Aktif</p>
            <p class="jp-stat__value">{{ $totalAktif }}</p>
            <p class="jp-stat__unit">Jenis</p>
        </article>

        <article class="jp-card jp-stat">
            <span class="jp-stat__icon jp-stat__icon--nonaktif">
                <svg width="25" height="25" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="5" width="14" height="16" rx="2" />
                    <path d="M9 9h6" />
                    <path d="M9 13h6" />
                </svg>
            </span>

            <p class="jp-stat__label">Nonaktif</p>
            <p class="jp-stat__value">{{ $totalNonaktif }}</p>
            <p class="jp-stat__unit">Jenis</p>
        </article>

    </section>

    {{-- Daftar --}}
    <section class="jp-card jp-panel">

        <div class="jp-head">

            <h2 class="jp-title">
                Daftar Jenis Pekerjaan
            </h2>

            <div class="jp-toolbar">

                <form method="get"
                    action="{{ route('admin.jenis-pekerjaan.index') }}"
                    class="jp-toolbar"
                    role="search">

                    <div class="jp-search">

                        <svg width="15" height="15"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>

                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            class="jp-input"
                            placeholder="Cari Jenis Pekerjaan..."
                            aria-label="Cari jenis pekerjaan">

                    </div>

                    <select
                        name="status"
                        class="jp-select"
                        aria-label="Filter status"
                        onchange="this.form.submit()">
                        <option value="">Semua Status</option>

                        <option value="aktif"
                            {{ request('status') === 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="nonaktif"
                            {{ request('status') === 'nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>

                    <button type="submit" style="display:none;">
                        Cari
                    </button>

                </form>

                <button type="button"
                    class="jp-btn"
                    data-aksi="tambah">

                    <svg width="15" height="15"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>

                    Tambah Jenis Pekerjaan

                </button>

            </div>

        </div>

        {{-- Tabel --}}
        <div class="jp-wrap">

            <div class="jp-scroll">

                <table class="jp-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama dan Jenis Pekerjaan</th>
                            <th>Satuan</th>
                            <th>Keterangan</th>
                            <th class="jp-c">Status</th>
                            <th class="jp-c">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($jenisPekerjaan as $item)

                        <tr
                            data-id="{{ $item->id }}"
                            data-kode="{{ $item->kode }}"
                            data-nama="{{ $item->nama_pekerjaan }}"
                            data-satuan="{{ $item->satuan }}"
                            data-keterangan="{{ $item->keterangan }}"
                            data-status="{{ $item->status }}">

                            <td>
                                {{ $jenisPekerjaan->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <div class="jp-table__nama">
                                    {{ $item->nama_pekerjaan }}
                                </div>

                                <span class="jp-table__kode">
                                    Kode: {{ $item->kode }}
                                </span>
                            </td>

                            <td>
                                {{ $item->satuan }}
                            </td>

                            <td>
                                <div class="jp-keterangan">
                                    {{ $item->keterangan ?: '-' }}
                                </div>
                            </td>

                            <td class="jp-c">

                                <span class="jp-badge jp-badge--{{ $item->status }}">
                                    {{ ucfirst($item->status) }}
                                </span>

                            </td>

                            <td class="jp-c">

                                <span class="jp-actions">

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="jp-icon"
                                        data-aksi="ubah"
                                        aria-label="Ubah {{ $item->nama_pekerjaan }}">
                                        <svg width="15" height="15"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                            <path d="m15 5 4 4" />
                                        </svg>
                                    </button>

                                    {{-- Hapus --}}
                                    <button
                                        type="button"
                                        class="jp-icon jp-icon--del"
                                        data-aksi="hapus"
                                        aria-label="Hapus {{ $item->nama_pekerjaan }}">
                                        <svg width="15" height="15"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round">
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
                            <td colspan="6" class="jp-empty">

                                @if ($sedangCari)
                                Tidak ada jenis pekerjaan yang cocok dengan pencarian atau filter.
                                @else
                                Belum ada data jenis pekerjaan.
                                @endif

                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Footer --}}
            <div class="jp-foot">

                <span>

                    @if ($jenisPekerjaan->total() > 0)

                    Menampilkan
                    {{ $jenisPekerjaan->firstItem() }}
                    -
                    {{ $jenisPekerjaan->lastItem() }}
                    dari
                    {{ $jenisPekerjaan->total() }}
                    data

                    @else

                    Menampilkan 0 data

                    @endif

                </span>

                @if ($jenisPekerjaan->hasPages())

                <nav class="jp-pager" aria-label="Halaman">

                    @if ($jenisPekerjaan->onFirstPage())

                    <span class="jp-page jp-page--off">
                        &lsaquo;
                    </span>

                    @else

                    <a
                        class="jp-page"
                        href="{{ $jenisPekerjaan->previousPageUrl() }}">
                        &lsaquo;
                    </a>

                    @endif


                    @for ($i = $mulai; $i <= $akhir; $i++)

                        @if ($i===$halaman)

                        <span class="jp-page jp-page--on">
                        {{ $i }}
                        </span>

                        @else

                        <a
                            class="jp-page"
                            href="{{ $jenisPekerjaan->url($i) }}">
                            {{ $i }}
                        </a>

                        @endif

                        @endfor


                        @if ($jenisPekerjaan->hasMorePages())

                        <a
                            class="jp-page"
                            href="{{ $jenisPekerjaan->nextPageUrl() }}">
                            &rsaquo;
                        </a>

                        @else

                        <span class="jp-page jp-page--off">
                            &rsaquo;
                        </span>

                        @endif

                </nav>

                @endif

            </div>

        </div>

    </section>

</div>


{{-- =========================================================
     MODAL TAMBAH / EDIT
========================================================= --}}

<dialog id="dlgJenisPekerjaan" class="jp jp-dlg">

    <div>

        <div class="jp-dlg__head">

            <h2 id="judulJenisPekerjaan" class="jp-dlg__title">
                Tambah Jenis Pekerjaan
            </h2>

            <button
                type="button"
                class="jp-dlg__x"
                data-aksi="tutup"
                aria-label="Tutup">
                <svg width="18" height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>

        </div>


        <form
            id="formJenisPekerjaan"
            method="post"
            action="{{ route('admin.jenis-pekerjaan.store') }}">

            @csrf

            <input
                type="hidden"
                name="_method"
                id="methodJenisPekerjaan"
                value="PUT"
                disabled>

            <div class="jp-dlg__body">

                <div class="jp-form">

                    {{-- Kode --}}
                    <div class="jp-field">

                        <label class="jp-label" for="fKode">
                            Kode
                        </label>

                        <input
                            class="jp-input"
                            id="fKode"
                            name="kode"
                            type="text"
                            maxlength="20"
                            placeholder="Contoh: JP001"
                            required>

                    </div>


                    {{-- Nama --}}
                    <div class="jp-field">

                        <label class="jp-label" for="fNama">
                            Nama Pekerjaan
                        </label>

                        <input
                            class="jp-input"
                            id="fNama"
                            name="nama_pekerjaan"
                            type="text"
                            maxlength="100"
                            placeholder="Contoh: Pemanen"
                            required>

                    </div>


                    {{-- Satuan --}}
                    <div class="jp-field">

                        <label class="jp-label" for="fSatuan">
                            Satuan
                        </label>

                        <input
                            class="jp-input"
                            id="fSatuan"
                            name="satuan"
                            type="text"
                            maxlength="50"
                            placeholder="Contoh: Per Kg"
                            required>

                    </div>


                    {{-- Status --}}
                    <div class="jp-field">

                        <label class="jp-label" for="fStatus">
                            Status
                        </label>

                        <select
                            class="jp-select"
                            id="fStatus"
                            name="status"
                            required>
                            <option value="aktif">
                                Aktif
                            </option>

                            <option value="nonaktif">
                                Nonaktif
                            </option>
                        </select>

                    </div>


                    {{-- Keterangan --}}
                    <div class="jp-field jp-field--full">

                        <label class="jp-label" for="fKeterangan">
                            Keterangan
                        </label>

                        <textarea
                            class="jp-textarea"
                            id="fKeterangan"
                            name="keterangan"
                            placeholder="Contoh: Pekerjaan memanen buah sawit siap panen"></textarea>

                    </div>

                </div>

            </div>


            <div class="jp-dlg__actions">

                <button
                    type="button"
                    class="jp-btn jp-btn--ghost"
                    data-aksi="tutup">
                    Batal
                </button>

                <button
                    type="submit"
                    class="jp-btn">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</dialog>


{{-- =========================================================
     MODAL HAPUS
========================================================= --}}

<dialog id="dlgHapus" class="jp jp-dlg jp-dlg--sm">

    <div>

        <div class="jp-dlg__head">

            <h2 class="jp-dlg__title">
                Hapus Jenis Pekerjaan?
            </h2>

            <button
                type="button"
                class="jp-dlg__x"
                data-aksi="tutup"
                aria-label="Tutup">
                <svg width="18" height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>

        </div>


        <form
            id="formHapus"
            method="post"
            action="">

            @csrf
            @method('DELETE')

            <div class="jp-dlg__body">

                <p class="jp-dlg__text">

                    Data
                    <strong id="namaHapus"></strong>
                    akan dihapus permanen dan tidak bisa dikembalikan.

                </p>

            </div>


            <div class="jp-dlg__actions">

                <button
                    type="button"
                    class="jp-btn jp-btn--ghost"
                    data-aksi="tutup">
                    Batal
                </button>

                <button
                    type="submit"
                    class="jp-btn jp-btn--danger">
                    Ya, Hapus
                </button>

            </div>

        </form>

    </div>

</dialog>


<script>
    (function() {

        const dlg = document.getElementById('dlgJenisPekerjaan');
        const dlgHapus = document.getElementById('dlgHapus');

        const form = document.getElementById('formJenisPekerjaan');
        const formHapus = document.getElementById('formHapus');

        const judul = document.getElementById('judulJenisPekerjaan');

        const method = document.getElementById('methodJenisPekerjaan');

        const fKode = document.getElementById('fKode');
        const fNama = document.getElementById('fNama');
        const fSatuan = document.getElementById('fSatuan');
        const fKeterangan = document.getElementById('fKeterangan');
        const fStatus = document.getElementById('fStatus');

        const namaHapus = document.getElementById('namaHapus');


        function bukaTambah() {

            judul.textContent = 'Tambah Jenis Pekerjaan';

            form.action = "{{ route('admin.jenis-pekerjaan.store') }}";

            method.disabled = true;

            fKode.value = '';
            fNama.value = '';
            fSatuan.value = '';
            fKeterangan.value = '';
            fStatus.value = 'aktif';

            dlg.showModal();
        }


        function bukaEdit(row) {

            judul.textContent = 'Edit Jenis Pekerjaan';

            const id = row.dataset.id;

            form.action = "{{ url('admin/jenis-pekerjaan') }}" + '/' + id;

            method.disabled = false;

            fKode.value = row.dataset.kode;
            fNama.value = row.dataset.nama;
            fSatuan.value = row.dataset.satuan;
            fKeterangan.value = row.dataset.keterangan;
            fStatus.value = row.dataset.status;

            dlg.showModal();
        }


        function bukaHapus(row) {

            const id = row.dataset.id;

            formHapus.action =
                "{{ url('admin/jenis-pekerjaan') }}" + '/' + id;

            namaHapus.textContent = row.dataset.nama;

            dlgHapus.showModal();
        }


        document.addEventListener('click', function(event) {

            const tombol = event.target.closest('[data-aksi]');

            if (!tombol) return;

            const aksi = tombol.dataset.aksi;


            if (aksi === 'tutup') {

                const dialog = tombol.closest('dialog');

                if (dialog) {
                    dialog.close();
                }

                return;
            }


            if (aksi === 'tambah') {

                bukaTambah();

                return;
            }


            const row = tombol.closest('tr');

            if (!row) return;


            if (aksi === 'ubah') {

                bukaEdit(row);

            }


            if (aksi === 'hapus') {

                bukaHapus(row);

            }

        });


        [dlg, dlgHapus].forEach(function(dialog) {

            dialog.addEventListener('click', function(event) {

                if (event.target === dialog) {
                    dialog.close();
                }

            });

        });

    })();
</script>

@endsection