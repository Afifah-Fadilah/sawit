<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\HasilKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $mandor = Auth::user()->mandor;

        // Jaga-jaga kalau akun mandor ternyata belum punya data profil Mandor terkait
        if (!$mandor) {
            return view('mandor.dashboard', [
                'totalBlok'    => 0,
                'totalPekerja' => 0,
                'logs'         => collect(),
            ]);
        }

        // Ambil jadwal aktif milik mandor ini (satu mandor = satu jadwal, sesuai aturan terbaru)
        $jadwal = $mandor->jadwal()->with('bloks.pekerjas')->first();

        $bloks = $jadwal ? $jadwal->bloks : collect();

        $totalBlok    = $bloks->count();
        $totalPekerja = $bloks->sum(function ($blok) {
            return $blok->pekerjas->where('status', 'aktif')->count();
        });

        // Log Aktivitas: 5 input hasil kerja terbaru yang dicatat mandor ini
        $logs = HasilKerja::where('mandor_id', $mandor->id)
            ->with(['pekerja', 'jenisPekerjaan'])
            ->latest() // urut berdasarkan created_at terbaru
            ->take(5)
            ->get()
            ->map(function ($h) {
                return [
                    'nama'  => optional($h->pekerja)->nama ?? '-',
                    'kerja' => optional($h->jenisPekerjaan)->jenis ?? '-',
                    'hasil' => number_format($h->jumlah, 0, ',', '.') . ' ' . (optional($h->jenisPekerjaan)->satuan ?? ''),
                    'waktu' => $h->created_at->diffForHumans(),
                ];
            });

        return view('mandor.dashboard', compact('totalBlok', 'totalPekerja', 'logs'));
    }
}