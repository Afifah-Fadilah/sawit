<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilKerja;
use App\Models\Blok;
use App\Models\JenisPekerjaan;
use App\Models\Pekerja;
use App\Models\Mandor;
use Illuminate\Http\Request;

class HasilKerjaController extends Controller
{
    /**
     * Menampilkan halaman Hasil Kerja (daftar + filter + statistik).
     */
    public function index(Request $request)
    {
        $query = HasilKerja::with(['pekerja', 'blok', 'jenisPekerjaan', 'mandor']);

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('blok')) {
            $query->where('blok_id', $request->blok);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_pekerjaan_id', $request->jenis);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->whereHas('pekerja', fn ($p) => $p->where('nama', 'like', "%{$q}%"))
                    ->orWhereHas('mandor', fn ($m) => $m->where('nama', 'like', "%{$q}%"));
            });
        }

        $hasilKerjas = $query->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // ---- Statistik kartu ----
        $totalHasilKerja = HasilKerja::count();

        $jenisPemanen = JenisPekerjaan::where('jenis', 'Pemanen')->first();
        $totalProduksi = $jenisPemanen
            ? HasilKerja::where('jenis_pekerjaan_id', $jenisPemanen->id)->sum('jumlah')
            : 0;
        $satuanProduksi = $jenisPemanen->satuan ?? '-';

        $totalPekerjaAktif = Pekerja::where('status', 'aktif')->count();

        // ---- Data pendukung filter & form ----
        $bloks = Blok::where('status', 'aktif')->orderBy('nama_blok')->get();
        $jenisPekerjaans = JenisPekerjaan::where('status', 'aktif')->orderBy('jenis')->get();
        $pekerjas = Pekerja::where('status', 'aktif')->orderBy('nama')->get();
        $mandors = Mandor::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.hasilkerja', compact(
            'hasilKerjas',
            'totalHasilKerja',
            'totalProduksi',
            'satuanProduksi',
            'totalPekerjaAktif',
            'bloks',
            'jenisPekerjaans',
            'pekerjas',
            'mandors'
        ));
    }

    /**
     * Simpan data hasil kerja baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pekerja_id'         => 'required|exists:pekerjas,id',
            'blok_id'            => 'required|exists:blok,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'mandor_id'          => 'required|exists:mandors,id',
            'tanggal'            => 'required|date',
            'jumlah'             => 'required|numeric|min:0',
            'keterangan'         => 'nullable|string|max:255',
        ]);

        // Satu pekerja hanya boleh punya satu data hasil kerja per tanggal
        // (sesuai unique key pekerja_id + tanggal di tabel hasil_kerjas).
        $sudahAda = HasilKerja::where('pekerja_id', $data['pekerja_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors(['pekerja_id' => 'Pekerja ini sudah memiliki data hasil kerja pada tanggal tersebut.'])
                ->withInput();
        }

        HasilKerja::create($data);

        return back()->with('sukses', 'Hasil kerja berhasil ditambahkan.');
    }

    /**
     * Ubah data hasil kerja.
     *
     * Nama parameter $hasil_kerja (bukan $hasilKerja) WAJIB seperti ini karena
     * Route::resource('hasil-kerja', ...) menghasilkan segmen route {hasil_kerja},
     * dan Laravel mencocokkan implicit route model binding berdasarkan nama variabel.
     */
    public function update(Request $request, HasilKerja $hasil_kerja)
    {
        $data = $request->validate([
            'pekerja_id'         => 'required|exists:pekerjas,id',
            'blok_id'            => 'required|exists:blok,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'mandor_id'          => 'required|exists:mandors,id',
            'tanggal'            => 'required|date',
            'jumlah'             => 'required|numeric|min:0',
            'keterangan'         => 'nullable|string|max:255',
        ]);

        $sudahAda = HasilKerja::where('pekerja_id', $data['pekerja_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->where('id', '!=', $hasil_kerja->id)
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors(['pekerja_id' => 'Pekerja ini sudah memiliki data hasil kerja pada tanggal tersebut.'])
                ->withInput();
        }

        $hasil_kerja->update($data);

        return back()->with('sukses', 'Hasil kerja berhasil diperbarui.');
    }

    /**
     * Hapus data hasil kerja.
     */
    public function destroy(HasilKerja $hasil_kerja)
    {
        $hasil_kerja->delete();

        return back()->with('sukses', 'Hasil kerja berhasil dihapus.');
    }
}