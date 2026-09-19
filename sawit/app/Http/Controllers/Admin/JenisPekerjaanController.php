<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JenisPekerjaanController extends Controller
{
    public function index(Request $request): View
    {
        $query = JenisPekerjaan::query();

        $cari = trim((string) $request->query('q', ''));

        if ($cari !== '') {
            $query->where(function ($q) use ($cari) {
                $q->where('kode', 'like', '%' . $cari . '%')
                    ->orWhere('nama_pekerjaan', 'like', '%' . $cari . '%')
                    ->orWhere('satuan', 'like', '%' . $cari . '%');
            });
        }

        $status = trim((string) $request->query('status', ''));

        if ($status !== '') {
            $query->where('status', $status);
        }

        return view('admin.jenispekerjaan', [
            'jenisPekerjaan' => $query->orderBy('id')->paginate(8)->withQueryString(),
            'totalJenis' => JenisPekerjaan::count(),
            'totalAktif' => JenisPekerjaan::where('status', 'aktif')->count(),
            'totalNonaktif' => JenisPekerjaan::where('status', 'nonaktif')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan());

        JenisPekerjaan::create($data);

        return redirect()
            ->route('admin.jenis-pekerjaan.index')
            ->with('sukses', 'Jenis pekerjaan berhasil ditambahkan.');
    }

    public function update(Request $request, JenisPekerjaan $jenisPekerjaan): RedirectResponse
    {
        $data = $request->validate($this->aturan($jenisPekerjaan->id));

        $jenisPekerjaan->update($data);

        return back()->with('sukses', 'Jenis pekerjaan berhasil diperbarui.');
    }

    public function destroy(JenisPekerjaan $jenisPekerjaan): RedirectResponse
    {
        $jenisPekerjaan->delete();

        return redirect()
            ->route('admin.jenis-pekerjaan.index')
            ->with('sukses', 'Jenis pekerjaan berhasil dihapus.');
    }

    private function aturan(?int $abaikanId = null): array
    {
        return [
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('jenis_pekerjaan', 'kode')->ignore($abaikanId),
            ],
            'nama_pekerjaan' => [
                'required',
                'string',
                'max:100',
            ],
            'satuan' => [
                'required',
                'string',
                'max:50',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                'in:aktif,nonaktif',
            ],
        ];
    }
}
