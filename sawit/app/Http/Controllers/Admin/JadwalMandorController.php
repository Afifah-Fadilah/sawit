<?php
// app/Http/Controllers/Admin/JadwalMandorController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalMandor;
use Illuminate\Http\Request;

class JadwalMandorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mandor_id' => 'required|exists:mandors,id',
            'blok_id' => 'required|exists:blok,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        JadwalMandor::create($request->only(
            'mandor_id', 'blok_id', 'tanggal_mulai', 'tanggal_selesai', 'keterangan'
        ));

        return back()->with('success', 'Jadwal mandor berhasil ditambahkan.');
    }

    public function destroy(JadwalMandor $jadwal)
    {
        $jadwal->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}