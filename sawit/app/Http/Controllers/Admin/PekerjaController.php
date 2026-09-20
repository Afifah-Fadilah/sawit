<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blok;
use App\Models\Pekerja;
use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;

class PekerjaController extends Controller
{
    public function index(Request $request)
    {
        $query = Pekerja::with('blok', 'jenisPekerjaan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $pekerjas = $query->orderBy('id')->paginate(10)->withQueryString();

        $totalPekerja = Pekerja::count();
        $pekerjaAktif = Pekerja::where('status', 'aktif')->count();
        $pekerjaNonaktif = Pekerja::where('status', 'nonaktif')->count();

        // Hanya jenis pekerjaan yang sudah diinput admin & berstatus aktif
        $jenisPekerjaanAktif = JenisPekerjaan::where('status', 'aktif')->get();

        // Tambahkan ini: ambil data blok untuk dropdown
        $blok = Blok::orderBy('nama_blok')->get();

        return view('admin.pekerja', compact(
            'pekerjas', 'totalPekerja', 'pekerjaAktif', 'pekerjaNonaktif', 'blok', 'jenisPekerjaanAktif'
        ));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nama'                => ['required', 'string', 'max:255'],
        'no_hp'               => ['required', 'string', 'max:20'],
        'jenis_pekerjaan_id'  => ['required', 'exists:jenis_pekerjaans,id'],
        'blok_id'             => ['nullable', 'exists:blok,id'],
        'status'              => ['required', 'in:aktif,nonaktif'],
    ]);

    $lastId = Pekerja::max('id') + 1;
    $validated['kode_pekerja'] = 'PKR' . str_pad($lastId, 4, '0', STR_PAD_LEFT);

    Pekerja::create($validated);

    return redirect()->route('admin.pekerja.index')
        ->with('status', 'Pekerja berhasil ditambahkan.');
}

public function update(Request $request, Pekerja $pekerja)
{
    $validated = $request->validate([
        'nama'                 => ['required', 'string', 'max:255'],
        'no_hp'                => ['required', 'string', 'max:20'],
        'jenis_pekerjaan_id'   => ['required', 'exists:jenis_pekerjaans,id'],
        'blok_id'              => ['nullable', 'exists:blok,id'],
        'status'               => ['required', 'in:aktif,nonaktif'],
    ]);

    $pekerja->update($validated);

    return redirect()->route('admin.pekerja.index')
        ->with('status', 'Data pekerja berhasil diperbarui.');
}    public function destroy(Pekerja $pekerja)
    {
        $pekerja->delete();

        return redirect()->route('admin.pekerja.index')
            ->with('status', 'Pekerja berhasil dihapus.');
    }
}