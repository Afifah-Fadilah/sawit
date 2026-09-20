{{-- resources/views/admin/tarifupah.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Kelola Tarif Upah')

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
            'label' => 'Total Tarif Aktif',
            'value' => number_format($totalTarifAktif, 0, ',', '.'),
            'unit'  => 'Jenis',
            'icon'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="M15 9.5c0-1.1-1.34-2-3-2s-3 .9-3 2 1.34 2 3 2 3 .9 3 2-1.34 2-3 2-3-.9-3-2"/>',
        ],
    ];

    $sedangCari = request()->filled('search') || (request()->filled('status') && request('status') !== 'semua');
@endphp

<style>
    .kt {
        --kt-deep: #5f6f52;
        --kt-ink: #1f2419;
        --kt-card: #f2f0d9;
        --kt-tan: #b8926e;
        --kt-head: #e2d8bc;

        color: var(--kt-ink);
        font-family: 'Poppins', system-ui, sans-serif;
    }
    .kt, .kt *, .kt *::before, .kt *::after { box-sizing: border-box; }

    .kt-card {
        border-radius: 20px;
        background: var(--kt-card);
        box-shadow: 0 6px 16px rgba(95, 111, 82, .16);
    }

    .kt-flash {
        margin: 0 0 18px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #e3f2d4;
        color: #23652d;
        font-size: 13px;
        font-weight: 500;
    }

    .kt-stats {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 240px));
        gap: 20px;
        margin-bottom: 22px;
    }
    .kt-stat {
        display: grid;
        grid-template-columns: auto 1fr;
        column-gap: 14px;
        align-items: start;
        padding: 20px 18px;
    }
    .kt-stat__icon {
        display: grid;
        place-items: center;
        grid-row: 1 / span 3;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--kt-tan);
        color: var(--kt-card);
    }
    .kt-stat__label { margin: 0; font-size: 11.5px; font-weight: 600; }
    .kt-stat__value { margin: 4px 0 0; font-size: 32px; font-weight: 700; line-height: 1.2; }
    .kt-stat__unit  { margin: 2px 0 0; font-size: 11.5px; font-weight: 500; }

    .kt-panel { padding: 18px 16px 16px; }
    .kt-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .kt-title { margin: 0; color: var(--kt-deep); font-size: 17px; font-weight: 600; }
    .kt-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
    .kt-search { position: relative; }
    .kt-search svg {
        position: absolute;
        top: 50%;
        left: 12px;
        color: #7a7a6c;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .kt-input,
    .kt-select {
        height: 38px;
        border: 1px solid #d5d3bb;
        border-radius: 10px;
        background: #fff;
        color: var(--kt-ink);
        font: inherit;
        font-size: 12px;
    }
    .kt-input { width: 220px; padding: 0 12px 0 34px; }
    .kt-select {
        min-width: 150px;
        padding: 0 38px 0 14px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235f6f52' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        cursor: pointer;
    }
    .kt-input:focus-visible,
    .kt-select:focus-visible,
    .kt-btn:focus-visible,
    .kt-icon:focus-visible {
        outline: 2px solid var(--kt-deep);
        outline-offset: 2px;
    }

    .kt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 38px;
        padding: 0 18px;
        border: 0;
        border-radius: 10px;
        background: var(--kt-deep);
        color: #fff;
        font: inherit;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .kt-btn:hover { background: #4d5b42; }
    .kt-btn:disabled { opacity: .5; cursor: not-allowed; }
    .kt-btn[hidden] { display: none; }
    .kt-btn--ghost { border: 1px solid #cfcdb4; background: #fff; color: var(--kt-ink); }
    .kt-btn--ghost:hover { background: #f3f2e6; }
    .kt-btn--danger { background: #b3382c; }
    .kt-btn--danger:hover { background: #952d23; }

    .kt-sr {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
        white-space: nowrap;
    }

    .kt-wrap { border-radius: 12px; background: #fff; overflow: hidden; }
    .kt-scroll { overflow-x: auto; }
    .kt-table { width: 100%; min-width: 780px; border-collapse: collapse; font-size: 12px; }
    .kt-table th {
        padding: 14px 16px;
        background: var(--kt-head);
        color: #4a4633;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }
    .kt-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #ecebe3;
        white-space: normal;
        vertical-align: top;
    }
    .kt-table .kt-c { text-align: center; white-space: nowrap; }
    .kt-empty { padding: 34px 16px !important; color: #6b6b60; text-align: center; }

    .kt-nama { display: flex; align-items: center; gap: 10px; white-space: nowrap; }
    .kt-dot { flex: none; width: 12px; height: 12px; border-radius: 50%; }
    .kt-kode { font-size: 10.5px; color: #9a9a8a; }

    .kt-badge {
        display: inline-block;
        min-width: 54px;
        padding: 3px 12px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 600;
        text-align: center;
    }
    .kt-badge--aktif { background: #e3f4d6; color: #23652d; }
    .kt-badge--nonaktif { background: #eeeeea; color: #5b5b52; }

    .kt-actions { display: inline-flex; gap: 6px; }
    .kt-icon {
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        padding: 0;
        border: 1px solid #d3d2c8;
        border-radius: 8px;
        background: #fff;
        color: var(--kt-ink);
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .kt-icon:hover { background: #f1f0e6; }
    .kt-icon--del { border-color: #efc5bf; background: #fdf1ef; color: #b3382c; }
    .kt-icon--del:hover { background: #fadfdb; }

    .kt-foot { padding: 14px 18px; font-size: 11.5px; }

    .kt-dlg {
        width: min(560px, calc(100vw - 32px));
        padding: 0;
        border: 0;
        border-radius: 20px;
        background: #fbfaf1;
        color: var(--kt-ink);
        box-shadow: 0 24px 60px rgba(30, 40, 20, .38);
        font-family: 'Poppins', system-ui, sans-serif;
        overflow-x: hidden;
    }
    .kt-dlg--sm { width: min(420px, calc(100vw - 32px)); }
    .kt-dlg::backdrop { background: rgba(30, 38, 22, .5); }

    .kt-dlg__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 22px 28px 18px;
        border-bottom: 1px solid #e8e6d0;
    }
    .kt-dlg__title { margin: 0; color: var(--kt-deep); font-size: 18px; font-weight: 600; }
    .kt-dlg__x {
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
    .kt-dlg__x:hover { background: #eeecd8; }

    .kt-dlg__body { padding: 24px 28px 26px; }
    .kt-dlg__text { margin: 0; font-size: 13.5px; line-height: 1.7; }

    .kt-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px 22px; }
    .kt-field { min-width: 0; }
    .kt-field--full { grid-column: 1 / -1; }
    .kt-label { display: block; margin-bottom: 7px; font-size: 12.5px; font-weight: 500; }

    .kt-field .kt-input,
    .kt-field .kt-select {
        display: block;
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid #d5d3bb;
        border-radius: 10px;
        background-color: #fff;
        font-size: 13.5px;
    }
    .kt-field .kt-input:focus-visible,
    .kt-field .kt-select:focus-visible {
        outline: none;
        border-color: var(--kt-deep);
        box-shadow: 0 0 0 3px rgba(95, 111, 82, .2);
    }
    .kt-field .kt-select { padding-right: 38px; }
    .kt-field .kt-input:disabled,
    .kt-field .kt-select:disabled {
        background-color: #f3f2e6;
        color: #3f4a35;
        -webkit-text-fill-color: #3f4a35;
        opacity: 1;
    }
    .kt-hint { margin: 6px 0 0; color: #6b6b60; font-size: 11px; }
    .kt-err { margin: 6px 0 0; color: #a33a2f; font-size: 11.5px; }

    .kt-dlg__actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 28px;
        border-top: 1px solid #e8e6d0;
        background: #f6f5e6;
    }
    .kt-dlg__actions .kt-btn { height: 40px; padding: 0 22px; }

    @media (max-width: 520px) {
        .kt-form { grid-template-columns: minmax(0, 1fr); gap: 16px; }
        .kt-dlg__head { padding: 18px 20px 14px; }
        .kt-dlg__body { padding: 20px; }
        .kt-dlg__actions { padding: 14px 20px; }
    }
</style>

<div class="kt">
    @if (session('status'))
        <p class="kt-flash" role="status">{{ session('status') }}</p>
    @endif

    {{-- Kartu statistik --}}
    <section class="kt-stats" aria-label="Ringkasan tarif upah">
        @foreach ($stats as $stat)
            <article class="kt-card kt-stat">
                <span class="kt-stat__icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        {!! $stat['icon'] !!}
                    </svg>
                </span>
                <h2 class="kt-stat__label">{{ $stat['label'] }}</h2>
                <p class="kt-stat__value">{{ $stat['value'] }}</p>
                <p class="kt-stat__unit">{{ $stat['unit'] }}</p>
            </article>
        @endforeach
    </section>

    {{-- Daftar tarif upah --}}
    <section class="kt-card kt-panel" aria-labelledby="judulDaftar">
        <div class="kt-head">
            <h2 id="judulDaftar" class="kt-title">Daftar Tarif Upah</h2>

            <div class="kt-toolbar">
                <form method="get" action="{{ url('admin/tarif-upah') }}" class="kt-toolbar" role="search">
                    <div class="kt-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input type="search" name="search" value="{{ request('search') }}" class="kt-input"
                               placeholder="Cari jenis pekerjaan..." aria-label="Cari jenis pekerjaan">
                    </div>

                    <select name="status" class="kt-select" aria-label="Filter status" onchange="this.form.submit()">
                        <option value="semua" {{ !request('status') || request('status') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    <button type="submit" class="kt-sr">Cari</button>
                </form>

                <button type="button" class="kt-btn" data-aksi="tambah"
                    {{ count($jenisTersedia) === 0 ? 'disabled title=Semua jenis pekerjaan sudah punya tarif' : '' }}>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                        <path d="M5 12h14"/><path d="M12 5v14"/>
                    </svg>
                    Tambah Tarif Upah
                </button>
            </div>
        </div>

        <div class="kt-wrap">
            <div class="kt-scroll">
                <table class="kt-table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama dan Jenis Pekerjaan</th>
                            <th scope="col">Satuan</th>
                            <th scope="col">Tarif Upah</th>
                            <th scope="col" class="kt-c">Status</th>
                            <th scope="col" class="kt-c">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tarifUpahs as $i => $item)
                            <tr data-tarif="{{ json_encode([
                                'id'        => $item->id,
                                'jenis'     => $item->jenisPekerjaan->jenis,
                                'kode'      => $item->jenisPekerjaan->kode,
                                'satuan'    => $item->jenisPekerjaan->satuan,
                                'warna'     => $item->jenisPekerjaan->warna,
                                'tarif'     => $item->tarif,
                                'status'    => $item->status,
                            ]) }}">
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="kt-nama">
                                        <span class="kt-dot" style="background: {{ $item->jenisPekerjaan->warna }}"></span>
                                        <div>
                                            <div style="font-weight:600;">{{ $item->jenisPekerjaan->jenis }}</div>
                                            <div class="kt-kode">Kode: {{ $item->jenisPekerjaan->kode }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->jenisPekerjaan->satuan }}</td>
                                <td>Rp {{ number_format($item->tarif, 0, ',', '.') }}</td>
                                <td class="kt-c">
                                    <span class="kt-badge kt-badge--{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td class="kt-c">
                                    <span class="kt-actions">
                                        <button type="button" class="kt-icon" data-aksi="ubah" aria-label="Ubah tarif {{ $item->jenisPekerjaan->jenis }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="kt-icon kt-icon--del" data-aksi="hapus" aria-label="Hapus tarif {{ $item->jenisPekerjaan->jenis }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="kt-empty">
                                    @if ($sedangCari)
                                        Tidak ada tarif upah yang cocok dengan pencarian atau filter.
                                    @else
                                        Belum ada data tarif upah. Klik "Tambah Tarif Upah" untuk menambahkan.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="kt-foot">
                Menampilkan 1 - {{ count($tarifUpahs) }} dari {{ count($tarifUpahs) }} data
            </div>
        </div>
    </section>
</div>

{{-- Dialog tambah / ubah --}}
<dialog id="dlgTarif" class="kt kt-dlg" aria-labelledby="judulTarif">
    <div class="kt-dlg__box">
        <div class="kt-dlg__head">
            <h2 id="judulTarif" class="kt-dlg__title">Tambah Tarif Upah</h2>
            <button type="button" class="kt-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <form id="formTarif" method="post" action="{{ url('admin/tarif-upah') }}" data-url-dasar="{{ url('admin/tarif-upah') }}">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="methodTarif" disabled>

            <div class="kt-dlg__body">
                <div class="kt-form">
                    <div class="kt-field kt-field--full" id="wrapJenisSelect">
                        <label class="kt-label" for="fJenisSelect">Jenis Pekerjaan</label>
                        <select class="kt-select" id="fJenisSelect" name="jenis_pekerjaan_id" required>
                            <option value="">Pilih Jenis Pekerjaan</option>
                            @foreach ($jenisTersedia as $opsi)
                                <option value="{{ $opsi->id }}" {{ old('jenis_pekerjaan_id') == $opsi->id ? 'selected' : '' }}>
                                    {{ $opsi->jenis }} ({{ $opsi->satuan }})
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_pekerjaan_id') <p class="kt-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kt-field kt-field--full" id="wrapJenisText" style="display:none;">
                        <label class="kt-label">Jenis Pekerjaan</label>
                        <input class="kt-input" id="fJenisText" type="text" disabled>
                        <p class="kt-hint">Jenis pekerjaan tidak bisa diganti setelah tarif dibuat. Hapus lalu buat baru kalau perlu pasangan berbeda.</p>
                    </div>

                    <div class="kt-field">
                        <label class="kt-label" for="fTarif">Tarif Upah (Rp)</label>
                        <input class="kt-input" id="fTarif" name="tarif" type="number" min="0" step="1"
                               placeholder="Contoh: 250000" value="{{ old('tarif') }}" required>
                        @error('tarif') <p class="kt-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="kt-field">
                        <label class="kt-label" for="fStatus">Status</label>
                        <select class="kt-select" id="fStatus" name="status" required>
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status') <p class="kt-err">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="kt-dlg__actions">
                <button type="button" class="kt-btn kt-btn--ghost" data-aksi="tutup">Batal</button>
                <button type="submit" class="kt-btn">Simpan</button>
            </div>
        </form>
    </div>
</dialog>

{{-- Dialog konfirmasi hapus --}}
<dialog id="dlgHapusTarif" class="kt kt-dlg kt-dlg--sm" aria-labelledby="judulHapusTarif">
    <div class="kt-dlg__box">
        <div class="kt-dlg__head">
            <h2 id="judulHapusTarif" class="kt-dlg__title">Hapus Tarif Upah?</h2>
            <button type="button" class="kt-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <form id="formHapusTarif" method="post" action="{{ url('admin/tarif-upah') }}">
            @csrf
            @method('DELETE')

            <div class="kt-dlg__body">
                <p class="kt-dlg__text">
                    Tarif untuk <strong id="namaHapusTarif"></strong> akan dihapus permanen dan tidak bisa dikembalikan.
                </p>
            </div>

            <div class="kt-dlg__actions">
                <button type="button" class="kt-btn kt-btn--ghost" data-aksi="tutup">Batal</button>
                <button type="submit" class="kt-btn kt-btn--danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    (function () {
        var dlgTarif = document.getElementById('dlgTarif');
        var dlgHapus = document.getElementById('dlgHapusTarif');
        var form = document.getElementById('formTarif');
        var formHapus = document.getElementById('formHapusTarif');
        var judul = document.getElementById('judulTarif');
        var methodInput = document.getElementById('methodTarif');
        var namaHapus = document.getElementById('namaHapusTarif');
        var urlDasar = form.dataset.urlDasar;
        var wrapSelect = document.getElementById('wrapJenisSelect');
        var wrapText = document.getElementById('wrapJenisText');
        var fJenisText = document.getElementById('fJenisText');

        function bukaTambah() {
            judul.textContent = 'Tambah Tarif Upah';
            methodInput.disabled = true;
            form.action = urlDasar;
            form.reset();
            wrapSelect.style.display = '';
            wrapText.style.display = 'none';
            dlgTarif.showModal();
        }

        function bukaUbah(data) {
            judul.textContent = 'Ubah Tarif Upah';
            methodInput.disabled = false;
            form.action = urlDasar + '/' + data.id;

            wrapSelect.style.display = 'none';
            wrapText.style.display = '';
            fJenisText.value = data.jenis + ' (Kode: ' + data.kode + ')';

            form.elements['tarif'].value = data.tarif || '';
            form.elements['status'].value = data.status || 'aktif';

            dlgTarif.showModal();
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
            var data = baris ? JSON.parse(baris.dataset.tarif) : null;
            if (!data) return;

            if (aksi === 'hapus') bukaHapus(data);
            else if (aksi === 'ubah') bukaUbah(data);
        });

        [dlgTarif, dlgHapus].forEach(function (d) {
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