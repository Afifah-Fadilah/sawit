{{-- resources/views/admin/pekerja.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Pekerja')

@section('content')
    @php
        $stats = [
            [
                'label' => 'Total Pekerja',
                'value' => number_format($totalPekerja, 0, ',', '.'),
                'unit' => 'Data',
                'icon' =>
                    '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            ],
            [
                'label' => 'Pekerja Aktif',
                'value' => number_format($pekerjaAktif, 0, ',', '.'),
                'unit' => 'Orang',
                'icon' =>
                    '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m17 11 2 2 4-4"/>',
            ],
            [
                'label' => 'Nonaktif',
                'value' => number_format($pekerjaNonaktif, 0, ',', '.'),
                'unit' => 'Orang',
                'icon' =>
                    '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" x2="22" y1="8" y2="13"/><line x1="22" x2="17" y1="8" y2="13"/>',
            ],
        ];

        $sedangCari = request()->filled('search') || (request()->filled('status') && request('status') !== 'semua');

        $halaman = $pekerjas->currentPage();
        $terakhir = $pekerjas->lastPage();
        $mulai = max(1, $halaman - 2);
        $akhir = min($terakhir, $halaman + 2);
    @endphp

    <style>
        .kp {
            --kp-deep: #5f6f52;
            --kp-ink: #1f2419;
            --kp-card: #f2f0d9;
            --kp-tan: #b8926e;
            --kp-head: #e2d8bc;

            color: var(--kp-ink);
            font-family: 'Poppins', system-ui, sans-serif;
        }

        .kp,
        .kp *,
        .kp *::before,
        .kp *::after {
            box-sizing: border-box;
        }

        .kp-card {
            border-radius: 20px;
            background: var(--kp-card);
            box-shadow: 0 6px 16px rgba(95, 111, 82, .16);
        }

        .kp-flash {
            margin: 0 0 18px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #e3f2d4;
            color: #23652d;
            font-size: 13px;
            font-weight: 500;
        }

        .kp-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 240px));
            gap: 20px;
            margin-bottom: 22px;
        }

        .kp-stat {
            display: grid;
            grid-template-columns: auto 1fr;
            column-gap: 14px;
            align-items: start;
            padding: 20px 18px;
        }

        .kp-stat__icon {
            display: grid;
            place-items: center;
            grid-row: 1 / span 3;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--kp-tan);
            color: var(--kp-card);
        }

        .kp-stat__label {
            margin: 0;
            font-size: 11.5px;
            font-weight: 600;
        }

        .kp-stat__value {
            margin: 4px 0 0;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
        }

        .kp-stat__unit {
            margin: 2px 0 0;
            font-size: 11.5px;
            font-weight: 500;
        }

        .kp-panel {
            padding: 18px 16px 16px;
        }

        .kp-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .kp-title {
            margin: 0;
            color: var(--kp-deep);
            font-size: 17px;
            font-weight: 600;
        }

        .kp-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .kp-search {
            position: relative;
        }

        .kp-search svg {
            position: absolute;
            top: 50%;
            left: 12px;
            color: #7a7a6c;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .kp-input,
        .kp-select {
            height: 38px;
            border: 1px solid #d5d3bb;
            border-radius: 10px;
            background: #fff;
            color: var(--kp-ink);
            font: inherit;
            font-size: 12px;
        }

        .kp-input {
            width: 230px;
            padding: 0 12px 0 34px;
        }

        .kp-select {
            min-width: 160px;
            padding: 0 38px 0 14px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235f6f52' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            cursor: pointer;
        }

        .kp-input:focus-visible,
        .kp-select:focus-visible,
        .kp-btn:focus-visible,
        .kp-icon:focus-visible,
        .kp-page:focus-visible {
            outline: 2px solid var(--kp-deep);
            outline-offset: 2px;
        }

        .kp-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 38px;
            padding: 0 18px;
            border: 0;
            border-radius: 10px;
            background: var(--kp-deep);
            color: #fff;
            font: inherit;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color .15s ease;
        }

        .kp-btn:hover {
            background: #4d5b42;
        }

        .kp-btn[hidden] {
            display: none;
        }

        .kp-btn--ghost {
            border: 1px solid #cfcdb4;
            background: #fff;
            color: var(--kp-ink);
        }

        .kp-btn--ghost:hover {
            background: #f3f2e6;
        }

        .kp-btn--danger {
            background: #b3382c;
        }

        .kp-btn--danger:hover {
            background: #952d23;
        }

        .kp-sr {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            white-space: nowrap;
        }

        .kp-wrap {
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }

        .kp-scroll {
            overflow-x: auto;
        }

        .kp-table {
            width: 100%;
            min-width: 780px;
            border-collapse: collapse;
            font-size: 12px;
        }

        .kp-table th {
            padding: 14px 16px;
            background: var(--kp-head);
            color: #4a4633;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
        }

        .kp-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #ecebe3;
            white-space: nowrap;
        }

        .kp-table .kp-c {
            text-align: center;
        }

        .kp-empty {
            padding: 34px 16px !important;
            color: #6b6b60;
            text-align: center;
            white-space: normal !important;
        }

        .kp-badge {
            display: inline-block;
            min-width: 54px;
            padding: 3px 12px;
            border-radius: 6px;
            font-size: 10.5px;
            font-weight: 600;
            text-align: center;
        }

        .kp-badge--aktif {
            background: #e3f4d6;
            color: #23652d;
        }

        .kp-badge--nonaktif {
            background: #eeeeea;
            color: #5b5b52;
        }

        .kp-actions {
            display: inline-flex;
            gap: 6px;
        }

        .kp-icon {
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            padding: 0;
            border: 1px solid #d3d2c8;
            border-radius: 8px;
            background: #fff;
            color: var(--kp-ink);
            cursor: pointer;
            transition: background-color .15s ease;
        }

        .kp-icon:hover {
            background: #f1f0e6;
        }

        .kp-icon--del {
            border-color: #efc5bf;
            background: #fdf1ef;
            color: #b3382c;
        }

        .kp-icon--del:hover {
            background: #fadfdb;
        }

        .kp-foot {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 18px;
            font-size: 11.5px;
        }

        .kp-pager {
            display: flex;
            gap: 4px;
        }

        .kp-page {
            display: grid;
            place-items: center;
            min-width: 26px;
            height: 26px;
            padding: 0 6px;
            border: 1px solid #d3d2c8;
            border-radius: 6px;
            background: #fff;
            color: var(--kp-ink);
            font-size: 11.5px;
            text-decoration: none;
        }

        a.kp-page:hover {
            background: #f1f0e6;
        }

        .kp-page--on {
            border-color: var(--kp-deep);
            background: var(--kp-deep);
            color: #fff;
        }

        .kp-page--off {
            opacity: .4;
        }

        .kp-dlg {
            width: min(560px, calc(100vw - 32px));
            padding: 0;
            border: 0;
            border-radius: 20px;
            background: #fbfaf1;
            color: var(--kp-ink);
            box-shadow: 0 24px 60px rgba(30, 40, 20, .38);
            font-family: 'Poppins', system-ui, sans-serif;
            overflow-x: hidden;
        }

        .kp-dlg--sm {
            width: min(420px, calc(100vw - 32px));
        }

        .kp-dlg::backdrop {
            background: rgba(30, 38, 22, .5);
        }

        .kp-dlg__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 22px 28px 18px;
            border-bottom: 1px solid #e8e6d0;
        }

        .kp-dlg__title {
            margin: 0;
            color: var(--kp-deep);
            font-size: 18px;
            font-weight: 600;
        }

        .kp-dlg__x {
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

        .kp-dlg__x:hover {
            background: #eeecd8;
        }

        .kp-dlg__x:focus-visible {
            outline: 2px solid var(--kp-deep);
            outline-offset: 2px;
        }

        .kp-dlg__body {
            padding: 24px 28px 26px;
        }

        .kp-dlg__text {
            margin: 0;
            font-size: 13.5px;
            line-height: 1.7;
        }

        .kp-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 22px;
        }

        .kp-field {
            min-width: 0;
        }

        .kp-field--full {
            grid-column: 1 / -1;
        }

        .kp-label {
            display: block;
            margin-bottom: 7px;
            font-size: 12.5px;
            font-weight: 500;
        }

        .kp-field .kp-input,
        .kp-field .kp-select {
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

        .kp-field .kp-input::placeholder {
            color: #a3a293;
        }

        .kp-field .kp-input:focus-visible,
        .kp-field .kp-select:focus-visible {
            outline: none;
            border-color: var(--kp-deep);
            box-shadow: 0 0 0 3px rgba(95, 111, 82, .2);
        }

        .kp-field .kp-select {
            padding-right: 38px;
        }

        .kp-field .kp-input:disabled,
        .kp-field .kp-select:disabled {
            background-color: #f3f2e6;
            color: #3f4a35;
            -webkit-text-fill-color: #3f4a35;
            opacity: 1;
        }

        .kp-err {
            margin: 6px 0 0;
            color: #a33a2f;
            font-size: 11.5px;
        }

        .kp-dlg__actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 28px;
            border-top: 1px solid #e8e6d0;
            background: #f6f5e6;
        }

        .kp-dlg__actions .kp-btn {
            height: 40px;
            padding: 0 22px;
        }

        @media (max-width: 520px) {
            .kp-form {
                grid-template-columns: minmax(0, 1fr);
                gap: 16px;
            }

            .kp-dlg__head {
                padding: 18px 20px 14px;
            }

            .kp-dlg__body {
                padding: 20px;
            }

            .kp-dlg__actions {
                padding: 14px 20px;
            }
        }
    </style>

    <div class="kp">
        @if (session('status'))
            <p class="kp-flash" role="status">{{ session('status') }}</p>
        @endif

        {{-- Kartu statistik --}}
        <section class="kp-stats" aria-label="Ringkasan pekerja">
            @foreach ($stats as $stat)
                <article class="kp-card kp-stat">
                    <span class="kp-stat__icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            {!! $stat['icon'] !!}
                        </svg>
                    </span>
                    <h2 class="kp-stat__label">{{ $stat['label'] }}</h2>
                    <p class="kp-stat__value">{{ $stat['value'] }}</p>
                    <p class="kp-stat__unit">{{ $stat['unit'] }}</p>
                </article>
            @endforeach
        </section>

        {{-- Daftar pekerja --}}
        <section class="kp-card kp-panel" aria-labelledby="judulDaftar">
            <div class="kp-head">
                <h2 id="judulDaftar" class="kp-title">Daftar Pekerja</h2>

                <div class="kp-toolbar">
                    <form method="get" action="{{ url('admin/pekerja') }}" class="kp-toolbar" role="search">
                        <div class="kp-search">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                            <input type="search" name="search" value="{{ request('search') }}" class="kp-input"
                                placeholder="Cari nama atau no. HP..." aria-label="Cari pekerja">
                        </div>

                        <select name="status" class="kp-select" aria-label="Filter status" onchange="this.form.submit()">
                            <option value="semua"
                                {{ !request('status') || request('status') === 'semua' ? 'selected' : '' }}>Semua Status
                            </option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif
                            </option>
                        </select>

                        <button type="submit" class="kp-sr">Cari</button>
                    </form>

                    <button type="button" class="kp-btn" data-aksi="tambah">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Tambah Pekerja
                    </button>
                </div>
            </div>

            <div class="kp-wrap">
                <div class="kp-scroll">
                    <table class="kp-table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama</th>
                                <th scope="col">No. HP</th>
                                <th scope="col">Jenis Pekerjaan</th>
                                <th scope="col">Blok Tugas</th>
                                <th scope="col" class="kp-c">Status</th>
                                <th scope="col" class="kp-c">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pekerjas as $pekerja)
                                <tr
                                    data-pekerja="{{ json_encode([
                                        'id' => $pekerja->id,
                                        'kode_pekerja' => $pekerja->kode_pekerja,
                                        'nama' => $pekerja->nama,
                                        'no_hp' => $pekerja->no_hp,
                                        'jenis_pekerjaan_id' => $pekerja->jenis_pekerjaan_id,
                                        'blok_id' => $pekerja->blok_id,
                                        'status' => $pekerja->status,
                                    ]) }}">
                                    <td>{{ $pekerjas->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div style="font-weight:600;">{{ $pekerja->nama }}</div>
                                        <div style="font-size:10.5px; color:#9a9a8a;">Kode: {{ $pekerja->kode_pekerja }}
                                        </div>
                                    </td>
                                    <td>{{ $pekerja->no_hp }}</td>
                                    <td>{{ $pekerja->jenis_pekerjaan }}</td>
                                    <td>{{ $pekerja->blok->nama_blok ?? '-' }}</td>
                                    <td class="kp-c">
                                        <span
                                            class="kp-badge kp-badge--{{ $pekerja->status }}">{{ ucfirst($pekerja->status) }}</span>
                                    </td>
                                    <td class="kp-c">
                                        <span class="kp-actions">
                                            <button type="button" class="kp-icon" data-aksi="lihat"
                                                aria-label="Lihat {{ $pekerja->nama }}">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            </button>
                                            <button type="button" class="kp-icon" data-aksi="ubah"
                                                aria-label="Ubah {{ $pekerja->nama }}">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    <path d="m15 5 4 4" />
                                                </svg>
                                            </button>
                                            <button type="button" class="kp-icon kp-icon--del" data-aksi="hapus"
                                                aria-label="Hapus {{ $pekerja->nama }}">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
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
                                    <td colspan="7" class="kp-empty">
                                        @if ($sedangCari)
                                            Tidak ada pekerja yang cocok dengan pencarian atau filter.
                                        @else
                                            Belum ada data pekerja. Klik "Tambah Pekerja" untuk menambahkan.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="kp-foot">
                    <span>
                        @if ($pekerjas->total() > 0)
                            Menampilkan {{ $pekerjas->firstItem() }} - {{ $pekerjas->lastItem() }} dari
                            {{ $pekerjas->total() }} data
                        @else
                            Menampilkan 0 data
                        @endif
                    </span>

                    @if ($pekerjas->hasPages())
                        <nav class="kp-pager" aria-label="Halaman">
                            @if ($pekerjas->onFirstPage())
                                <span class="kp-page kp-page--off" aria-hidden="true">&lsaquo;</span>
                            @else
                                <a class="kp-page" href="{{ $pekerjas->previousPageUrl() }}"
                                    aria-label="Halaman sebelumnya">&lsaquo;</a>
                            @endif

                            @for ($i = $mulai; $i <= $akhir; $i++)
                                @if ($i === $halaman)
                                    <span class="kp-page kp-page--on" aria-current="page">{{ $i }}</span>
                                @else
                                    <a class="kp-page" href="{{ $pekerjas->url($i) }}"
                                        aria-label="Halaman {{ $i }}">{{ $i }}</a>
                                @endif
                            @endfor

                            @if ($pekerjas->hasMorePages())
                                <a class="kp-page" href="{{ $pekerjas->nextPageUrl() }}"
                                    aria-label="Halaman berikutnya">&rsaquo;</a>
                            @else
                                <span class="kp-page kp-page--off" aria-hidden="true">&rsaquo;</span>
                            @endif
                        </nav>
                    @endif
                </div>
            </div>
        </section>
    </div>

    {{-- Dialog tambah / ubah / lihat --}}
    <dialog id="dlgPekerja" class="kp kp-dlg" aria-labelledby="judulPekerja">
        <div class="kp-dlg__box">
            <div class="kp-dlg__head">
                <h2 id="judulPekerja" class="kp-dlg__title">Tambah Pekerja</h2>
                <button type="button" class="kp-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <form id="formPekerja" method="post" action="{{ url('admin/pekerja') }}"
                data-url-dasar="{{ url('admin/pekerja') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="methodPekerja" disabled>

                <div class="kp-dlg__body">
                    <div class="kp-form">
                        <div class="kp-field kp-field--full">
                            <label class="kp-label" for="fNama">Nama</label>
                            <input class="kp-input" id="fNama" name="nama" type="text" maxlength="255"
                                placeholder="Contoh: Budi Santoso" value="{{ old('nama') }}" required>
                            @error('nama')
                                <p class="kp-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kp-field">
                            <label class="kp-label" for="fNoHp">No. HP</label>
                            <input class="kp-input" id="fNoHp" name="no_hp" type="text" maxlength="20"
                                placeholder="Contoh: 0812-3456-7890" value="{{ old('no_hp') }}" required>
                            @error('no_hp')
                                <p class="kp-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kp-field">
                            <label class="kp-label" for="fJenis">Jenis Pekerjaan</label>
                            <select class="kp-select" id="fJenis" name="jenis_pekerjaan_id" required>
                                <option value="">Pilih Jenis Pekerjaan</option>
                                @foreach ($jenisPekerjaanAktif as $jp)
                                    <option value="{{ $jp->id }}"
                                        {{ (string) old('jenis_pekerjaan_id') === (string) $jp->id ? 'selected' : '' }}>
                                        {{ $jp->jenis }}</option>
                                @endforeach
                            </select>
                            @error('jenis_pekerjaan_id')
                                <p class="kp-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kp-field">
                            <label class="kp-label" for="fBlok">Blok Tugas</label>
                            <select class="kp-select" id="fBlok" name="blok_id">
                                <option value="">-</option>
                                @foreach ($blok as $b)
                                    <option value="{{ $b->id }}"
                                        {{ (string) old('blok_id') === (string) $b->id ? 'selected' : '' }}>
                                        {{ $b->nama_blok }}</option>
                                @endforeach
                            </select>
                            @error('blok_id')
                                <p class="kp-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="kp-field">
                            <label class="kp-label" for="fStatus">Status</label>
                            <select class="kp-select" id="fStatus" name="status" required>
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                            @error('status')
                                <p class="kp-err">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="kp-dlg__actions">
                    <button type="button" class="kp-btn kp-btn--ghost" id="btnTutupPekerja"
                        data-aksi="tutup">Batal</button>
                    <button type="submit" class="kp-btn" id="btnSimpanPekerja">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Dialog konfirmasi hapus --}}
    <dialog id="dlgHapus" class="kp kp-dlg kp-dlg--sm" aria-labelledby="judulHapus">
        <div class="kp-dlg__box">
            <div class="kp-dlg__head">
                <h2 id="judulHapus" class="kp-dlg__title">Hapus Pekerja?</h2>
                <button type="button" class="kp-dlg__x" data-aksi="tutup" aria-label="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <form id="formHapus" method="post" action="{{ url('admin/pekerja') }}">
                @csrf
                @method('DELETE')

                <div class="kp-dlg__body">
                    <p class="kp-dlg__text">
                        Data <strong id="namaHapus"></strong> akan dihapus permanen dan tidak bisa dikembalikan.
                    </p>
                </div>

                <div class="kp-dlg__actions">
                    <button type="button" class="kp-btn kp-btn--ghost" data-aksi="tutup">Batal</button>
                    <button type="submit" class="kp-btn kp-btn--danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        (function() {
            var dlgPekerja = document.getElementById('dlgPekerja');
            var dlgHapus = document.getElementById('dlgHapus');
            var form = document.getElementById('formPekerja');
            var formHapus = document.getElementById('formHapus');
            var judul = document.getElementById('judulPekerja');
            var methodInput = document.getElementById('methodPekerja');
            var btnSimpan = document.getElementById('btnSimpanPekerja');
            var btnTutup = document.getElementById('btnTutupPekerja');
            var namaHapus = document.getElementById('namaHapus');
            var urlDasar = form.dataset.urlDasar;
            var fields = ['nama', 'no_hp', 'jenis_pekerjaan_id', 'blok_id', 'status'];
            var judulMode = {
                tambah: 'Tambah Pekerja',
                ubah: 'Ubah Pekerja',
                lihat: 'Detail Pekerja'
            };

            function bukaForm(mode, data) {
                var lihat = mode === 'lihat';

                judul.textContent = judulMode[mode];
                methodInput.disabled = mode !== 'ubah';
                form.action = mode === 'ubah' ? urlDasar + '/' + data.id : urlDasar;

                form.querySelectorAll('.kp-err').forEach(function(el) {
                    el.remove();
                });
                fields.forEach(function(f) {
                    var nilai = data ? data[f] : (f === 'status' ? 'aktif' : '');
                    form.elements[f].value = nilai == null ? '' : nilai;
                });

                fields.forEach(function(f) {
                    form.elements[f].disabled = lihat;
                });
                btnSimpan.hidden = lihat;
                btnTutup.textContent = lihat ? 'Tutup' : 'Batal';
                dlgPekerja.showModal();
            }

            function bukaHapus(data) {
                formHapus.action = urlDasar + '/' + data.id;
                namaHapus.textContent = data.nama;
                dlgHapus.showModal();
            }

            document.addEventListener('click', function(e) {
                var tombol = e.target.closest('[data-aksi]');
                if (!tombol) return;

                var aksi = tombol.dataset.aksi;
                if (aksi === 'tutup') {
                    tombol.closest('dialog').close();
                    return;
                }
                if (aksi === 'tambah') {
                    bukaForm('tambah', null);
                    return;
                }

                var baris = tombol.closest('tr');
                var data = baris ? JSON.parse(baris.dataset.pekerja) : null;
                if (!data) return;

                if (aksi === 'hapus') bukaHapus(data);
                else bukaForm(aksi, data);
            });

            [dlgPekerja, dlgHapus].forEach(function(d) {
                d.addEventListener('click', function(e) {
                    if (e.target === d) d.close();
                });
            });

            @if ($errors->any())
                bukaForm('tambah', null);
            @endif
        })();
    </script>
@endsection
