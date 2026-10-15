<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        return view('admin.jenispekerjaan', compact(
            'jenisPekerjaans', 'totalJenis', 'jenisAktif', 'jenisNonaktif'
        ));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'jenis'      => ['required', 'string', 'max:100', 'unique:jenis_pekerjaans,jenis'],
        'satuan'     => ['required', 'string', 'max:50'],
        'keterangan' => ['nullable', 'string'],
        'warna'      => ['nullable', 'string', 'max:20'],
        'status'     => ['required', 'in:aktif,nonaktif'],
    ], [
        'jenis.unique' => 'Jenis pekerjaan ini sudah ada.',
    ]);

    // Kode dibuat setelah data tersimpan, supaya mengikuti id yang sebenarnya
    $jenis = JenisPekerjaan::create($validated + ['kode' => 'TMP' . uniqid()]);
    $jenis->update(['kode' => 'JP' . str_pad($jenis->id, 4, '0', STR_PAD_LEFT)]);

    return redirect()->route('admin.jenis-pekerjaan.index')
        ->with('status', 'Jenis pekerjaan berhasil ditambahkan.');
}

    public function update(Request $request, JenisPekerjaan $jenisPekerjaan)
    {
        $validated = $request->validate([
            'jenis' => [
                'required',
                'string',
                'max:100',
                Rule::unique('jenis_pekerjaans', 'jenis')
                    ->ignore($jenisPekerjaan->id),
            ],
            'satuan'     => ['required', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string'],
            'warna'      => ['nullable', 'string', 'max:20'],
            'status'     => ['required', 'in:aktif,nonaktif'],
        ], [
            'jenis.unique' => 'Jenis pekerjaan ini sudah ada.',
        ]);

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