<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilKerja;
use App\Models\Blok;
use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;
use App\Exports\LaporanHasilKerjaExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanHasilKerjaController extends Controller
{
    public function index(Request $request)
    {
        // Query utama laporan
        $query = HasilKerja::with([
            'pekerja',
            'blok',
            'jenisPekerjaan',
            'mandor',
        ]);

        // Filter tanggal mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal',
                '>=',
                $request->tanggal_mulai
            );
        }

        // Filter tanggal akhir
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal',
                '<=',
                $request->tanggal_akhir
            );
        }

        // Filter blok
        if ($request->filled('blok')) {
            $query->where('blok_id', $request->blok);
        }

        // Filter jenis pekerjaan
        if ($request->filled('jenis')) {
            $query->where(
                'jenis_pekerjaan_id',
                $request->jenis
            );
        }

        // Pencarian nama pekerja
        if ($request->filled('q')) {
            $q = $request->q;

            $query->whereHas('pekerja', function ($p) use ($q) {
                $p->where('nama', 'like', "%{$q}%");
            });
        }

        // Ambil semua data yang sesuai filter untuk ringkasan
        $dataRingkasan = (clone $query)->get();

        // Total catatan hasil kerja
        $totalData = $dataRingkasan->count();

        // Total pekerja unik dalam hasil filter
        $totalPekerja = $dataRingkasan
            ->pluck('pekerja_id')
            ->unique()
            ->count();

        // Ambil semua jenis pekerjaan yang aktif
        $bloks = Blok::where('status', 'aktif')
            ->orderBy('nama_blok')
            ->get();

        $jenisPekerjaans = JenisPekerjaan::where('status', 'aktif')
            ->orderBy('jenis')
            ->get();

        // Buat ringkasan untuk setiap jenis pekerjaan aktif.
        // Jenis tanpa catatan tetap ditampilkan dengan total 0.
        $ringkasanProduksi = $jenisPekerjaans
            ->map(function ($jenis) use ($dataRingkasan) {
                $items = $dataRingkasan->where(
                    'jenis_pekerjaan_id',
                    $jenis->id
                );

                return [
                    'id' => $jenis->id,
                    'jenis' => $jenis->jenis,
                    'satuan' => $jenis->satuan ?? '-',
                    'total' => $items->sum('jumlah'),
                ];
            })
            ->values();

        // Tabel laporan dengan pagination
        $hasilKerjas = (clone $query)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.laporanhasilkerja', compact(
            'hasilKerjas',
            'totalData',
            'totalPekerja',
            'ringkasanProduksi',
            'bloks',
            'jenisPekerjaans'
        ));
    }

    public function unduh(Request $request)
    {
        $filters = $request->only([
            'tanggal_mulai',
            'tanggal_akhir',
            'blok',
            'jenis',
            'q',
        ]);

        return Excel::download(
            new LaporanHasilKerjaExport($filters),
            'laporan-hasil-kerja-' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }
}
