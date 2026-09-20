<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
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
            ]);
        }

        // Ambil jadwal aktif milik mandor ini (satu mandor = satu jadwal, sesuai aturan terbaru)
        $jadwal = $mandor->jadwal()->with('bloks.pekerjas')->first();

        $bloks = $jadwal ? $jadwal->bloks : collect();

        $totalBlok    = $bloks->count();
        $totalPekerja = $bloks->sum(function ($blok) {
            return $blok->pekerjas->where('status', 'aktif')->count();
        });

        return view('mandor.dashboard', compact('totalBlok', 'totalPekerja'));
    }
}