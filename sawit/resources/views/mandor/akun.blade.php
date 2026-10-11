@extends('mandor.layouts.mandor')

@section('title', 'Profil Mandor')

@section('content')
    @php
        $title = 'Profil Mandor';
        $inisial = strtoupper(mb_substr(optional($mandor)->nama ?? 'M', 0, 1));
    @endphp

    <style>
        .ak {
            --ak-deep: #5f6f52;
            --ak-ink: #1f2419;
            --ak-card: #f2f0d9;
            --ak-tan: #b8926e;

            color: var(--ak-ink);
            font-family: 'Poppins', system-ui, sans-serif;
        }

        .ak,
        .ak *,
        .ak *::before,
        .ak *::after {
            box-sizing: border-box;
        }

        .ak-flash {
            margin: 0 0 14px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #e3f2d4;
            color: #23652d;
            font-size: 13px;
            font-weight: 500;
        }

        .ak-flash--err {
            background: #fbe1de;
            color: #b3382c;
        }

        .ak-card {
            padding: 18px 16px 20px;
            border-radius: 20px;
            background: var(--ak-card);
            box-shadow: 0 6px 16px rgba(95, 111, 82, .16);
        }

        .ak-profil {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 16px;
            margin-bottom: 14px;
            border-bottom: 1px solid #ddd9b8;
        }

        .ak-avatar {
            display: grid;
            place-items: center;
            flex: none;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: var(--ak-deep);
            color: #fefae0;
            font-size: 24px;
            font-weight: 700;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(95, 111, 82, .25);
        }

        .ak-nama {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #8a4b1e;
            word-break: break-word;
        }

        .ak-kode {
            display: inline-block;
            margin-top: 5px;
            padding: 2px 10px;
            border-radius: 6px;
            background: #d9d4b4;
            color: #4a4633;
            font-size: 10.5px;
            font-weight: 600;
        }

        .ak-label {
            display: block;
            margin: 0 0 6px;
            font-size: 12.5px;
            font-weight: 600;
        }

        .ak-group {
            margin-bottom: 14px;
        }

        .ak-field {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            min-height: 44px;
            padding: 0 14px;
            border: 1px solid #8f9a7e;
            border-radius: 10px;
            background: #e2ddbd;
            color: var(--ak-ink);
            font: inherit;
            font-size: 13px;
            text-align: left;
        }

        .ak-field svg {
            flex: none;
            color: #4a4633;
        }

        .ak-field__teks {
            flex: 1;
            min-width: 0;
            word-break: break-word;
            padding: 11px 0;
        }

        .ak-field--putih {
            background: #fbfaf1;
        }

        .ak-field--btn {
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .ak-field--btn:hover {
            background: #d9d4b4;
            border-color: var(--ak-deep);
        }

        .ak-field--btn:focus-visible {
            outline: 2px solid var(--ak-deep);
            outline-offset: 2px;
        }

        .ak-edit {
            display: grid;
            place-items: center;
            flex: none;
            width: 30px;
            height: 30px;
            padding: 0;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #4a4633;
            cursor: pointer;
        }

        .ak-edit:hover {
            background: #eeecd8;
        }

        .ak-edit:focus-visible {
            outline: 2px solid var(--ak-deep);
            outline-offset: 1px;
        }

        .ak-petunjuk {
            margin: 5px 2px 0;
            font-size: 11px;
            color: #7a7a6c;
        }

        .ak-aksi {
            display: flex;
            justify-content: flex-end;
            margin-top: 6px;
        }

        .ak-keluar {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            height: 42px;
            padding: 0 18px 0 6px;
            border: 0;
            border-radius: 12px;
            background: #6f9a6a;
            color: #fff;
            font: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(95, 111, 82, .3);
        }

        .ak-keluar:hover {
            background: #5f8a5a;
        }

        .ak-keluar__ikon {
            display: grid;
            place-items: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #fff;
            color: #d4382c;
        }

        .ak-kosong {
            padding: 30px 16px;
            text-align: center;
            font-size: 13px;
            color: #6b6b60;
        }

        /* Dialog */
        .ak-dlg {
            width: min(420px, calc(100vw - 32px));
            padding: 0;
            border: 0;
            border-radius: 20px;
            background: #fbfaf1;
            color: var(--ak-ink);
            box-shadow: 0 24px 60px rgba(30, 40, 20, .38);
            font-family: 'Poppins', system-ui, sans-serif;
            overflow-x: hidden;
        }

        .ak-dlg::backdrop {
            background: rgba(30, 38, 22, .5);
        }

        .ak-dlg__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px 14px;
            border-bottom: 1px solid #e8e6d0;
        }

        .ak-dlg__title {
            margin: 0;
            color: var(--ak-deep);
            font-size: 16px;
            font-weight: 700;
        }

        .ak-dlg__x {
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

        .ak-dlg__x:hover {
            background: #eeecd8;
        }

        .ak-dlg__body {
            padding: 18px 20px;
        }

        .ak-dlg__body--scroll {
            max-height: min(60vh, 460px);
            overflow-y: auto;
        }

        .ak-dlg__actions {
            display: flex;
            gap: 10px;
            padding: 4px 20px 18px;
        }

        .ak-btn {
            flex: 1;
            height: 44px;
            border-radius: 12px;
            border: 0;
            font: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .ak-btn--ghost {
            background: #fff;
            border: 1px solid #cfcdb4;
            color: var(--ak-ink);
        }

        .ak-btn--ghost:hover {
            background: #f3f2e6;
        }

        .ak-btn--solid {
            background: var(--ak-deep);
            color: #fff;
        }

        .ak-btn--solid:hover {
            background: #4d5b42;
        }

        .ak-input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1px solid #d5d3bb;
            border-radius: 12px;
            background: #fff;
            font: inherit;
            font-size: 14px;
            color: var(--ak-ink);
        }

        .ak-input:focus-visible {
            outline: 2px solid var(--ak-deep);
            outline-offset: 1px;
        }

        .ak-err {
            margin: 6px 0 0;
            color: #a33a2f;
            font-size: 11.5px;
        }

        /* Daftar pekerja per blok */
        .ak-blok {
            margin-bottom: 16px;
        }

        .ak-blok:last-child {
            margin-bottom: 0;
        }

        .ak-blok__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #eef1e0;
            margin-bottom: 6px;
        }

        .ak-blok__nama {
            font-size: 13px;
            font-weight: 700;
            color: var(--ak-deep);
        }

        .ak-blok__sub {
            font-size: 11px;
            color: #6b6b60;
        }

        .ak-pekerja {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 9px 4px;
            border-bottom: 1px solid #f0efe6;
        }

        .ak-pekerja:last-child {
            border-bottom: 0;
        }

        .ak-pekerja__kiri {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .ak-pekerja__avatar {
            display: grid;
            place-items: center;
            flex: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #eef1e0;
            color: var(--ak-deep);
        }

        .ak-pekerja__nama {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            word-break: break-word;
        }

        .ak-chip {
            flex: none;
            padding: 3px 10px;
            border-radius: 999px;
            background: #fdeecb;
            color: #8a5a1e;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .ak-kosong-kecil {
            padding: 8px 4px;
            font-size: 12px;
            color: #8a8a7a;
        }
    </style>

    <div class="ak" style="padding: 16px 4px 90px;">
        @if (session('success'))
            <p class="ak-flash" role="status">{{ session('success') }}</p>
        @endif
        @if ($errors->any())
            <p class="ak-flash ak-flash--err" role="alert">{{ $errors->first() }}</p>
        @endif

        @if (!$mandor)
            <div class="ak-card">
                <p class="ak-kosong">Akun ini belum terhubung dengan data mandor. Hubungi admin.</p>
            </div>
        @else
            <section class="ak-card" aria-label="Profil mandor">
                <div class="ak-profil">
                    <span class="ak-avatar" aria-hidden="true">{{ $inisial }}</span>
                    <div style="min-width:0;">
                        <p class="ak-nama">{{ strtoupper($mandor->nama) }}</p>
                        <span class="ak-kode">Kode: {{ $mandor->kode_mandor }}</span>
                    </div>
                </div>

                {{-- No Telepon (bisa diubah) --}}
                <div class="ak-group">
                    <span class="ak-label">No Telepon</span>
                    <div class="ak-field ak-field--putih">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        <span class="ak-field__teks">{{ $mandor->phone ?: '-' }}</span>
                        <button type="button" class="ak-edit" data-aksi="ubah-telepon" aria-label="Ubah nomor telepon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                <path d="m15 5 4 4" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Kategori Afdeling --}}
                <div class="ak-group">
                    <span class="ak-label">Kategori Afdeling</span>
                    <div class="ak-field">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M3 9h18" />
                            <path d="M3 15h18" />
                            <path d="M9 3v18" />
                            <path d="M15 3v18" />
                        </svg>
                        <span class="ak-field__teks">{{ $afdeling }}</span>
                    </div>
                </div>

                {{-- Blok yang dikelola: 1-2 blok, tidak harus berurutan --}}
                <div class="ak-group">
                    <span class="ak-label">Blok yang di kelola</span>
                    <div class="ak-field">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M3 9h18" />
                            <path d="M3 15h18" />
                            <path d="M9 3v18" />
                            <path d="M15 3v18" />
                        </svg>
                        <span class="ak-field__teks">{{ $namaBlok }}</span>
                    </div>
                </div>

                {{-- Total pekerja (klik untuk lihat daftar) --}}
                <div class="ak-group">
                    <span class="ak-label">Total Pekerja</span>
                    <button type="button" class="ak-field ak-field--btn" data-aksi="lihat-pekerja" aria-haspopup="dialog"
                        aria-label="Lihat daftar pekerja">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <span class="ak-field__teks">{{ number_format($totalPekerja, 0, ',', '.') }} Orang</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </button>
                    <p class="ak-petunjuk">Ketuk untuk melihat daftar pekerja.</p>
                </div>

                <div class="ak-aksi">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ak-keluar">
                            <span class="ak-keluar__ikon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" x2="9" y1="12" y2="12" />
                                </svg>
                            </span>
                            Keluar
                        </button>
                    </form>
                </div>
            </section>

            {{-- Dialog: ubah nomor telepon --}}
            <dialog id="dlgTelepon" class="ak ak-dlg" aria-labelledby="judulTelepon">
                <div class="ak-dlg__head">
                    <h2 id="judulTelepon" class="ak-dlg__title">Ubah No Telepon</h2>
                    <button type="button" class="ak-dlg__x" data-aksi="tutup" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('mandor.akun.phone') }}">
                    @csrf
                    @method('PUT')
                    <div class="ak-dlg__body">
                        <label class="ak-label" for="fTelepon">Nomor telepon baru</label>
                        <input class="ak-input" id="fTelepon" name="phone" type="tel" inputmode="tel"
                            maxlength="20" placeholder="0812-3456-7890" value="{{ old('phone', $mandor->phone) }}"
                            required>
                        @error('phone')
                            <p class="ak-err">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="ak-dlg__actions">
                        <button type="button" class="ak-btn ak-btn--ghost" data-aksi="tutup">Batal</button>
                        <button type="submit" class="ak-btn ak-btn--solid">Simpan</button>
                    </div>
                </form>
            </dialog>

            {{-- Dialog: daftar pekerja per blok --}}
            <dialog id="dlgPekerja" class="ak ak-dlg" aria-labelledby="judulPekerja">
                <div class="ak-dlg__head">
                    <h2 id="judulPekerja" class="ak-dlg__title">Daftar Pekerja ({{ $totalPekerja }})</h2>
                    <button type="button" class="ak-dlg__x" data-aksi="tutup" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <div class="ak-dlg__body ak-dlg__body--scroll">
                    @forelse ($bloks as $blok)
                        <div class="ak-blok">
                            <div class="ak-blok__head">
                                <span class="ak-blok__nama">{{ $blok->nama_blok }}</span>
                                <span class="ak-blok__sub">{{ $blok->afdeling }} &middot; {{ $blok->pekerjas->count() }}
                                    orang</span>
                            </div>

                            @forelse ($blok->pekerjas as $p)
                                <div class="ak-pekerja">
                                    <div class="ak-pekerja__kiri">
                                        <span class="ak-pekerja__avatar" aria-hidden="true">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="8" r="4" />
                                                <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                            </svg>
                                        </span>
                                        <p class="ak-pekerja__nama">{{ $p->nama }}</p>
                                    </div>
                                    <span class="ak-chip">{{ optional($p->jenisPekerjaan)->jenis ?? '-' }}</span>
                                </div>
                            @empty
                                <p class="ak-kosong-kecil">Belum ada pekerja aktif di blok ini.</p>
                            @endforelse
                        </div>
                    @empty
                        <p class="ak-kosong">Belum ada blok yang ditugaskan ke Anda.</p>
                    @endforelse
                </div>
                <div class="ak-dlg__actions">
                    <button type="button" class="ak-btn ak-btn--ghost" data-aksi="tutup">Tutup</button>
                </div>
            </dialog>
        @endif
    </div>

    <script>
        (function() {
            var dlgTelepon = document.getElementById('dlgTelepon');
            var dlgPekerja = document.getElementById('dlgPekerja');
            if (!dlgTelepon || !dlgPekerja) return;

            document.addEventListener('click', function(e) {
                var tombol = e.target.closest('[data-aksi]');
                if (!tombol) return;

                var aksi = tombol.dataset.aksi;
                if (aksi === 'tutup') {
                    tombol.closest('dialog').close();
                } else if (aksi === 'ubah-telepon') {
                    dlgTelepon.showModal();
                    var f = document.getElementById('fTelepon');
                    if (f) {
                        f.focus();
                        f.select();
                    }
                } else if (aksi === 'lihat-pekerja') {
                    dlgPekerja.showModal();
                }
            });

            [dlgTelepon, dlgPekerja].forEach(function(d) {
                d.addEventListener('click', function(e) {
                    if (e.target === d) d.close();
                });
            });

            @if ($errors->has('phone'))
                dlgTelepon.showModal();
            @endif
        })();
    </script>
@endsection
