<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use App\Models\TarifUpah;
use Illuminate\Http\Request;

class TarifUpahController extends Controller
{
    public function index(Request $request)
    {
        $query = TarifUpah::with('jenisPekerjaan');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('jenisPekerjaan', function ($w) use ($s) {
                $w->where('jenis', 'like', "%{$s}%")
                    ->orWhere('kode', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $tarifUpahs = $query->get();

        $totalJenis      = JenisPekerjaan::count();
        $totalTarifAktif = TarifUpah::where('status', 'aktif')->count();

        // Jenis pekerjaan yang belum punya tarif, untuk pilihan dropdown "Tambah Tarif Upah"
        $jenisTersedia = JenisPekerjaan::whereDoesntHave('tarifUpah')
            ->orderBy('jenis')
            ->get();

        return view('admin.tarifupah', compact(
            'tarifUpahs',
            'totalJenis',
            'totalTarifAktif',
            'jenisTersedia'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id|unique:tarif_upahs,jenis_pekerjaan_id',
            'tarif'  => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        TarifUpah::create($data);

        return back()->with('status', 'Tarif upah berhasil ditambahkan.');
    }

    public function update(Request $request, TarifUpah $tarifUpah)
    {
        $data = $request->validate([
            'tarif'  => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $tarifUpah->update($data);

        return back()->with('status', 'Tarif upah berhasil diperbarui.');
    }

    public function destroy(TarifUpah $tarifUpah)
    {
        $tarifUpah->delete();

        return back()->with('status', 'Tarif upah berhasil dihapus.');
    }
}