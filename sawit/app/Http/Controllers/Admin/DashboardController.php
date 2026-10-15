<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TarifUpah;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Tanggal untuk grafik (?tanggal=) dan untuk tabel Hasil Kerja (?tanggal_hasil=).
        // Keduanya independen; default-nya hari ini (timezone di config/app.php).
        $ambilTanggal = function (string $nama) {
            try {
                return request($nama)
                    ? Carbon::parse(request($nama))->toDateString()
                    : today()->toDateString();
            } catch (\Exception $e) {
                return today()->toDateString();
            }
        };

        $tanggal = $ambilTanggal('tanggal');
        $tanggalHasil = $ambilTanggal('tanggal_hasil');

        // Statistik utama
        $totalJenisPekerjaan = DB::table('jenis_pekerjaans')
            ->where('status', 'aktif')
            ->where('jenis', '!=', 'Mandor')
            ->count();

        $totalPekerja = DB::table('pekerjas')
            ->where('status', 'aktif')
            ->count();

        $totalBlok = DB::table('blok')
            ->where('status', 'aktif')
            ->count();

        // Total upah dari laporan upah yang sudah final
        $laporanFinal = DB::table('laporan_upahs')
            ->where('status', 'final')
            ->pluck('id');

        $totalUpah = DB::table('laporan_upah_items')
            ->whereIn('laporan_upah_id', $laporanFinal)
            ->sum('upah_bersih');

        // Rekap upah pekerja per jenis pekerjaan pada tanggal yang dipilih.
        // Dihitung sama seperti Laporan Upah: jumlah hasil kerja x tarif aktif.
        $tarif = TarifUpah::where('status', 'aktif')
            ->orderByDesc('id')
            ->get()
            ->unique('jenis_pekerjaan_id')
            ->keyBy('jenis_pekerjaan_id');

        $hasilPerJenis = DB::table('hasil_kerjas')
            ->join(
                'jenis_pekerjaans',
                'hasil_kerjas.jenis_pekerjaan_id',
                '=',
                'jenis_pekerjaans.id'
            )
            ->whereDate('hasil_kerjas.tanggal', $tanggal)
            ->where('jenis_pekerjaans.jenis', 'not like', '%mandor%')
            ->groupBy('hasil_kerjas.jenis_pekerjaan_id', 'jenis_pekerjaans.jenis')
            ->selectRaw(
                'hasil_kerjas.jenis_pekerjaan_id, jenis_pekerjaans.jenis, SUM(hasil_kerjas.jumlah) as total'
            )
            ->get();

        $rekapUpah = [];

        foreach ($hasilPerJenis as $baris) {
            $tarifJenis = optional($tarif->get($baris->jenis_pekerjaan_id))->tarif ?? 0;

            $rekapUpah[$baris->jenis] = (int) round((float) $baris->total * (float) $tarifJenis);
        }

        // Urutkan nama jenis pekerjaan
        ksort($rekapUpah);

        // Hasil kerja pada tanggal filter tabel (terpisah dari grafik)
        $hasilKerjaTerbaru = DB::table('hasil_kerjas')
            ->join('pekerjas', 'hasil_kerjas.pekerja_id', '=', 'pekerjas.id')
            ->join('blok', 'hasil_kerjas.blok_id', '=', 'blok.id')
            ->join(
                'jenis_pekerjaans',
                'hasil_kerjas.jenis_pekerjaan_id',
                '=',
                'jenis_pekerjaans.id'
            )
            ->where('jenis_pekerjaans.jenis', '!=', 'Mandor')
            ->whereDate('hasil_kerjas.tanggal', $tanggalHasil)
            ->orderByDesc('hasil_kerjas.id')
            ->limit(6)
            ->select(
                'hasil_kerjas.tanggal',
                'blok.nama_blok',
                'jenis_pekerjaans.jenis',
                'jenis_pekerjaans.satuan',
                'hasil_kerjas.jumlah',
                'pekerjas.nama as nama_pekerja'
            )
            ->get();

        return view('admin.dashboard', compact(
            'totalJenisPekerjaan',
            'totalPekerja',
            'totalBlok',
            'totalUpah',
            'rekapUpah',
            'hasilKerjaTerbaru',
            'tanggal',
            'tanggalHasil'
        ));
    }
}