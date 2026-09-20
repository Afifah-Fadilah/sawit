{{-- resources/views/admin/jenispekerjaan.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Kelola Jenis Pekerjaan')

@section('content')
@php
    $stats = [
        [
            'label' => 'Total Jenis Pekerjaan',
            'value' => number_format($totalJenis, 0, ',', '.'),
            'unit'  => 'Jenis',
            'icon'  => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
        ],
        [
            'label' => 'Aktif',
            'value' => number_format($jenisAktif, 0, ',', '.'),
            'unit'  => 'Jenis',
            'icon'  => '<path d="M20 6 9 17l-5-5"/>',
        ],
        [
            'label' => 'Nonaktif',
            'value' => number_format($jenisNonaktif, 0, ',', '.'),
            'unit'  => 'Jenis',
            'icon'  => '<path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
        ],
    ];

    $sedangCari = request()->filled('search') || (request()->filled('status') && request('status') !== 'semua');
@endphp

<style>
    .kj {
        --kj-deep: #5f6f52;
        --kj-ink: #1f2419;
        --kj-card: #f2f0d9;
        --kj-tan: #b8926e;
        --kj-head: #e2d8bc;

        color: var(--kj-ink);
        font-family: 'Poppins', system-ui, sans-serif;
    }
    .kj, .kj *, .kj *::before, .kj *::after { box-sizing: border-box; }

    .kj-card {
        border-radius: 20px;
        background: var(--kj-card);
        box-shadow: 0 6px 16px rgba(95, 111, 82, .16);
    }

    .kj-flash {
        margin: 0 0 18px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #e3f2d4;
        color: #23652d;
        font-size: 13px;
        font-weight: 500;
    }

    .kj-stats {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 240px));
        gap: 20px;
        margin-bottom: 22px;
    }
    .kj-stat {
        display: grid;
        grid-template-columns: auto 1fr;
        column-gap: 14px;
        align-items: start;
        padding: 20px 18px;
    }
    .kj-stat__icon {
        display: grid;
        place-items: center;
        grid-row: 1 / span 3;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--kj-tan);
        color: var(--kj-card);
    }
    .kj-stat__label { margin: 0; font-size: 11.5px; font-weight: 600; }
    .kj-stat__value { margin: 4px 0 0; font-size: 32px; font-weight: 700; line-height: 1.2; }
    .kj-stat__unit  { margin: 2px 0 0; font-size: 11.5px; font-weight: 500; }

    .kj-panel { padding: 18px 16px 16px; }
    .kj-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .kj-title { margin: 0; color: var(--kj-deep); font-size: 17px; font-weight: 600; }
    .kj-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
    .kj-search { position: relative; }
    .kj-search svg {
        position: absolute;
        top: 50%;
        left: 12px;
        color: #7a7a6c;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .kj-input,
    .kj-select {
        height: 38px;
        border: 1px solid #d5d3bb;
        border-radius: 10px;
        background: #fff;
        color: var(--kj-ink);
        font: inherit;
        font-size: 12px;
    }
    .kj-input { width: 220px; padding: 0 12px 0 34px; }
    .kj-select {
        min-width: 150px;
        padding: 0 38px 0 14px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235f6f52' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        cursor: pointer;
    }
    .kj-input:focus-visible,
    .kj-select:focus-visible,
    .kj-btn:focus-visible,
    .kj-icon:focus-visible {
        outline: 2px solid var(--kj-deep);
        outline-offset: 2px;
    }

    .kj-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 38px;
        padding: 0 18px;
        border: 0;
        border-radius: 10px;
        background: var(--kj-deep);
        color: #fff;
        font: inherit;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .kj-btn:hover { background: #4d5b42; }
    .kj-btn[hidden] { display: none; }
    .kj-btn--ghost { border: 1px solid #cfcdb4; background: #fff; color: var(--kj-ink); }
    .kj-btn--ghost:hover { background: #f3f2e6; }
    .kj-btn--danger { background: #b3382c; }
    .kj-btn--danger:hover { background: #952d23; }

    .kj-sr {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
        white-space: nowrap;
    }

    .kj-wrap { border-radius: 12px; background: #fff; overflow: hidden; }
    .kj-scroll { overflow-x: auto; }
    .kj-table { width: 100%; min-width: 780px; border-collapse: collapse; font-size: 12px; }
    .kj-table th {
        padding: 14px 16px;
        background: var(--kj-head);
        color: #4a4633;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }
    .kj-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #ecebe3;
        white-space: normal;
        vertical-align: top;
    }
    .kj-table .kj-c { text-align: center; white-space: nowrap; }
    .kj-empty { padding: 34px 16px !important; color: #6b6b60; text-align: center; }

    .kj-nama { display: flex; align-items: center; gap: 10px; white-space: nowrap; }
    .kj-dot { flex: none; width: 12px; height: 12px; border-radius: 50%; }
    .kj-kode { font-size: 10.5px; color: #9a9a8a; }

    .kj-badge {
        display: inline-block;
        min-width: 54px;
        padding: 3px 12px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 600;
        text-align: center;
    }
    .kj-badge--aktif { background: #e3f4d6; color: #23652d; }
    .kj-badge--nonaktif { background: #eeeeea; color: #5b5b52; }

    .kj-actions { display: inline-flex; gap: 6px; }
    .kj-icon {
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        padding: 0;
        border: 1px solid #d3d2c8;
        border-radius: 8px;
        background: #fff;
        color: var(--kj-ink);
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .kj-icon:hover { background: #f1f0e6; }
    .kj-icon--del { border-color: #efc5bf; background: #fdf1ef; color: #b3382c; }
    .kj-icon--del:hover { background: #fadfdb; }

    .kj-foot { padding: 14px 18px; font-size: 11.5px; }

    .kj-dlg {
        width: min(560px, calc(100vw - 32px));
        padding: 0;
        border: 0;
        border-radius: 20px;
        background: #fbfaf1;
        color: var(--kj-ink);
        box-shadow: 0 24px 60px rgba(30, 40, 20, .38);
        font-family: 'Poppins', system-ui, sans-serif;
        overflow-x: hidden;
    }
    .kj-dlg--sm { width: min(420px, calc(100vw - 32px)); }
    .kj-dlg::backdrop { background: rgba(30, 38, 22, .5); }

    .kj-dlg__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 22px 28px 18px;
        border-bottom: 1px solid #e8e6d0;
    }
    .kj-dlg__title { margin: 0; color: var(--kj-deep); font-size: 18px; font-weight: 600; }
    .kj-dlg__x {
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
    .kj-dlg__x:hover { background: #eeecd8; }

    .kj-dlg__body { padding: 24px 28px 26px; }
    .kj-dlg__text { margin: 0; font-size: 13.5px; line-height: 1.7; }

    .kj-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px 22px; }
    .kj-field { min-width: 0; }
    .kj-field--full { grid-column: 1 / -1; }
    .kj-label { display: block; margin-bottom: 7px; font-size: 12.5px; font-weight: 500; }

    .kj-field .kj-input,
    .kj-field .kj-select {
        display: block;
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid #d5d3bb;
        border-radius: 10px;
        background-color: #fff;
        font-size: 13.5px;
    }
    .kj-field textarea.kj-input { height: auto; padding: 10px 14px; resize: vertical; }
    .kj-field .kj-input:focus-visible,
    .kj-field .kj-select:focus-visible {
        outline: none;
        border-color: var(--kj-deep);
        box-shadow: 0 0 0 3px rgba(95, 111, 82, .2);
    }
    .kj-field .kj-select { padding-right: 38px; }
    .kj-field .kj-input:disabled,
    .kj-field .kj-select:disabled {
        background-color: #f3f2e6;
        color: #3f4a35;
        -webkit-text-fill-color: #3f4a35;
        opacity: 1;
    }
    .kj-err { margin: 6px 0 0; color: #a33a2f; font-size: 11.5px; }

    .kj-dlg__actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 28px;
        border-top: 1px solid #e8e6d0;
        background: #f6f5e6;
    }
    .kj-dlg__actions .kj-btn { height: 40px; padding: 0 22px; }

    @media (max-width: 520px) {
        .kj-form { grid-template-columns: minmax(0, 1fr); gap: 16px; }
        .kj-dlg__head { padding: 18px 20px 14px; }
        .kj-dlg__body { padding: 20px; }
        .kj-dlg__actions { padding: 14px 20px; }
    }
</style>

<div class="kj">
    @if (session('status'))
        <p class="kj-flash" role="status">{{ session('status') }}</p>
    @endif

    {{-- Kartu statistik --}}
    <section class="kj-stats" aria-label="Ringkasan jenis pekerjaan">
        @foreach ($stats as $stat)
            <article class="kj-card kj-stat">
                <span class="kj-stat__icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        {!! $stat['icon'] !!}
                    </svg>
                </span>
                <h2 class="kj-stat__label">{{ $stat['label'] }}</h2>
                <p class="kj-stat__value">{{ $stat['value'] }}</p>
                <p class="kj-stat__unit">{{ $stat['unit'] }}</p>
            </article>
        @endforeach
    </section>

    {{-- Daftar jenis pekerjaan --}}
    <section class="kj-card kj-panel" aria-labelledby="judulDaftar">
        <div class="kj-head">
            <h2 id="judulDaftar" class="kj-title">Daftar Jenis Pekerjaan</h2>

            <div class="kj-toolbar">
                <form method="get" action="{{ url('admin/jenis-pekerjaan') }}" class="kj-toolbar" role="search">
                    <div class="kj-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input type="search" name="search" value="{{ request('search') }}" class="kj-input"
                               placeholder="Cari jenis pekerjaan..." aria-label="Cari jenis pekerjaan">
                    </div>

                    <select name="status" class="kj-select" aria-label="Filter status" onchange="this.form.submit()">
                        <option value="semua" {{ !request('status') || request('status') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    <button type="submit" class="kj-sr">Cari</button>
                </form>

                <button type="button" class="kj-btn" data-aksi="tambah" {{ count($jenisTersedia) === 0 ? 'disabled title=Semua jenis sudah ditambahkan' : '' }}>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                        <path d="M5 12h14"/><path d="M12 5v14"/>
                    </svg>
                    Tambah Jenis Pekerjaan
                </button>
            </div>
        </div>

        <div class="kj-wrap">
            <div class="kj-scroll">
                <table class="kj-table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama dan Jenis Pekerjaan</th>
                            <th scope="col">Satuan</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col" class="kj-c">Status</th>
                            <th scope="col" class="kj-c">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jenisPekerjaans as $i => $item)
                            <tr data-jenis="{{ json_encode([
                                'id'          => $item->id,
                                'kode'        => $item->kode,
                                'jenis'       => $item->jenis,
                                'satuan'      => $item->satuan,
                                'keterangan'  => $item->keterangan,
                                'warna'       => $item->warna,
                                'status'      => $item->status,
                            ]) }}">
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="kj-nama">
                                        <span class="kj-dot" style="background: {{ $item->warna }}"></span>
                                        <div>
                                            <div style="font-weight:600;">{{ $item->jenis }}</div>
                                            <div class="kj-kode">Kode: {{ $item->kode }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->satuan }}</td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                                <td class="kj-c">
                                    <span class="kj-badge kj-badge--{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td class="kj-c">
                                    <span class="kj-actions">
                                        <button type="button" class="kj-icon" data-aksi="ubah" aria-label="Ubah {{ $item->jenis }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="kj-icon kj-icon--del" data-aksi="hapus" aria-label="Hapus {{ $item->jenis }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="kj-empty">
                                    @if ($sedangCari)
                                        Tidak ada jenis pekerjaan yang cocok dengan pencarian atau filter.
                                    @else
                                        Belum ada data jenis pekerjaan. Klik "Tambah Jenis Pekerjaan" untuk menambahkan.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="kj-foot">
                Menampilkan 1 - {{ count($jenisPekerjaans) }} dari {{ count($jenisPekerjaans) }} data
            </div>
        </div>
    </section>
</div>

{{-- Dialog tambah / ubah --}}
<dialog id="dlgJenis" class="kj kj-dlg" aria-labelledby="judulJenis">
    <div class="kj-dlg__box">
        <div class="kj-dlg__head">
            <h2 id="judulJenis" class="kj-dlg__title">Tambah Jenis Pekerjaan</h2>
            <button type="button" class="kj-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <form id="formJenis" method="post" action="{{ url('admin/jenis-pekerjaan') }}" data-url-dasar="{{ url('admin/jenis-pekerjaan') }}">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="methodJenis" disabled>

            <div class="kj-dlg__body">
                <div class="kj-form">
                    <div class="kj-field" id="wrapJenisSelect">
                        <label class="kj-label" for="fJenisSelect">Jenis Pekerjaan</label>
                        <select class="kj-select" id="fJenisSelect" name="jenis" required>
                            @foreach ($jenisTersedia as $opsi)
                                <option value="{{ $opsi }}" {{ old('jenis') === $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                            @endforeach
                        </select>
                        @error('jenis') <p class="kj-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kj-field" id="wrapJenisText" style="display:none;">
                        <label class="kj-label">Jenis Pekerjaan</label>
                        <input class="kj-input" id="fJenisText" type="text" disabled>
                    </div>

                    <div class="kj-field">
                        <label class="kj-label" for="fSatuan">Satuan</label>
                        <input class="kj-input" id="fSatuan" name="satuan" type="text" maxlength="50" placeholder="Contoh: Per Kg" value="{{ old('satuan') }}" required>
                        @error('satuan') <p class="kj-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kj-field kj-field--full">
                        <label class="kj-label" for="fKeterangan">Keterangan</label>
                        <textarea class="kj-input" id="fKeterangan" name="keterangan" rows="3" placeholder="Contoh: Pekerjaan memanen buah sawit siap panen">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <p class="kj-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kj-field">
                        <label class="kj-label" for="fWarna">Warna Label</label>
                        <input class="kj-input" id="fWarna" name="warna" type="color" value="{{ old('warna', '#5f6f52') }}" style="height:44px; padding:4px;">
                        @error('warna') <p class="kj-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kj-field">
                        <label class="kj-label" for="fStatus">Status</label>
                        <select class="kj-select" id="fStatus" name="status" required>
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status') <p class="kj-err">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="kj-dlg__actions">
                <button type="button" class="kj-btn kj-btn--ghost" data-aksi="tutup">Batal</button>
                <button type="submit" class="kj-btn">Simpan</button>
            </div>
        </form>
    </div>
</dialog>

{{-- Dialog konfirmasi hapus --}}
<dialog id="dlgHapus" class="kj kj-dlg kj-dlg--sm" aria-labelledby="judulHapus">
    <div class="kj-dlg__box">
        <div class="kj-dlg__head">
            <h2 id="judulHapus" class="kj-dlg__title">Hapus Jenis Pekerjaan?</h2>
            <button type="button" class="kj-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <form id="formHapus" method="post" action="{{ url('admin/jenis-pekerjaan') }}">
            @csrf
            @method('DELETE')

            <div class="kj-dlg__body">
                <p class="kj-dlg__text">
                    Data <strong id="namaHapus"></strong> akan dihapus permanen dan tidak bisa dikembalikan.
                </p>
            </div>

            <div class="kj-dlg__actions">
                <button type="button" class="kj-btn kj-btn--ghost" data-aksi="tutup">Batal</button>
                <button type="submit" class="kj-btn kj-btn--danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    (function () {
        var dlgJenis = document.getElementById('dlgJenis');
        var dlgHapus = document.getElementById('dlgHapus');
        var form = document.getElementById('formJenis');
        var formHapus = document.getElementById('formHapus');
        var judul = document.getElementById('judulJenis');
        var methodInput = document.getElementById('methodJenis');
        var namaHapus = document.getElementById('namaHapus');
        var urlDasar = form.dataset.urlDasar;
        var wrapSelect = document.getElementById('wrapJenisSelect');
        var wrapText = document.getElementById('wrapJenisText');
        var fJenisText = document.getElementById('fJenisText');

        function bukaTambah() {
            judul.textContent = 'Tambah Jenis Pekerjaan';
            methodInput.disabled = true;
            form.action = urlDasar;
            form.reset();
            wrapSelect.style.display = '';
            wrapText.style.display = 'none';
            dlgJenis.showModal();
        }

        function bukaUbah(data) {
            judul.textContent = 'Ubah Jenis Pekerjaan';
            methodInput.disabled = false;
            form.action = urlDasar + '/' + data.id;

            wrapSelect.style.display = 'none';
            wrapText.style.display = '';
            fJenisText.value = data.jenis;

            form.elements['satuan'].value = data.satuan || '';
            form.elements['keterangan'].value = data.keterangan || '';
            form.elements['warna'].value = data.warna || '#5f6f52';
            form.elements['status'].value = data.status || 'aktif';

            dlgJenis.showModal();
        }

        function bukaHapus(data) {
            formHapus.action = urlDasar + '/' + data.id;
            namaHapus.textContent = data.jenis;
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
                bukaTambah();
                return;
            }

            var baris = tombol.closest('tr');
            var data = baris ? JSON.parse(baris.dataset.jenis) : null;
            if (!data) return;

            if (aksi === 'hapus') bukaHapus(data);
            else if (aksi === 'ubah') bukaUbah(data);
        });

        [dlgJenis, dlgHapus].forEach(function (d) {
            d.addEventListener('click', function (e) {
                if (e.target === d) d.close();
            });
        });

        @if ($errors->any())
            bukaTambah();
        @endif
    })();
</script>
@endsection