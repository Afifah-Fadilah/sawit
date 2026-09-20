<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\HasilKerja;
use App\Models\Pekerja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InputDataController extends Controller
{
    public function index(Request $request)
    {
        $mandor  = Auth::user()->mandor;
        $hariIni = Carbon::today();

        if (!$mandor) {
            abort(403, 'Akun ini belum terhubung ke data mandor.');
        }

        $jadwal  = $mandor->jadwal()->with('bloks')->first();
        $bloks   = $jadwal ? $jadwal->bloks : collect();
        $blokIds = $bloks->pluck('id');

        $query = Pekerja::with(['blok', 'jenisPekerjaan'])
            ->whereIn('blok_id', $blokIds)
            ->where('status', 'aktif');

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('blok_id')) {
            $query->where('blok_id', $request->blok_id);
        }

        $pekerjas = $query->orderBy('nama')->get();

        // Hasil kerja hari ini untuk pekerja-pekerja di daftar ini, dikelompokkan per pekerja_id
        $hasilHariIni = HasilKerja::whereIn('pekerja_id', $pekerjas->pluck('id'))
            ->where('tanggal', $hariIni->toDateString())
            ->get()
            ->keyBy('pekerja_id');

        return view('mandor.input-data', compact('pekerjas', 'bloks', 'hasilHariIni', 'hariIni'));
    }

    public function store(Request $request)
    {
        $mandor = Auth::user()->mandor;

        $data = $request->validate([
            'pekerja_id' => 'required|exists:pekerjas,id',
            'jumlah'     => 'required|numeric|min:0.01',
        ]);

        $pekerja = Pekerja::findOrFail($data['pekerja_id']);

        // Pastikan pekerja ini memang di blok yang dipegang mandor yang login
        $jadwal  = $mandor->jadwal()->with('bloks')->first();
        $blokIds = $jadwal ? $jadwal->bloks->pluck('id') : collect();

        if (!$blokIds->contains($pekerja->blok_id)) {
            abort(403, 'Pekerja ini bukan bagian dari blok yang Anda kelola.');
        }

        if (!$pekerja->jenis_pekerjaan_id || !$pekerja->blok_id) {
            return back()->withErrors(['jumlah' => 'Pekerja ini belum punya jenis pekerjaan atau blok lengkap di data admin.']);
        }

        $hariIni = Carbon::today();

        $sudahAda = HasilKerja::where('pekerja_id', $pekerja->id)
            ->where('tanggal', $hariIni->toDateString())
            ->exists();

        if ($sudahAda) {
            return back()->withErrors(['jumlah' => 'Data hasil kerja pekerja ini untuk hari ini sudah diinput sebelumnya.']);
        }

        HasilKerja::create([
            'pekerja_id'         => $pekerja->id,
            'blok_id'            => $pekerja->blok_id,
            'jenis_pekerjaan_id' => $pekerja->jenis_pekerjaan_id,
            'mandor_id'          => $mandor->id,
            'tanggal'            => $hariIni,
            'jumlah'             => $data['jumlah'],
        ]);

        return back()->with('success', 'Hasil kerja ' . $pekerja->nama . ' berhasil disimpan.');
    }
}