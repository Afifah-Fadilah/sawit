{{-- resources/views/mandor/input-data.blade.php --}}
@extends('mandor.layouts.mandor')

@section('title', 'Input Data')

@section('content')
    @php
        $title = 'Input Data';
        $tabAktif = request('blok_id', '');
    @endphp

    <style>
        .ih {
            --ih-deep: #5f6f52;
            --ih-ink: #1f2419;
            --ih-card: #f2f0d9;
            --ih-tan: #b8926e;

            color: var(--ih-ink);
            font-family: 'Poppins', system-ui, sans-serif;
        }

        .ih,
        .ih *,
        .ih *::before,
        .ih *::after {
            box-sizing: border-box;
        }

        .ih-flash {
            margin: 0 0 14px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #e3f2d4;
            color: #23652d;
            font-size: 13px;
            font-weight: 500;
        }

        .ih-flash--err {
            background: #fbe1de;
            color: #b3382c;
        }

        .ih-tanggal {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            background: var(--ih-card);
            color: var(--ih-deep);
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .ih-sub {
            margin: 0 0 16px;
            font-size: 13px;
            color: #6b6b60;
        }

        .ih-searchbar {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }

        .ih-search {
            position: relative;
            flex: 1;
        }

        .ih-search svg {
            position: absolute;
            top: 50%;
            left: 14px;
            color: #7a7a6c;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .ih-input {
            width: 100%;
            height: 42px;
            border: 1px solid #d5d3bb;
            border-radius: 12px;
            background: #fff;
            padding: 0 14px 0 38px;
            font: inherit;
            font-size: 13px;
            color: var(--ih-ink);
        }

        .ih-input:focus-visible {
            outline: 2px solid var(--ih-deep);
            outline-offset: 1px;
        }

        .ih-chips {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            margin-bottom: 14px;
        }

        .ih-chip {
            flex: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid #d5d3bb;
            background: #fff;
            color: var(--ih-ink);
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .ih-chip--on {
            background: var(--ih-deep);
            border-color: var(--ih-deep);
            color: #fff;
        }

        .ih-list {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(95, 111, 82, .1);
        }

        .ih-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 13px 16px;
            border-top: 1px solid #f0efe6;
        }

        .ih-item:first-child {
            border-top: 0;
        }

        .ih-orang {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .ih-avatar {
            flex: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--ih-card);
            color: var(--ih-deep);
            display: grid;
            place-items: center;
        }

        .ih-nama {
            margin: 0;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ih-ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ih-blok {
            margin: 1px 0 0;
            font-size: 11.5px;
            color: #8a8a7a;
        }

        .ih-aksi {
            flex: none;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 0;
            display: grid;
            place-items: center;
            cursor: pointer;
        }

        .ih-aksi--input {
            background: #e3f4d6;
            color: #23652d;
        }

        .ih-aksi--lihat {
            background: #fdeecb;
            color: #b8792f;
        }

        .ih-empty {
            padding: 34px 16px;
            text-align: center;
            color: #8a8a7a;
            font-size: 13px;
        }

        /* Dialog */
        .ih-dlg {
            width: min(420px, calc(100vw - 32px));
            padding: 0;
            border: 0;
            border-radius: 20px;
            background: #fbfaf1;
            color: var(--ih-ink);
            box-shadow: 0 24px 60px rgba(30, 40, 20, .38);
            font-family: 'Poppins', system-ui, sans-serif;
            overflow-x: hidden;
        }

        .ih-dlg::backdrop {
            background: rgba(30, 38, 22, .5);
        }

        .ih-dlg__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 22px 16px;
            border-bottom: 1px solid #e8e6d0;
        }

        .ih-dlg__title {
            margin: 0;
            color: var(--ih-deep);
            font-size: 16px;
            font-weight: 700;
        }

        .ih-dlg__x {
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #6b6b60;
            cursor: pointer;
        }

        .ih-dlg__x:hover {
            background: #eeecd8;
        }

        .ih-dlg__body {
            padding: 20px 22px;
        }

        .ih-sub-title {
            margin: 0 0 16px;
            font-size: 12.5px;
            color: #6b6b60;
        }

        .ih-pekerja-card {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .ih-pekerja-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e8e6d0;
            display: grid;
            place-items: center;
            color: var(--ih-deep);
        }

        .ih-pekerja-nama {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #8a4b1e;
        }

        .ih-pekerja-kode {
            display: inline-block;
            margin-top: 3px;
            padding: 2px 10px;
            border-radius: 999px;
            background: #eef1e0;
            color: #5f6f52;
            font-size: 10.5px;
            font-weight: 600;
        }

        .ih-info-box {
            background: #fff;
            border: 1px solid #e8e6d0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }

        .ih-info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .ih-info-label {
            display: block;
            margin: 0 0 6px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--ih-ink);
        }

        .ih-info-chip {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 10px;
            background: var(--ih-tan);
            color: #fff;
            font-size: 12.5px;
            font-weight: 600;
        }

        .ih-label {
            display: block;
            margin: 0 0 8px;
            font-size: 12.5px;
            font-weight: 600;
        }

        .ih-jumlah-wrap {
            display: flex;
            align-items: stretch;
            border: 1px solid #d5d3bb;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .ih-jumlah-input {
            flex: 1;
            border: 0;
            padding: 0 14px;
            height: 46px;
            font: inherit;
            font-size: 15px;
            color: var(--ih-ink);
        }

        .ih-jumlah-input:focus-visible {
            outline: none;
        }

        .ih-jumlah-input:disabled {
            background: #f6f5ea;
            color: #6b6b60;
        }

        .ih-satuan {
            display: flex;
            align-items: center;
            padding: 0 16px;
            background: #f2f0d9;
            color: var(--ih-deep);
            font-size: 12.5px;
            font-weight: 700;
            border-left: 1px solid #d5d3bb;
            white-space: nowrap;
        }

        .ih-err {
            margin: 6px 0 0;
            color: #a33a2f;
            font-size: 11.5px;
        }

        .ih-dlg__actions {
            display: flex;
            gap: 10px;
            padding: 4px 22px 20px;
        }

        .ih-btn {
            flex: 1;
            height: 44px;
            border-radius: 12px;
            border: 0;
            font: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .ih-btn--ghost {
            background: #fff;
            border: 1px solid #cfcdb4;
            color: var(--ih-ink);
        }

        .ih-btn--ghost:hover {
            background: #f3f2e6;
        }

        .ih-btn--solid {
            background: var(--ih-deep);
            color: #fff;
        }

        .ih-btn--solid:hover {
            background: #4d5b42;
        }

        .ih-btn--solid[hidden] {
            display: none;
        }

        .ih-note {
            margin: 0 22px 20px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #eef1e0;
            font-size: 11.5px;
            color: #4a4633;
        }

        .ih-note strong {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .ih-note ul {
            margin: 0;
            padding-left: 18px;
        }

        .ih-note li {
            margin-bottom: 4px;
        }

        /* Dialog konfirmasi */
        .ih-dlg--konfirmasi {
            width: min(380px, calc(100vw - 32px));
        }

        .ih-konfirmasi-icon {
            display: grid;
            place-items: center;
            width: 56px;
            height: 56px;
            margin: 4px auto 14px;
            border-radius: 50%;
            background: #fdeecb;
            color: #b8792f;
        }

        .ih-konfirmasi-text {
            margin: 0 0 18px;
            text-align: center;
            font-size: 13px;
            color: #4a4633;
            line-height: 1.6;
        }

        .ih-konfirmasi-ringkasan {
            display: grid;
            gap: 10px;
            background: #fff;
            border: 1px solid #e8e6d0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .ih-konfirmasi-baris {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 12.5px;
        }

        .ih-konfirmasi-baris span:first-child {
            color: #8a8a7a;
        }

        .ih-konfirmasi-baris span:last-child {
            font-weight: 700;
            color: var(--ih-ink);
            text-align: right;
        }
    </style>

    <div class="ih" style="padding: 16px 4px 90px;">
        @if (session('success'))
            <p class="ih-flash" role="status">{{ session('success') }}</p>
        @endif
        @if ($errors->any())
            <p class="ih-flash ih-flash--err" role="alert">{{ $errors->first() }}</p>
        @endif

        <span class="ih-tanggal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <path d="M16 2v4" />
                <path d="M8 2v4" />
                <path d="M3 10h18" />
            </svg>
            {{ $hariIni->translatedFormat('d F Y') }}
        </span>

        <p class="ih-sub">Data yang Anda input akan tercatat untuk tanggal hari ini.</p>

        <div>
            <form method="get" action="{{ url('mandor/input-data-harian') }}">
                @if ($tabAktif)
                    <input type="hidden" name="blok_id" value="{{ $tabAktif }}">
                @endif

                <div class="ih-searchbar">
                    <div class="ih-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}" class="ih-input"
                            placeholder="Cari Nama Pekerja..." onchange="this.form.submit()">
                    </div>
                </div>
            </form>

            <div class="ih-chips">
                <a href="{{ url('mandor/input-data-harian') }}{{ request('q') ? '?q=' . request('q') : '' }}"
                    class="ih-chip {{ $tabAktif === '' ? 'ih-chip--on' : '' }}">Semua</a>
                @foreach ($bloks as $b)
                    <a href="{{ url('mandor/input-data-harian') }}?blok_id={{ $b->id }}{{ request('q') ? '&q=' . request('q') : '' }}"
                        class="ih-chip {{ (string) $tabAktif === (string) $b->id ? 'ih-chip--on' : '' }}">{{ $b->nama_blok }}</a>
                @endforeach
            </div>

            <div class="ih-list">
                @forelse ($pekerjas as $p)
                    @php $hasil = $hasilHariIni->get($p->id); @endphp
                    <div class="ih-item"
                        data-pekerja="{{ json_encode([
                            'id' => $p->id,
                            'nama' => $p->nama,
                            'kode' => $p->kode_pekerja,
                            'blok' => optional($p->blok)->nama_blok,
                            'jenis' => optional($p->jenisPekerjaan)->jenis,
                            'satuan' => optional($p->jenisPekerjaan)->satuan,
                            'jumlah' => $hasil->jumlah ?? null,
                            'sudah' => (bool) $hasil,
                        ]) }}">
                        <div class="ih-orang">
                            <span class="ih-avatar" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                </svg>
                            </span>
                            <div style="min-width:0;">
                                <p class="ih-nama">{{ $p->nama }} - {{ optional($p->jenisPekerjaan)->jenis ?? '-' }}
                                </p>
                                <p class="ih-blok">{{ optional($p->blok)->nama_blok ?? '-' }}</p>
                            </div>
                        </div>

                        @if ($hasil)
                            <button type="button" class="ih-aksi ih-aksi--lihat" data-aksi="lihat"
                                aria-label="Lihat hasil kerja {{ $p->nama }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        @else
                            <button type="button" class="ih-aksi ih-aksi--input" data-aksi="input"
                                aria-label="Input hasil kerja {{ $p->nama }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                    <path d="m15 5 4 4" />
                                </svg>
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="ih-empty">
                        Belum ada pekerja pada blok yang Anda kelola, atau tidak cocok dengan pencarian.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Dialog input / lihat --}}
    <dialog id="dlgInput" class="ih ih-dlg" aria-labelledby="judulDlgInput">
        <div class="ih-dlg__head">
            <h2 id="judulDlgInput" class="ih-dlg__title">Input Data</h2>
            <button type="button" class="ih-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <form id="formInput" method="post" action="{{ url('mandor/input-data-harian') }}">
            @csrf
            <input type="hidden" name="pekerja_id" id="fPekerjaId">

            <div class="ih-dlg__body">
                <p class="ih-sub-title" id="subInput">Formulir pencatatan hasil kerja harian.</p>

                <div class="ih-pekerja-card">
                    <span class="ih-pekerja-avatar" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                    </span>
                    <div>
                        <p class="ih-pekerja-nama" id="dNamaPekerja"></p>
                        <span class="ih-pekerja-kode" id="dKodePekerja"></span>
                    </div>
                </div>

                <div class="ih-info-box">
                    <div class="ih-info-row">
                        <div>
                            <span class="ih-info-label">Pekerjaan</span>
                            <span class="ih-info-chip" id="dJenisPekerjaan"></span>
                        </div>
                        <div>
                            <span class="ih-info-label">Blok</span>
                            <span class="ih-info-chip" id="dBlok"></span>
                        </div>
                    </div>

                    <label class="ih-label" for="fJumlah">Masukkan jumlah hasil kerja</label>
                    <div class="ih-jumlah-wrap">
                        <input type="number" step="0.01" min="0.01" name="jumlah" id="fJumlah"
                            class="ih-jumlah-input" placeholder="0" required>
                        <span class="ih-satuan" id="dSatuan"></span>
                    </div>
                    @error('jumlah')
                        <p class="ih-err">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="ih-dlg__actions">
                <button type="button" class="ih-btn ih-btn--ghost" data-aksi="tutup">Batal</button>
                <button type="button" class="ih-btn ih-btn--solid" id="btnSimpanInput"
                    data-aksi="minta-konfirmasi">Simpan data</button>
            </div>
        </form>

        <div class="ih-note">
            <strong>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 16v-4" />
                    <path d="M12 8h.01" />
                </svg>
                Informasi Penting
            </strong>
            <ul>
                <li>Pastikan jumlah hasil kerja yang diinput sudah benar.</li>
                <li>Data hasil kerja yang sudah disimpan tidak dapat diubah.</li>
            </ul>
        </div>
    </dialog>

    {{-- Dialog konfirmasi simpan --}}
    <dialog id="dlgKonfirmasi" class="ih ih-dlg ih-dlg--konfirmasi" aria-labelledby="judulKonfirmasi">
        <div class="ih-dlg__head">
            <h2 id="judulKonfirmasi" class="ih-dlg__title">Konfirmasi Simpan</h2>
            <button type="button" class="ih-dlg__x" data-aksi="tutup" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <div class="ih-dlg__body">
            <span class="ih-konfirmasi-icon" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 8v4" />
                    <path d="M12 16h.01" />
                </svg>
            </span>
            <p class="ih-konfirmasi-text">
                Periksa kembali data di bawah ini. Setelah disimpan, data hasil kerja <strong>tidak dapat diubah</strong>.
            </p>

            <div class="ih-konfirmasi-ringkasan">
                <div class="ih-konfirmasi-baris">
                    <span>Nama Pekerja</span>
                    <span id="kNamaPekerja"></span>
                </div>
                <div class="ih-konfirmasi-baris">
                    <span>Pekerjaan</span>
                    <span id="kJenisPekerjaan"></span>
                </div>
                <div class="ih-konfirmasi-baris">
                    <span>Blok</span>
                    <span id="kBlok"></span>
                </div>
                <div class="ih-konfirmasi-baris">
                    <span>Jumlah Hasil Kerja</span>
                    <span id="kJumlah"></span>
                </div>
            </div>
        </div>

        <div class="ih-dlg__actions">
            <button type="button" class="ih-btn ih-btn--ghost" data-aksi="batal-konfirmasi">Periksa Lagi</button>
            <button type="button" class="ih-btn ih-btn--solid" id="btnKonfirmasiSimpan">Ya, Simpan</button>
        </div>
    </dialog>

    <script>
        (function() {
            var dlg = document.getElementById('dlgInput');
            var judul = document.getElementById('judulDlgInput');
            var sub = document.getElementById('subInput');
            var form = document.getElementById('formInput');
            var btnSimpan = document.getElementById('btnSimpanInput');

            var fPekerjaId = document.getElementById('fPekerjaId');
            var fJumlah = document.getElementById('fJumlah');
            var dNama = document.getElementById('dNamaPekerja');
            var dKode = document.getElementById('dKodePekerja');
            var dJenis = document.getElementById('dJenisPekerjaan');
            var dBlok = document.getElementById('dBlok');
            var dSatuan = document.getElementById('dSatuan');

            function bukaDialog(data, modeLihat) {
                judul.textContent = modeLihat ? 'Detail Hasil Kerja' : 'Input Data';
                sub.textContent = modeLihat ?
                    'Data hasil kerja yang sudah tersimpan (tidak dapat diubah).' :
                    'Formulir pencatatan hasil kerja harian.';

                fPekerjaId.value = data.id;
                dNama.textContent = data.nama;
                dKode.textContent = 'Kode: ' + data.kode;
                dJenis.textContent = data.jenis || '-';
                dBlok.textContent = data.blok || '-';
                dSatuan.textContent = data.satuan || '';

                fJumlah.value = data.jumlah != null ? data.jumlah : '';
                fJumlah.disabled = modeLihat;

                btnSimpan.hidden = modeLihat;

                dlg.showModal();
            }

            // ---------- Dialog konfirmasi sebelum submit ----------
            var dlgKonfirmasi = document.getElementById('dlgKonfirmasi');
            var btnKonfirmasiSimpan = document.getElementById('btnKonfirmasiSimpan');
            var kNama = document.getElementById('kNamaPekerja');
            var kJenis = document.getElementById('kJenisPekerjaan');
            var kBlok = document.getElementById('kBlok');
            var kJumlah = document.getElementById('kJumlah');

            function mintaKonfirmasi() {
                if (!form.reportValidity()) return; // jumlah kosong / tidak valid -> browser tampilkan pesan bawaan

                kNama.textContent = dNama.textContent || '-';
                kJenis.textContent = dJenis.textContent || '-';
                kBlok.textContent = dBlok.textContent || '-';
                kJumlah.textContent = (fJumlah.value || '0') + (dSatuan.textContent ? ' ' + dSatuan.textContent : '');

                dlgKonfirmasi.showModal();
            }

            btnKonfirmasiSimpan.addEventListener('click', function() {
                dlgKonfirmasi.close();
                form.requestSubmit ? form.requestSubmit() : form.submit();
            });

            document.addEventListener('click', function(e) {
                var tombol = e.target.closest('[data-aksi]');
                if (!tombol) return;

                var aksi = tombol.dataset.aksi;

                if (aksi === 'minta-konfirmasi') {
                    mintaKonfirmasi();
                    return;
                }
                if (aksi === 'batal-konfirmasi') {
                    dlgKonfirmasi.close();
                    return;
                }
                if (aksi === 'tutup') {
                    tombol.closest('dialog').close();
                    return;
                }

                var baris = tombol.closest('.ih-item');
                if (!baris) return;

                var data = JSON.parse(baris.dataset.pekerja);
                bukaDialog(data, aksi === 'lihat');
            });

            dlg.addEventListener('click', function(e) {
                if (e.target === dlg) dlg.close();
            });
            dlgKonfirmasi.addEventListener('click', function(e) {
                if (e.target === dlgKonfirmasi) dlgKonfirmasi.close();
            });

            @if ($errors->any())
                dlg.showModal();
            @endif
        })();
    </script>
@endsection
