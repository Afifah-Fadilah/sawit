<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    public function index()
    {
        $mandor = Auth::user()->mandor;

        if (!$mandor) {
            return view('mandor.akun', [
                'mandor'       => null,
                'bloks'        => collect(),
                'afdeling'     => '-',
                'namaBlok'     => '-',
                'totalPekerja' => 0,
            ]);
        }

        // Jadwal milik mandor ini (sama seperti DashboardController).
        // Blok yang dikelola bisa 1-2 dan tidak harus berurutan (mis. Blok 1 & Blok 3),
        // jadi nama blok diambil apa adanya dari relasi, bukan dihitung dari nomor.
        $jadwal = $mandor->jadwal()
            ->with(['bloks.pekerjas' => function ($q) {
                $q->where('status', 'aktif')
                    ->with('jenisPekerjaan')
                    ->orderBy('nama');
            }])
            ->first();

        $bloks = $jadwal ? $jadwal->bloks->sortBy('nama_blok')->values() : collect();

        // Semua blok dalam satu jadwal berada di afdeling yang sama, tapi tetap
        // pakai unique() supaya aman kalau data lama ada yang berbeda.
        $afdeling = $bloks->pluck('afdeling')->filter()->unique()->implode(', ') ?: '-';
        $namaBlok = $bloks->pluck('nama_blok')->implode(', ') ?: '-';

        $totalPekerja = $bloks->sum(fn ($blok) => $blok->pekerjas->count());

        return view('mandor.akun', compact('mandor', 'bloks', 'afdeling', 'namaBlok', 'totalPekerja'));
    }

    /**
     * Mandor mengubah nomor teleponnya sendiri.
     */
    public function updatePhone(Request $request)
    {
        $mandor = Auth::user()->mandor;

        abort_unless($mandor, 404);

        $data = $request->validate(
            [
                'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            ],
            [
                'phone.required' => 'Nomor telepon wajib diisi.',
                'phone.max'      => 'Nomor telepon maksimal 20 karakter.',
                'phone.regex'    => 'Nomor telepon hanya boleh berisi angka, spasi, tanda + dan -.',
            ]
        );

        $mandor->update(['phone' => $data['phone']]);

        return back()->with('success', 'Nomor telepon berhasil diperbarui.');
    }
}