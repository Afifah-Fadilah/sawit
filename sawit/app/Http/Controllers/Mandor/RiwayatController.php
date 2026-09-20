<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\HasilKerja;
use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $mandor = Auth::user()->mandor;

        if (!$mandor) {
            return view('mandor.riwayat', [
                'riwayat'         => HasilKerja::whereRaw('1 = 0')->paginate(12),
                'bloks'           => collect(),
                'jenisPekerjaans' => collect(),
            ]);
        }

        $jadwal = $mandor->jadwal()->with('bloks')->first();
        $bloks  = $jadwal ? $jadwal->bloks : collect();

        $jenisPekerjaans = JenisPekerjaan::orderBy('jenis')->get();

        $riwayat = $this->queryTerfilter($request, $mandor)
            ->with(['pekerja', 'jenisPekerjaan', 'blok'])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('mandor.riwayat', compact('riwayat', 'bloks', 'jenisPekerjaans'));
    }

    /**
     * Unduh laporan riwayat sebagai CSV, mengikuti filter yang sedang aktif
     * (periode, blok, jenis pekerjaan, kata kunci) persis seperti yang tampil di layar.
     */
    public function unduh(Request $request)
    {
        $mandor = Auth::user()->mandor;

        if (!$mandor) {
            abort(404);
        }

        $data = $this->queryTerfilter($request, $mandor)
            ->with(['pekerja', 'jenisPekerjaan', 'blok'])
            ->orderByDesc('tanggal')
            ->get();

        $namaFile = 'riwayat-hasil-kerja-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '"',
        ];

        $callback = function () use ($data) {
            $out = fopen('php://output', 'w');
            // BOM supaya Excel baca UTF-8 dengan benar
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Tanggal', 'Nama Pekerja', 'Pekerjaan', 'Blok', 'Jumlah', 'Satuan', 'Keterangan']);

            foreach ($data as $r) {
                fputcsv($out, [
                    optional($r->tanggal)->format('d-m-Y'),
                    optional($r->pekerja)->nama,
                    optional($r->jenisPekerjaan)->jenis,
                    optional($r->blok)->nama_blok,
                    $r->jumlah,
                    optional($r->jenisPekerjaan)->satuan,
                    $r->keterangan,
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Query HasilKerja milik mandor yang login, dengan filter periode/blok/jenis/kata kunci
     * dari request. Dipakai bersama oleh index() dan unduh() supaya hasilnya selalu sinkron.
     */
    protected function queryTerfilter(Request $request, $mandor)
    {
        $query = HasilKerja::where('mandor_id', $mandor->id);

        switch ($request->get('periode', 'semua')) {
            case 'minggu':
                $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'bulan':
                $query->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year);
                break;
            case 'tahun':
                $query->whereYear('tanggal', now()->year);
                break;
            // 'semua' -> tanpa filter tanggal
        }

        if ($request->filled('blok_id')) {
            $query->where('blok_id', $request->blok_id);
        }

        if ($request->filled('jenis_pekerjaan_id')) {
            $query->where('jenis_pekerjaan_id', $request->jenis_pekerjaan_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('pekerja', function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%");
            });
        }

        return $query;
    }
}