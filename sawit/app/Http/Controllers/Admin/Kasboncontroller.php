<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kasbon;
use App\Models\Blok;
use App\Models\Pekerja;
use App\Models\Mandor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KasbonController extends Controller
{
    /**
     * Menampilkan halaman daftar Kasbon (filter + statistik).
     * Form tambah/ubah ada di dialog pada view admin.kasbon.
     */
    public function index(Request $request)
    {
        $query = Kasbon::with(['pekerja', 'mandor', 'blok']);

        if ($request->filled('bulan')) {
            // input type="month" -> format "2026-09"
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereYear('tanggal_kasbon', $tahun)
                ->whereMonth('tanggal_kasbon', $bulan);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'aktif'     => $query->where('status', 'aktif'),
                'lunas'     => $query->where('status', 'lunas'),
                'menunggu'  => $query->menungguPotongan(),
                'terlambat' => $query->terlambat(),
                default     => null,
            };
        }

        if (in_array($request->peminjam, ['pekerja', 'mandor'], true)) {
            // filter jenis peminjam: "pekerja" atau "mandor"
            $query->where('tipe_peminjam', $request->peminjam);
        }

        if ($request->filled('blok')) {
            $query->where('blok_id', $request->blok);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->whereHas('pekerja', fn ($p) => $p->where('nama', 'like', "%{$q}%"))
                    ->orWhereHas('mandor', fn ($m) => $m->where('nama', 'like', "%{$q}%"));
            });
        }

        $kasbons = $query->orderByDesc('tanggal_kasbon')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // ---- Statistik kartu ----
        $totalAktif = Kasbon::where('status', 'aktif')->count();
        $nominalAktif = Kasbon::where('status', 'aktif')
            ->get()
            ->sum(fn ($k) => $k->sisa_pinjaman);

        $totalLunas = Kasbon::where('status', 'lunas')->count();
        $nominalLunas = Kasbon::where('status', 'lunas')->sum('nominal_kasbon');

        $totalMenunggu = Kasbon::menungguPotongan()->count();
        $nominalMenunggu = Kasbon::menungguPotongan()->sum('nominal_potongan_per_periode');

        $totalTerlambat = Kasbon::terlambat()->count();
        $nominalTerlambat = Kasbon::terlambat()->sum('nominal_potongan_per_periode');

        $totalSemua = Kasbon::count();
        $nominalSemua = Kasbon::sum('nominal_kasbon');

        // ---- Data pendukung filter & form ----
        $bloks = Blok::where('status', 'aktif')->orderBy('nama_blok')->get();
        $pekerjas = Pekerja::where('status', 'aktif')->orderBy('nama')->get();
        $mandors = Mandor::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.kasbon', compact(
            'kasbons',
            'totalAktif', 'nominalAktif',
            'totalLunas', 'nominalLunas',
            'totalMenunggu', 'nominalMenunggu',
            'totalTerlambat', 'nominalTerlambat',
            'totalSemua', 'nominalSemua',
            'bloks', 'pekerjas', 'mandors'
        ));
    }

    /**
     * Simpan kasbon baru.
     */
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        Kasbon::create($data);

        return redirect()
            ->route('admin.kasbon.index')
            ->with('sukses', 'Kasbon baru berhasil ditambahkan.');
    }

    /**
     * Perbarui data kasbon.
     */
    public function update(Request $request, Kasbon $kasbon)
    {
        $data = $this->validasi($request);

        $kasbon->update($data);

        return redirect()
            ->route('admin.kasbon.index')
            ->with('sukses', 'Data kasbon berhasil diperbarui.');
    }

    /**
     * Hapus data kasbon.
     */
    public function destroy(Kasbon $kasbon)
    {
        $kasbon->delete();

        return back()->with('sukses', 'Data kasbon berhasil dihapus.');
    }

    /**
     * Tandai kasbon sebagai lunas (melunasi sisa pinjaman sekaligus).
     */
    public function tandaiLunas(Kasbon $kasbon)
    {
        $kasbon->update([
            'nominal_terbayar' => $kasbon->nominal_kasbon,
            'status'           => 'lunas',
            'tanggal_lunas'    => Carbon::today(),
        ]);

        return back()->with('sukses', 'Kasbon berhasil ditandai lunas.');
    }

    /**
     * Batalkan status lunas (misal salah klik): kasbon kembali aktif
     * dan sisa pinjaman kembali penuh.
     */
    public function batalLunas(Kasbon $kasbon)
    {
        if ($kasbon->status !== 'lunas') {
            return back();
        }

        $kasbon->update([
            'nominal_terbayar' => 0,
            'status'           => 'aktif',
            'tanggal_lunas'    => null,
        ]);

        return back()->with('sukses', 'Status lunas dibatalkan, kasbon kembali aktif.');
    }

    /**
     * Validasi + normalisasi input form (dipakai store & update).
     * Blok tidak diinput manual: pekerja -> blok milik pekerja, mandor -> tanpa blok.
     */
    private function validasi(Request $request): array
    {
        $validated = $request->validate([
            'peminjam'                     => ['required', 'regex:/^(pekerja|mandor):\d+$/'],
            'tanggal_kasbon'               => 'required|date',
            'nominal_kasbon'               => 'required|numeric|min:1',
            'tujuan_kasbon'                => 'required|string|max:500',
            'tanggal_rencana_potongan'     => 'required|date|after_or_equal:tanggal_kasbon',
            'nominal_potongan_per_periode' => 'required|numeric|min:1',
        ], [
            'peminjam.required' => 'Pekerja atau mandor wajib dipilih.',
            'peminjam.regex'    => 'Pilihan pekerja/mandor tidak valid.',
        ]);

        [$tipe, $id] = explode(':', $validated['peminjam']);

        $blokId = $tipe === 'pekerja'
            ? Pekerja::findOrFail($id)->blok_id
            : null;

        return [
            'tipe_peminjam'                => $tipe,
            'pekerja_id'                   => $tipe === 'pekerja' ? $id : null,
            'mandor_id'                    => $tipe === 'mandor' ? $id : null,
            'blok_id'                      => $blokId,
            'tanggal_kasbon'               => $validated['tanggal_kasbon'],
            'nominal_kasbon'               => $validated['nominal_kasbon'],
            'tujuan_kasbon'                => $validated['tujuan_kasbon'],
            'tanggal_rencana_potongan'     => $validated['tanggal_rencana_potongan'],
            'nominal_potongan_per_periode' => $validated['nominal_potongan_per_periode'],
        ];
    }
}