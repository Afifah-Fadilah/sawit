<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Upah</title>
    <style>
        :root {
            --hijau: #5f6f52;
            --krem: #f2f0d9;
            --garis: #dcd9c4;
            --teks: #24291f;
            --abu: #6b6b60;

            font-family: 'Poppins', system-ui, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 28px;
            background: #eeeee8;
            color: var(--teks);
            font-size: 14px;
        }

        .toolbar {
            max-width: 800px;
            margin: 0 auto 18px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            background: var(--hijau);
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
        }

        .btn.secondary { background: white; color: var(--teks); border: 1px solid var(--garis); }

        .slip {
            max-width: 800px;
            margin: 0 auto 24px;
            padding: 34px 38px;
            background: white;
            border: 1px solid var(--garis);
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(30, 40, 20, .08);
            page-break-after: always;
            break-after: page;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--hijau);
        }

        .brand h1 { margin: 0; color: var(--hijau); font-size: 24px; letter-spacing: .5px; }
        .brand p { margin: 5px 0 0; color: var(--abu); font-size: 12px; }
        .brand-mark {
            display: grid;
            place-items: center;
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            border-radius: 12px;
            background: var(--krem);
            color: var(--hijau);
            font-size: 23px;
            font-weight: bold;
        }

        .heading { margin: 24px 0 18px; text-align: center; }
        .heading h2 { margin: 0; font-size: 20px; }
        .heading p { margin: 7px 0 0; color: var(--abu); font-size: 12px; }

        .identity {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 24px;
            padding: 16px;
            border-radius: 10px;
            background: #faf9f0;
            margin-bottom: 22px;
        }

        .identity .label { display: block; color: var(--abu); font-size: 11px; margin-bottom: 5px; }
        .identity strong { font-size: 13px; }

        table { width: 100%; border-collapse: collapse; margin: 12px 0 18px; }
        th, td { padding: 11px 10px; border-bottom: 1px solid #e9e7dc; text-align: left; vertical-align: top; }
        th { background: var(--krem); color: #4a4633; font-size: 12px; }
        td { font-size: 12px; }
        .right { text-align: right; white-space: nowrap; }
        .muted { color: var(--abu); }
        .summary { width: 100%; max-width: 360px; margin-left: auto; }
        .summary td { padding: 8px 10px; }
        .summary .total td { border-top: 2px solid var(--hijau); background: #f7f6e9; font-weight: bold; font-size: 14px; }
        .words { margin-top: 20px; padding: 13px 15px; border: 1px dashed var(--garis); border-radius: 8px; }
        .words span { display: block; margin-bottom: 5px; color: var(--abu); font-size: 11px; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 38px; text-align: center; }
        .signatures .space { height: 64px; }
        .signatures p { margin: 0; font-size: 12px; }
        .footer { margin-top: 26px; padding-top: 12px; border-top: 1px solid var(--garis); text-align: center; color: var(--abu); font-size: 10px; }

        @media (max-width: 600px) {
            body { padding: 12px; }
            .slip { padding: 22px 16px; }
            .identity { grid-template-columns: 1fr; }
            .brand h1 { font-size: 20px; }
            th, td { padding: 8px 6px; }
        }

        @media print {
            @page { size: A4; margin: 12mm; }
            body { padding: 0; background: white; }
            .toolbar { display: none !important; }
            .slip { max-width: none; margin: 0; padding: 8mm 6mm; border: 0; border-radius: 0; box-shadow: none; }
            .slip:last-child { page-break-after: auto; break-after: auto; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" type="button" onclick="window.print()">Cetak Slip</button>
        <button class="btn secondary" type="button" onclick="window.close()">Tutup</button>
    </div>

    @foreach ($slips as $it)
        @php
            $laporan = $it->laporan;
            $rp = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
            $tanggalPeriode = $laporan
                ? $laporan->tanggal_mulai->translatedFormat('d M Y') . ' – ' . $laporan->tanggal_selesai->translatedFormat('d M Y')
                : '-';
            $rincian = is_array($it->rincian) ? $it->rincian : (json_decode($it->rincian ?? '[]', true) ?: []);
            $rincianKasbon = is_array($it->rincian_kasbon) ? $it->rincian_kasbon : (json_decode($it->rincian_kasbon ?? '[]', true) ?: []);
        @endphp

        <main class="slip">
            <header class="brand">
                <div>
                    <h1>KELAPAKUY!</h1>
                </div>
                <div class="brand-mark">K</div>
            </header>

            <section class="heading">
                <h2>SLIP UPAH</h2>
                <p>Dokumen rincian upah per penerima</p>
            </section>

            <section class="identity">
                <div>
                    <span class="label">Nama Penerima</span>
                    <strong>{{ $it->nama }}</strong>
                </div>
                <div>
                    <span class="label">Kode Penerima</span>
                    <strong>{{ $it->kode ?: '-' }}</strong>
                </div>
                <div>
                    <span class="label">Kategori</span>
                    <strong>{{ ucfirst($it->tipe ?? '-') }}</strong>
                </div>
                <div>
                    <span class="label">Periode Upah</span>
                    <strong>{{ $laporan->periode_teks ?? $tanggalPeriode }}</strong>
                </div>
                <div>
                    <span class="label">Kode Laporan</span>
                    <strong>{{ $laporan->kode ?? '-' }}</strong>
                </div>
                <div>
                    <span class="label">Tanggal Cetak</span>
                    <strong>{{ $tanggalCetak->translatedFormat('d F Y, H:i') }}</strong>
                </div>
            </section>

            <h3 style="font-size:14px;margin:0;">Rincian Upah</h3>
            <table>
                <thead>
                    <tr>
                        <th>Uraian</th>
                        <th class="right">Jumlah</th>
                        <th class="right">Tarif</th>
                        <th class="right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rincian as $r)
                        <tr>
                            <td>
                                {{ $r['jenis'] ?? $r['label'] ?? 'Rincian upah' }}
                                @if (!empty($r['satuan']))
                                    <span class="muted">({{ $r['satuan'] }})</span>
                                @endif
                            </td>
                            <td class="right">{{ isset($r['jumlah']) ? rtrim(rtrim(number_format((float) $r['jumlah'], 2, ',', '.'), '0'), ',') : '-' }}</td>
                            <td class="right">{{ isset($r['tarif']) ? $rp($r['tarif']) : '-' }}</td>
                            <td class="right">{{ $rp($r['subtotal'] ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">Tidak ada rincian tambahan.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <table class="summary">
                <tbody>
                    <tr>
                        <td>Upah Kotor</td>
                        <td class="right">{{ $rp($it->upah_kotor) }}</td>
                    </tr>
                    <tr>
                        <td>Potongan Kasbon</td>
                        <td class="right">- {{ $rp($it->potongan_kasbon) }}</td>
                    </tr>
                    <tr class="total">
                        <td>Upah Bersih</td>
                        <td class="right">{{ $rp($it->upah_bersih) }}</td>
                    </tr>
                </tbody>
            </table>

            @if (count($rincianKasbon))
                <h3 style="font-size:14px;margin:22px 0 8px;">Rincian Potongan Kasbon</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal Kasbon</th>
                            <th>Tujuan</th>
                            <th class="right">Potongan</th>
                            <th class="right">Sisa Setelah Potongan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rincianKasbon as $kb)
                            <tr>
                                <td>{{ !empty($kb['tanggal']) ? \Illuminate\Support\Carbon::parse($kb['tanggal'])->translatedFormat('d M Y') : '-' }}</td>
                                <td>{{ $kb['tujuan'] ?? '-' }}</td>
                                <td class="right">{{ $rp($kb['nominal'] ?? 0) }}</td>
                                <td class="right">{{ $rp($kb['sisa_setelah'] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <footer class="footer">
                Slip ini dicetak dari sistem KelapaKuy! · Periode {{ $laporan->periode_teks ?? $tanggalPeriode }}
            </footer>
        </main>
    @endforeach
</body>
</html>
