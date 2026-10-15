<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilKerja;
use App\Models\JenisPekerjaan;
use App\Models\Kasbon;
use App\Models\KasbonPotongan;
use App\Models\LaporanUpah;
use App\Models\LaporanUpahItem;
use App\Models\Mandor;
use App\Models\Pekerja;
use App\Models\TarifUpah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanUpahController extends Controller
{
    /**
     * Daftar laporan upah per periode. Bila ?periode=ID diberikan,
     * halaman menampilkan detail periode tersebut (tab Pekerja / Mandor).
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab') === 'mandor' ? 'mandor' : 'pekerja';

        $laporan = null;
        $laporans = null;
        $items = collect();
        $ringkas = ['kotor' => 0, 'potongan' => 0, 'bersih' => 0];
        $jumlahTab = ['pekerja' => 0, 'mandor' => 0];
        $adaTanpaTarif = false;

        if ($request->filled('periode')) {
            $laporan = LaporanUpah::findOrFail($request->input('periode'));
            $semua = $laporan->items()->get();

            $jumlahTab = [
                'pekerja' => $semua->where('tipe', 'pekerja')->count(),
                'mandor'  => $semua->where('tipe', 'mandor')->count(),
            ];
            $ringkas = [
                'kotor'    => (int) $semua->sum('upah_kotor'),
                'potongan' => (int) $semua->sum('potongan_kasbon'),
                'bersih'   => (int) $semua->sum('upah_bersih'),
            ];
            $adaTanpaTarif = $semua->contains(
                fn ($i) => collect($i->rincian)->contains('tanpa_tarif', true)
            );
            $items = $semua->where('tipe', $tab)
                ->sortBy('nama', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();
        } else {
            $laporans = LaporanUpah::query()
                ->withCount([
                    'items as jumlah_pekerja' => fn ($q) => $q->where('tipe', 'pekerja'),
                    'items as jumlah_mandor'  => fn ($q) => $q->where('tipe', 'mandor'),
                ])
                ->withSum('items as total_kotor', 'upah_kotor')
                ->withSum('items as total_potongan', 'potongan_kasbon')
                ->withSum('items as total_bersih', 'upah_bersih')
                ->orderByDesc('tanggal_mulai')
                ->orderByDesc('id')
                ->paginate(10)
                ->withQueryString();
        }

        $finalItem = fn () => LaporanUpahItem::whereHas('laporan', fn ($q) => $q->where('status', 'final'));

        $stat = [
            'total'    => LaporanUpah::count(),
            'draft'    => LaporanUpah::where('status', 'draft')->count(),
            'dibayar'  => (int) $finalItem()->sum('upah_bersih'),
            'potongan' => (int) $finalItem()->sum('potongan_kasbon'),
        ];

        // Saran periode berikutnya: sehari setelah laporan terakhir sampai hari ini
        $akhir = LaporanUpah::max('tanggal_selesai');
        $saran = [
            'mulai'  => $akhir ? Carbon::parse($akhir)->addDay()->toDateString() : now()->startOfMonth()->toDateString(),
            'sampai' => now()->toDateString(),
        ];
        if ($saran['mulai'] > $saran['sampai']) {
            $saran['mulai'] = $saran['sampai'];
        }

        return view('admin.laporanupah', compact(
            'laporan', 'laporans', 'items', 'tab', 'ringkas', 'jumlahTab',
            'adaTanpaTarif', 'stat', 'saran'
        ));
    }

    /**
     * Buat laporan periode baru (status draft) dan hitung upahnya.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'nullable|string|max:255',
        ], [
            'tanggal_mulai.required'          => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required'        => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal'  => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $mulai  = Carbon::parse($data['tanggal_mulai'])->toDateString();
        $sampai = Carbon::parse($data['tanggal_selesai'])->toDateString();

        if (LaporanUpah::where('tanggal_mulai', '<=', $sampai)->where('tanggal_selesai', '>=', $mulai)->exists()) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Periode bertabrakan dengan laporan upah yang sudah ada.',
            ]);
        }

        if (! HasilKerja::whereBetween('tanggal', [$mulai, $sampai])->exists()) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Tidak ada data hasil kerja pada rentang tanggal ini.',
            ]);
        }

        $laporan = DB::transaction(function () use ($mulai, $sampai, $data) {
            $l = LaporanUpah::create([
                'tanggal_mulai'   => $mulai,
                'tanggal_selesai' => $sampai,
                'keterangan'      => $data['keterangan'] ?? null,
                'status'          => 'draft',
            ]);

            $l->update(['kode' => 'UPH-' . $l->tanggal_mulai->format('Ym') . '-' . str_pad($l->id, 3, '0', STR_PAD_LEFT)]);
            $this->bangun($l);

            return $l;
        });

        return redirect()
            ->route('admin.laporanupah', ['periode' => $laporan->id])
            ->with('sukses', 'Laporan upah berhasil dibuat. Isi gaji mandor, periksa potongan kasbon, lalu finalisasi.');
    }

    /**
     * Hitung ulang draft (mis. setelah ada hasil kerja / tarif / kasbon yang berubah).
     * Gaji mandor yang sudah diisi tetap dipertahankan.
     */
    public function hitungUlang(LaporanUpah $laporanUpah)
    {
        if ($laporanUpah->isFinal()) {
            return back()->withErrors(['laporan' => 'Laporan sudah final. Batalkan finalisasi dulu untuk menghitung ulang.']);
        }

        DB::transaction(fn () => $this->bangun($laporanUpah));

        return redirect()
            ->route('admin.laporanupah', ['periode' => $laporanUpah->id])
            ->with('sukses', 'Laporan berhasil dihitung ulang.');
    }
    /**
     * Finalisasi: potongan kasbon benar-benar dicatat dan sisa pinjaman berkurang.
     */
    public function finalisasi(LaporanUpah $laporanUpah)
    {
        if ($laporanUpah->isFinal()) {
            return back();
        }

        DB::transaction(function () use ($laporanUpah) {
            foreach ($laporanUpah->items()->get() as $item) {
                $idPeminjam = $item->tipe === 'pekerja' ? $item->pekerja_id : $item->mandor_id;
                $hit = $this->hitungPotongan($item->tipe, $idPeminjam, (int) $item->upah_kotor, $laporanUpah->tanggal_selesai);

                $diterapkan = [];
                $total = 0;

                foreach ($hit['baris'] as $b) {
                    $kasbon = Kasbon::lockForUpdate()->find($b['kasbon_id']);
                    if (! $kasbon) {
                        continue;
                    }

                    $sisa = max(0, (int) $kasbon->nominal_kasbon - (int) ($kasbon->nominal_terbayar ?? 0));
                    $nominal = min((int) $b['nominal'], $sisa);
                    if ($nominal <= 0) {
                        continue;
                    }

                    KasbonPotongan::create([
                        'kasbon_id'            => $kasbon->id,
                        'laporan_upah_id'      => $laporanUpah->id,
                        'laporan_upah_item_id' => $item->id,
                        'nominal'              => $nominal,
                    ]);

                    $terbayar = (int) ($kasbon->nominal_terbayar ?? 0) + $nominal;
                    $kasbon->nominal_terbayar = $terbayar;
                    if ($terbayar >= (int) $kasbon->nominal_kasbon) {
                        $kasbon->status = 'lunas';
                        $kasbon->tanggal_lunas = Carbon::today();
                    }
                    $kasbon->save();

                    $b['nominal'] = $nominal;
                    $b['sisa_setelah'] = $sisa - $nominal;
                    $diterapkan[] = $b;
                    $total += $nominal;
                }

                $item->update([
                    'potongan_kasbon' => $total,
                    'upah_bersih'     => (int) $item->upah_kotor - $total,
                    'rincian_kasbon'  => $diterapkan,
                ]);
            }

            $laporanUpah->update(['status' => 'final', 'finalized_at' => now()]);
        });

        return redirect()
            ->route('admin.laporanupah', ['periode' => $laporanUpah->id])
            ->with('sukses', 'Laporan difinalisasi. Potongan kasbon sudah dicatat dan slip siap dicetak.');
    }

    /**
     * Batalkan finalisasi: potongan kasbon dikembalikan, laporan kembali draft.
     */
    public function batalFinalisasi(LaporanUpah $laporanUpah)
    {
        if (! $laporanUpah->isFinal()) {
            return back();
        }

        DB::transaction(function () use ($laporanUpah) {
            $potongans = KasbonPotongan::where('laporan_upah_id', $laporanUpah->id)->get();

            foreach ($potongans as $p) {
                $kasbon = Kasbon::lockForUpdate()->find($p->kasbon_id);
                if (! $kasbon) {
                    continue;
                }

                $kasbon->nominal_terbayar = max(0, (int) ($kasbon->nominal_terbayar ?? 0) - (int) $p->nominal);
                if ((int) $kasbon->nominal_terbayar < (int) $kasbon->nominal_kasbon) {
                    $kasbon->status = 'aktif';
                    $kasbon->tanggal_lunas = null;
                }
                $kasbon->save();
            }

            KasbonPotongan::where('laporan_upah_id', $laporanUpah->id)->delete();

            $laporanUpah->update(['status' => 'draft', 'finalized_at' => null]);
            $this->bangun($laporanUpah);
        });

        return redirect()
            ->route('admin.laporanupah', ['periode' => $laporanUpah->id])
            ->with('sukses', 'Finalisasi dibatalkan. Potongan kasbon dikembalikan dan laporan kembali menjadi draft.');
    }


    /**
     * Hapus laporan upah, baik Draft maupun Final.
     * Jika laporan Final, kembalikan potongan kasbon terlebih dahulu.
     */
    public function destroy(LaporanUpah $laporanUpah)
    {
        DB::transaction(function () use ($laporanUpah) {

            // Jika laporan sudah Final, kembalikan potongan kasbon
            if ($laporanUpah->isFinal()) {

                $potongans = KasbonPotongan::where(
                    'laporan_upah_id',
                    $laporanUpah->id
                )->get();

                foreach ($potongans as $p) {
                    $kasbon = Kasbon::lockForUpdate()->find($p->kasbon_id);

                    if (! $kasbon) {
                        continue;
                    }

                    $kasbon->nominal_terbayar = max(
                        0,
                        (int) ($kasbon->nominal_terbayar ?? 0)
                            - (int) $p->nominal
                    );

                    // Perbarui status kasbon setelah pengembalian potongan
                    if (
                        (int) $kasbon->nominal_terbayar
                        < (int) $kasbon->nominal_kasbon
                    ) {
                        $kasbon->status = 'aktif';
                        $kasbon->tanggal_lunas = null;
                    }

                    $kasbon->save();
                }

                // Hapus catatan potongan yang terkait dengan laporan
                KasbonPotongan::where(
                    'laporan_upah_id',
                    $laporanUpah->id
                )->delete();
            }

            // Hapus item laporan, lalu laporan periodenya
            $laporanUpah->items()->delete();
            $laporanUpah->delete();
        });

        return redirect()
            ->route('admin.laporanupah')
            ->with('sukses', 'Laporan upah berhasil dihapus.');
    }

    /**
     * Slip / invoice satu pekerja atau mandor.
     */
    public function slip(LaporanUpahItem $item)
    {
        return $this->tampilSlip(collect([$item->load('laporan')]));
    }

    /**
     * Slip semua (atau ?tipe=pekerja|mandor) dalam satu periode, satu slip per halaman.
     */
    public function slipSemua(Request $request, LaporanUpah $laporanUpah)
    {
        // Ambil seluruh pekerja dan mandor pada periode laporan,
        // termasuk yang upahnya masih Rp0.
        $items = $laporanUpah->items()
            ->orderByDesc('tipe')
            ->orderBy('nama')
            ->get();

        if ($items->isEmpty()) {
            return back()->withErrors([
                'laporan' => 'Belum ada data upah yang bisa dicetak.'
            ]);
        }

        return view('admin.laporanupah-semua-slip', [
            'laporan'     => $laporanUpah,
            'items'       => $items,
            'tanggalCetak' => now(),
        ]);
    }

    // ------------------------------------------------------------------
    // Perhitungan
    // ------------------------------------------------------------------
    //
private function bangun(LaporanUpah $l): void
{
    $mulai  = $l->tanggal_mulai->toDateString();
    $sampai = $l->tanggal_selesai->toDateString();

    $l->items()->delete();

    $tarif = TarifUpah::where('status', 'aktif')->orderByDesc('id')->get()
        ->unique('jenis_pekerjaan_id')->keyBy('jenis_pekerjaan_id');
    $jenis = JenisPekerjaan::all()->keyBy('id');

    // ---- Pekerja ----
    $hasil = HasilKerja::selectRaw('pekerja_id, jenis_pekerjaan_id, SUM(jumlah) as total')
        ->whereBetween('tanggal', [$mulai, $sampai])
        ->groupBy('pekerja_id', 'jenis_pekerjaan_id')
        ->get()
        ->groupBy('pekerja_id');

    $pekerjas = Pekerja::whereIn('id', $hasil->keys())->get()->keyBy('id');

    foreach ($hasil as $pekerjaId => $baris) {
        $p = $pekerjas->get($pekerjaId);
        $rincian = [];
        $kotor = 0;

        foreach ($baris->sortBy(fn ($b) => optional($jenis->get($b->jenis_pekerjaan_id))->jenis ?? '') as $b) {
            $jp = $jenis->get($b->jenis_pekerjaan_id);
            $t = optional($tarif->get($b->jenis_pekerjaan_id))->tarif;
            $sub = $t !== null ? (int) round((float) $b->total * (float) $t) : 0;

            $rincian[] = [
                'jenis'       => optional($jp)->jenis ?? '-',
                'satuan'      => trim(preg_replace('/^per\s+/i', '', (string) optional($jp)->satuan)),
                'jumlah'      => (float) $b->total,
                'tarif'       => $t !== null ? (int) $t : 0,
                'subtotal'    => $sub,
                'tanpa_tarif' => $t === null,
            ];
            $kotor += $sub;
        }

        $this->buatItem($l, 'pekerja', (int) $pekerjaId, optional($p)->kode_pekerja, optional($p)->nama ?? '(Pekerja dihapus)', $kotor, $rincian);
    }

    // ---- Mandor: tarif harian x jumlah hari hadir ----
    // Hadir = pada tanggal itu ada hasil kerja yang disetor lewat mandor tsb
    $hariHadir = HasilKerja::selectRaw('mandor_id, COUNT(DISTINCT tanggal) as hari')
        ->whereBetween('tanggal', [$mulai, $sampai])
        ->groupBy('mandor_id')
        ->pluck('hari', 'mandor_id');

    $idJenisMandor = JenisPekerjaan::where('jenis', 'like', '%mandor%')->pluck('id');
    $tarifMandor = $tarif->only($idJenisMandor->all())->first();
    $jpMandor = $jenis->get($tarifMandor->jenis_pekerjaan_id ?? $idJenisMandor->first());
    $namaJenisMandor = optional($jpMandor)->jenis ?? 'Mandor';

    $mandors = Mandor::where('status', 'aktif')
        ->orWhereIn('id', $hariHadir->keys())
        ->orderBy('nama')
        ->get();

    foreach ($mandors as $m) {
        $hari = (int) ($hariHadir[$m->id] ?? 0);
        $rincian = [];
        $kotor = 0;

        if ($hari > 0) {
            $t = $tarifMandor ? (int) $tarifMandor->tarif : null;
            $kotor = $t !== null ? $hari * $t : 0;

            $rincian[] = [
                'label'       => 'Kehadiran ' . $hari . ' hari',
                'jenis'       => 'Kehadiran (' . $namaJenisMandor . ')',
                'satuan'      => 'hari',
                'jumlah'      => $hari,
                'tarif'       => $t ?? 0,
                'subtotal'    => $kotor,
                'tanpa_tarif' => $t === null,
            ];
        }

        $this->buatItem($l, 'mandor', $m->id, $m->kode_mandor ?? null, $m->nama, $kotor, $rincian);
    }
}

    private function buatItem(LaporanUpah $l, string $tipe, int $id, ?string $kode, string $nama, int $kotor, array $rincian): void
    {
        $pot = $this->hitungPotongan($tipe, $id, $kotor, $l->tanggal_selesai);

        $l->items()->create([
            'tipe'            => $tipe,
            'pekerja_id'      => $tipe === 'pekerja' ? $id : null,
            'mandor_id'       => $tipe === 'mandor' ? $id : null,
            'kode'            => $kode,
            'nama'            => $nama,
            'upah_kotor'      => $kotor,
            'potongan_kasbon' => $pot['total'],
            'upah_bersih'     => $kotor - $pot['total'],
            'rincian'         => $rincian,
            'rincian_kasbon'  => $pot['baris'],
        ]);
    }

    /**
     * Hitung potongan kasbon untuk satu orang pada satu periode.
     *
     * Aturan:
     *  - hanya kasbon aktif yang tanggal rencana potongannya sudah tiba (<= akhir periode)
     *  - tiap kasbon dipotong sebesar "nominal potongan per periode",
     *    tetapi tidak melebihi sisa pinjaman
     *  - total potongan tidak boleh melebihi upah kotor (upah bersih tidak minus)
     *  - kasbon tertua dipotong lebih dulu
     */
    private function hitungPotongan(string $tipe, ?int $id, int $upahKotor, $tanggalSelesai): array
    {
        $hasil = ['total' => 0, 'baris' => []];

        if (! $id || $upahKotor <= 0) {
            return $hasil;
        }

        $sisaUpah = $upahKotor;

        $kasbons = Kasbon::where('status', 'aktif')
            ->where('tipe_peminjam', $tipe)
            ->where($tipe . '_id', $id)
            ->whereDate('tanggal_rencana_potongan', '<=', Carbon::parse($tanggalSelesai)->toDateString())
            ->orderBy('tanggal_kasbon')
            ->orderBy('id')
            ->get();

        foreach ($kasbons as $k) {
            if ($sisaUpah <= 0) {
                break;
            }

            $sisaPinjaman = max(0, (int) $k->nominal_kasbon - (int) ($k->nominal_terbayar ?? 0));
            $nominal = min((int) $k->nominal_potongan_per_periode, $sisaPinjaman, $sisaUpah);
            if ($nominal <= 0) {
                continue;
            }

            $sisaUpah -= $nominal;
            $hasil['total'] += $nominal;
            $hasil['baris'][] = [
                'kasbon_id'    => $k->id,
                'tanggal'      => optional($k->tanggal_kasbon)->format('Y-m-d'),
                'tujuan'       => $k->tujuan_kasbon,
                'nominal'      => $nominal,
                'sisa_setelah' => $sisaPinjaman - $nominal,
            ];
        }

        return $hasil;
    }

    // ------------------------------------------------------------------
    // Slip
    // ------------------------------------------------------------------

    private function tampilSlip($slips)
    {
        return view('admin.laporanupah-slip', [
            'slips'        => $slips,
            'tanggalCetak' => now(),
            'terbilang'    => fn (int $n) => ucfirst($n > 0 ? $this->terbilang($n) : 'nol') . ' rupiah',
        ]);
    }

    private function terbilang(int $n): string
    {
        $s = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($n < 12) {
            return $s[$n];
        }
        if ($n < 20) {
            return $s[$n - 10] . ' belas';
        }
        if ($n < 100) {
            return $s[intdiv($n, 10)] . ' puluh' . ($n % 10 ? ' ' . $s[$n % 10] : '');
        }
        if ($n < 200) {
            return 'seratus' . ($n - 100 ? ' ' . $this->terbilang($n - 100) : '');
        }
        if ($n < 1000) {
            return $s[intdiv($n, 100)] . ' ratus' . ($n % 100 ? ' ' . $this->terbilang($n % 100) : '');
        }
        if ($n < 2000) {
            return 'seribu' . ($n - 1000 ? ' ' . $this->terbilang($n - 1000) : '');
        }
        if ($n < 1000000) {
            return $this->terbilang(intdiv($n, 1000)) . ' ribu' . ($n % 1000 ? ' ' . $this->terbilang($n % 1000) : '');
        }
        if ($n < 1000000000) {
            return $this->terbilang(intdiv($n, 1000000)) . ' juta' . ($n % 1000000 ? ' ' . $this->terbilang($n % 1000000) : '');
        }

        return $this->terbilang(intdiv($n, 1000000000)) . ' miliar' . ($n % 1000000000 ? ' ' . $this->terbilang($n % 1000000000) : '');
    }
}