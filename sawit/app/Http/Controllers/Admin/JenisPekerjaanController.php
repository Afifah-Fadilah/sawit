<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;

class JenisPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisPekerjaan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $jenisPekerjaans = $query->orderBy('id')->get();

        $totalJenis = JenisPekerjaan::count();
        $jenisAktif = JenisPekerjaan::where('status', 'aktif')->count();
        $jenisNonaktif = JenisPekerjaan::where('status', 'nonaktif')->count();

        // Jenis yang belum dipakai, buat opsi dropdown saat tambah baru
        $jenisTerpakai = JenisPekerjaan::pluck('jenis')->toArray();
        $jenisTersedia = array_values(array_diff(JenisPekerjaan::JENIS_OPSI, $jenisTerpakai));

        return view('admin.jenispekerjaan', compact(
            'jenisPekerjaans', 'totalJenis', 'jenisAktif', 'jenisNonaktif', 'jenisTersedia'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis'       => ['required', 'in:' . implode(',', JenisPekerjaan::JENIS_OPSI), 'unique:jenis_pekerjaans,jenis'],
            'satuan'      => ['required', 'string', 'max:50'],
            'keterangan'  => ['nullable', 'string'],
            'warna'       => ['nullable', 'string', 'max:20'],
            'status'      => ['required', 'in:aktif,nonaktif'],
        ]);

        $lastId = JenisPekerjaan::max('id') + 1;
        $validated['kode'] = 'JP' . str_pad($lastId, 4, '0', STR_PAD_LEFT);

        JenisPekerjaan::create($validated);

        return redirect()->route('admin.jenis-pekerjaan.index')
            ->with('status', 'Jenis pekerjaan berhasil ditambahkan.');
    }

    public function update(Request $request, JenisPekerjaan $jenisPekerjaan)
    {
        $validated = $request->validate([
            'satuan'      => ['required', 'string', 'max:50'],
            'keterangan'  => ['nullable', 'string'],
            'warna'       => ['nullable', 'string', 'max:20'],
            'status'      => ['required', 'in:aktif,nonaktif'],
        ]);

        // 'jenis' sengaja tidak diubah lewat form, karena dipakai sebagai acuan tetap

        $jenisPekerjaan->update($validated);

        return redirect()->route('admin.jenis-pekerjaan.index')
            ->with('status', 'Jenis pekerjaan berhasil diperbarui.');
    }

    public function destroy(JenisPekerjaan $jenisPekerjaan)
    {
        $jenisPekerjaan->delete();

        return redirect()->route('admin.jenis-pekerjaan.index')
            ->with('status', 'Jenis pekerjaan berhasil dihapus.');
    }
}