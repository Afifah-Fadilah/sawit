{{-- resources/views/admin/laporanupah-semua-slip.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Upah - {{ $laporan->kode }}</title>
    <style>
        :root {
            --hijau: #5f6f52;
            --krem: #f2f0d9;
            --kepala: #e2d8bc;
            --garis: #dedbc7;
            --teks: #25291f;
            --abu: #6b6b60;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 28px; background: #efefe9; color: var(--teks);
            font-family: Arial, Helvetica, sans-serif; font-size: 13px;
        }
        .toolbar { max-width: 1100px; margin: 0 auto 16px; display: flex; justify-content: flex-end; gap: 8px; }
        .btn { border: 0; border-radius: 8px; padding: 10px 16px; background: var(--hijau); color: white; cursor: pointer; font-size: 13px; }
        .btn-light { background: white; border: 1px solid var(--garis); color: var(--teks); }
        .sheet {
            max-width: 1100px; margin: 0 auto; padding: 34px; background: white;
            border: 1px solid var(--garis); border-radius: 12px; box-shadow: 0 4px 18px rgba(30,40,20,.08);
        }
        .brand { display: flex; justify-content: space-between; align-items: center; gap: 20px; padding-bottom: 18px; border-bottom: 3px solid var(--hijau); }
        .brand h1 { margin: 0; color: var(--hijau); font-size: 23px; }
        .brand p { margin: 5px 0 0; color: var(--abu); font-size: 12px; }
        .brand-mark { display: grid; place-items: center; width: 48px; height: 48px; border-radius: 12px; background: var(--krem); color: var(--hijau); font-weight: bold; font-size: 22px; }
        .title { margin: 24px 0 18px; text-align: center; }
        .title h2 { margin: 0; font-size: 20px; }
        .title p { margin: 7px 0 0; color: var(--abu); font-size: 12px; }
        .meta { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; padding: 15px; margin-bottom: 22px; background: #faf9f0; border-radius: 10px; }
        .meta span { display: block; margin-bottom: 5px; color: var(--abu); font-size: 11px; }
        .meta strong { font-size: 13px; }
        h3 { margin: 20px 0 10px; color: var(--hijau); font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: var(--kepala); color: #4a4633; text-align: left; font-size: 12px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e9e7dc; vertical-align: top; }
        .right { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .sub { display: block; margin-top: 4px; color: var(--abu); font-size: 11px; line-height: 1.5; white-space: normal; }
        tfoot td { background: #f7f6e9; border-top: 2px solid var(--hijau); font-weight: bold; }
        .note { margin-top: 18px; color: var(--abu); font-size: 11px; }
        @media (max-width: 700px) {
            body { padding: 10px; }
            .sheet { padding: 16px; overflow-x: auto; }
            .meta { grid-template-columns: 1fr; }
            table { min-width: 760px; }
        }
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { padding: 0; background: white; }
            .toolbar { display: none !important; }
            .sheet { max-width: none; margin: 0; padding: 0; border: 0; border-radius: 0; box-shadow: none; overflow: visible; }
            table { min-width: 0; }
            tr { break-inside: avoid; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" type="button" onclick="window.print()">Cetak Rekap Upah</button>
        <button class="btn btn-light" type="button" onclick="window.close()">Tutup</button>
    </div>

    @php
        $rp = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
        $kotorTotal = (int) $items->sum('upah_kotor');
        $potonganTotal = (int) $items->sum('potongan_kasbon');
        $bersihTotal = (int) $items->sum('upah_bersih');
    @endphp

    <main class="sheet">
        <header class="brand">
            <div>
                <h1>KELAPAKUY!</h1>
                <p>Rekap pembayaran upah pekerja dan mandor</p>
            </div>
            <div class="brand-mark">K</div>
        </header>

        <section class="title">
            <h2>REKAPITULASI UPAH PER PERIODE</h2>
            <p>Daftar upah seluruh pekerja dan mandor</p>
        </section>

        <section class="meta">
            <div>
                <span>Periode</span>
                <strong>{{ $laporan->tanggal_mulai->translatedFormat('d M Y') }} – {{ $laporan->tanggal_selesai->translatedFormat('d M Y') }}</strong>
            </div>
            <div>
                <span>Kode Laporan</span>
                <strong>{{ $laporan->kode }}</strong>
            </div>
            <div>
                <span>Status Laporan</span>
                <strong>{{ $laporan->isFinal() ? 'Final' : 'Draft' }}</strong>
            </div>
        </section>

        @php
            $pekerja = $items->where('tipe', 'pekerja')->values();
            $mandor = $items->where('tipe', 'mandor')->values();

            $jumlahKotorPekerja = (int) $pekerja->sum('upah_kotor');
            $jumlahPotonganPekerja = (int) $pekerja->sum('potongan_kasbon');
            $jumlahBersihPekerja = (int) $pekerja->sum('upah_bersih');

            $jumlahKotorMandor = (int) $mandor->sum('upah_kotor');
            $jumlahPotonganMandor = (int) $mandor->sum('potongan_kasbon');
            $jumlahBersihMandor = (int) $mandor->sum('upah_bersih');
        @endphp

        <h3>Rekap Upah Pekerja</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 42px;">No.</th>
                    <th>Nama Pekerja</th>
                    <th>Jenis Pekerjaan</th>
                    <th class="right">Upah Hasil Kerja</th>
                    <th class="right">Potongan Kasbon</th>
                    <th class="right">Upah Bersih</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pekerja as $it)
                    @php
                        $rincian = is_array($it->rincian)
                            ? $it->rincian
                            : (json_decode($it->rincian ?? '[]', true) ?: []);
                        $jenisPekerjaan = collect($rincian)->pluck('jenis')->filter()->unique()->values();
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $it->nama }}</strong><span class="sub">{{ $it->kode ?: '-' }}</span></td>
                        <td>
                            {{ $jenisPekerjaan->isNotEmpty() ? $jenisPekerjaan->implode(', ') : 'Pekerja' }}
                            @if (count($rincian))
                                <span class="sub">
                                    @foreach ($rincian as $r)
                                        @if (!empty($r['jenis']))
                                            {{ $r['jenis'] }}: {{ rtrim(rtrim(number_format((float) ($r['jumlah'] ?? 0), 2, ',', '.'), '0'), ',') }} {{ $r['satuan'] ?? '' }} × {{ $rp($r['tarif'] ?? 0) }} = {{ $rp($r['subtotal'] ?? 0) }}@if (!$loop->last); @endif
                                        @endif
                                    @endforeach
                                </span>
                            @endif
                        </td>
                        <td class="right">{{ $rp($it->upah_kotor) }}</td>
                        <td class="right">{{ $rp($it->potongan_kasbon) }}</td>
                        <td class="right"><strong>{{ $rp($it->upah_bersih) }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center">Belum ada data upah pekerja pada periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot><tr><td colspan="3">Total Upah Pekerja</td><td class="right">{{ $rp($jumlahKotorPekerja) }}</td><td class="right">{{ $rp($jumlahPotonganPekerja) }}</td><td class="right">{{ $rp($jumlahBersihPekerja) }}</td></tr></tfoot>
        </table>

        <h3>Rekap Upah Mandor</h3>
        <table>
            <thead><tr><th style="width: 42px;">No.</th><th>Nama Mandor</th><th>Peran</th><th class="right">Upah Kotor</th><th class="right">Potongan Kasbon</th><th class="right">Upah Bersih</th></tr></thead>
            <tbody>
                @forelse ($mandor as $it)
                    <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $it->nama }}</strong><span class="sub">{{ $it->kode ?: '-' }}</span></td><td>Mandor</td><td class="right">{{ $rp($it->upah_kotor) }}</td><td class="right">{{ $rp($it->potongan_kasbon) }}</td><td class="right"><strong>{{ $rp($it->upah_bersih) }}</strong></td></tr>
                @empty
                    <tr><td colspan="6" class="center">Belum ada data upah mandor pada periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot><tr><td colspan="3">Total Upah Mandor</td><td class="right">{{ $rp($jumlahKotorMandor) }}</td><td class="right">{{ $rp($jumlahPotonganMandor) }}</td><td class="right">{{ $rp($jumlahBersihMandor) }}</td></tr></tfoot>
        </table>

        <h3>Total Keseluruhan Upah Pekerja dan Mandor</h3>
        <table>
            <thead><tr><th>Keterangan</th><th class="right">Upah Kotor</th><th class="right">Potongan Kasbon</th><th class="right">Upah Bersih</th></tr></thead>
            <tbody>
                <tr><td>Total Upah Pekerja</td><td class="right">{{ $rp($jumlahKotorPekerja) }}</td><td class="right">{{ $rp($jumlahPotonganPekerja) }}</td><td class="right">{{ $rp($jumlahBersihPekerja) }}</td></tr>
                <tr><td>Total Upah Mandor</td><td class="right">{{ $rp($jumlahKotorMandor) }}</td><td class="right">{{ $rp($jumlahPotonganMandor) }}</td><td class="right">{{ $rp($jumlahBersihMandor) }}</td></tr>
            </tbody>
            <tfoot><tr><td>TOTAL KESELURUHAN</td><td class="right">{{ $rp($jumlahKotorPekerja + $jumlahKotorMandor) }}</td><td class="right">{{ $rp($jumlahPotonganPekerja + $jumlahPotonganMandor) }}</td><td class="right">{{ $rp($jumlahBersihPekerja + $jumlahBersihMandor) }}</td></tr></tfoot>
        </table>

        <p class="note">
            Dicetak pada {{ $tanggalCetak->translatedFormat('d F Y, H:i') }}.
            Potongan kasbon mengikuti hasil perhitungan pada laporan periode ini.
        </p>
    </main>
</body>
</html>
