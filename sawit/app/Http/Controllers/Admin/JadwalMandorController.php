<?php
// app/Http/Controllers/Admin/JadwalMandorController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blok;
use App\Models\JadwalMandor;
use App\Models\Mandor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JadwalMandorController extends Controller
{
    protected function rules(Request $request, ?int $jadwalId = null): array
    {
        $mandorId = $request->input('mandor_id');

        return [
            'mandor_id' => [
                'required',
                'exists:mandors,id',
                // Satu mandor hanya boleh punya SATU baris jadwal.
                // Kalau mau ganti/tambah blok, edit jadwal yang sudah ada.
                Rule::unique('jadwal_mandors', 'mandor_id')->ignore($jadwalId),
            ],

            'blok_ids'   => 'required|array|min:1|max:2',

            'blok_ids.*' => [
                'exists:blok,id',
                // Blok yang dipilih tidak sedang dipegang mandor LAIN.
                function ($attribute, $value, $fail) use ($mandorId, $jadwalId) {
                    $pemegang = DB::table('blok_jadwal_mandor')
                        ->join('jadwal_mandors', 'jadwal_mandors.id', '=', 'blok_jadwal_mandor.jadwal_mandor_id')
                        ->where('blok_jadwal_mandor.blok_id', $value)
                        ->where('jadwal_mandors.mandor_id', '!=', $mandorId)
                        ->when($jadwalId, function ($q) use ($jadwalId) {
                            $q->where('jadwal_mandors.id', '!=', $jadwalId);
                        })
                        ->value('jadwal_mandors.mandor_id');

                    if ($pemegang) {
                        $namaMandor = Mandor::find($pemegang)?->nama ?? 'mandor lain';
                        $namaBlok   = Blok::find($value)?->nama_blok ?? 'Blok ini';
                        $fail("{$namaBlok} sudah dikelola oleh {$namaMandor}.");
                    }
                },
            ],

            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'nullable|string|max:255',
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules($request));

        $jadwal = JadwalMandor::create([
            'mandor_id' => $data['mandor_id'],
            'blok_id'   => $data['blok_ids'][0],
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'keterangan'      => $data['keterangan'] ?? null,
        ]);

        $jadwal->bloks()->sync($data['blok_ids']);

        return back()->with('success', 'Jadwal mandor berhasil ditambahkan.');
    }

    public function update(Request $request, JadwalMandor $jadwal)
    {
        $data = $request->validate($this->rules($request, $jadwal->id));

        $jadwal->update([
            'mandor_id' => $data['mandor_id'],
            'blok_id'   => $data['blok_ids'][0],
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'keterangan'      => $data['keterangan'] ?? null,
        ]);

        $jadwal->bloks()->sync($data['blok_ids']);

        return back()->with('success', 'Jadwal mandor berhasil diperbarui.');
    }

    public function destroy(JadwalMandor $jadwal)
    {
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    public function data()
    {
        return JadwalMandor::with(['mandor', 'bloks.pekerjas'])
            ->orderByDesc('tanggal_mulai')
            ->get();
    }
}