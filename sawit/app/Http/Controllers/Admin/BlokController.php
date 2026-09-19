<?php

// app/Http/Controllers/Admin/BlokController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlokController extends Controller
{
    public function index(Request $request): View
    {
        $query = Blok::query();

        // Pencarian: nama blok atau afdeling
        $cari = trim((string) $request->query('q', ''));
        if ($cari !== '') {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_blok', 'like', '%' . $cari . '%')
                  ->orWhere('afdeling', 'like', '%' . $cari . '%');
            });
        }

        // Filter afdeling
        $afdeling = trim((string) $request->query('afdeling', ''));
        if ($afdeling !== '') {
            $query->where('afdeling', $afdeling);
        }

        return view('admin.kelolablok', [
            'bloks'          => $query->orderBy('id')->paginate(8)->withQueryString(),
            'totalBlok'      => Blok::count(),
            'totalLuas'      => (float) Blok::sum('luas'),
            'totalAfdeling'  => Blok::distinct()->count('afdeling'),
            'daftarAfdeling' => Blok::select('afdeling')->distinct()->orderBy('afdeling')->pluck('afdeling'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan(), $this->pesan(), $this->atribut());

        Blok::create($data);

        return redirect()
            ->route('admin.blok.index')
            ->with('sukses', 'Blok berhasil ditambahkan.');
    }

    public function update(Request $request, Blok $blok): RedirectResponse
    {
        $data = $request->validate($this->aturan($blok->id), $this->pesan(), $this->atribut());

        $blok->update($data);

        return back()->with('sukses', 'Blok berhasil diperbarui.');
    }

    public function destroy(Blok $blok): RedirectResponse
    {
        $blok->delete();

        return redirect()
            ->route('admin.blok.index')
            ->with('sukses', 'Blok berhasil dihapus.');
    }

    private function aturan(?int $abaikanId = null): array
    {
        return [
            'nama_blok'   => ['required', 'string', 'max:100', Rule::unique('blok', 'nama_blok')->ignore($abaikanId)],
            'afdeling'    => ['required', 'string', 'max:100'],
            'luas'        => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'tahun_tanam' => ['required', 'integer', 'between:1900,' . date('Y')],
            'status'      => ['required', 'in:aktif,nonaktif'],
        ];
    }

    private function pesan(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique'   => ':attribute sudah dipakai.',
            'string'   => ':attribute harus berupa teks.',
            'numeric'  => ':attribute harus berupa angka.',
            'integer'  => ':attribute harus berupa bilangan bulat.',
            'max'      => ':attribute melebihi batas maksimum.',
            'min'      => ':attribute di bawah batas minimum.',
            'between'  => ':attribute harus antara :min dan :max.',
            'in'       => ':attribute tidak valid.',
        ];
    }

    private function atribut(): array
    {
        return [
            'nama_blok'   => 'Nama blok',
            'afdeling'    => 'Afdeling',
            'luas'        => 'Luas',
            'tahun_tanam' => 'Tahun tanam',
            'status'      => 'Status',
        ];
    }
}