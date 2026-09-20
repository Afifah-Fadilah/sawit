<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mandor;
use App\Models\Blok;
use App\Models\JadwalMandor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MandorController extends Controller
{
    /**
     * Menampilkan halaman Mandor (3 tab: Daftar Mandor, Kelola Akun, Jadwal Mandor).
     */
    public function index(Request $request)
    {
        $query = Mandor::with('user', 'bloks');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('kode_mandor', 'like', "%{$q}%");
            });
        }

        if ($request->filled('blok')) {
            $namaBlok = $request->blok;
            $query->whereHas('bloks', function ($w) use ($namaBlok) {
                $w->where('nama_blok', $namaBlok);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mandors = $query->orderBy('nama')->paginate(10, ['*'], 'page')->withQueryString();

        $totalMandor    = Mandor::count();
        $mandorAktif    = Mandor::where('status', 'aktif')->count();
        $mandorNonaktif = Mandor::where('status', 'nonaktif')->count();

        $bloks = Blok::where('status', 'aktif')->orderBy('nama_blok')->get();

        // Peta blok yang sudah dipegang mandor tertentu, dipakai untuk:
        // 1) validasi backend (satu blok hanya boleh dipegang satu mandor)
        // 2) tampilan form (checkbox blok yang sudah dipegang mandor lain otomatis nonaktif)
        // Sumber kebenaran sekarang: pivot blok_jadwal_mandor (via jadwal_mandors)
$pemegangBlok = DB::table('blok_jadwal_mandor')
    ->join('jadwal_mandors', 'jadwal_mandors.id', '=', 'blok_jadwal_mandor.jadwal_mandor_id')
    ->join('mandors', 'mandors.id', '=', 'jadwal_mandors.mandor_id')
    ->get(['blok_jadwal_mandor.blok_id', 'jadwal_mandors.mandor_id', 'mandors.nama'])
    ->keyBy('blok_id');

        $jadwals = JadwalMandor::with(['mandor', 'blok'])
            ->orderByDesc('tanggal_mulai')
            ->paginate(10, ['*'], 'jadwal_page')
            ->withQueryString();

        // Daftar id mandor yang sudah punya jadwal, dipakai untuk
        // menonaktifkan pilihan mandor di dropdown "Tambah Jadwal".
        $mandorSudahJadwal = JadwalMandor::pluck('mandor_id')->all();
        return view('admin.mandor', compact(
            'mandors',
            'totalMandor',
            'mandorAktif',
            'mandorNonaktif',
            'bloks',
            'jadwals',
            'pemegangBlok',
            'mandorSudahJadwal'
        ));

    }

    /**
     * Simpan mandor baru sekaligus buat akun user (username auto, password default).
     */
    public function store(Request $request)
{
    $data = $request->validate([
        'nama'   => 'required|string|max:100',
        'phone'  => 'required|string|max:20',
        'status' => 'required|in:aktif,nonaktif',
    ]);

    DB::transaction(function () use ($data) {
        $nomorUrut = Mandor::max('id') + 1;
        $kode      = 'MDR' . str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);
        $username  = Str::slug($data['nama'], '') . rand(10, 99);

        $user = User::create([
            'name'     => $data['nama'] . ' (' . $kode . ')',
            'username' => $username,
            'password' => Hash::make('mandor123'),
            'role'     => 'mandor',
        ]);

        Mandor::create([
            'user_id'     => $user->id,
            'kode_mandor' => $kode,
            'nama'        => $data['nama'],
            'phone'       => $data['phone'],
            'status'      => $data['status'],
            'blok_kelola' => null,
            'afdeling'    => null,
        ]);
    });

    return back()->with('sukses', 'Mandor baru berhasil ditambahkan. Atur username & password final di tab "Kelola Akun", dan tempatkan blok di tab "Jadwal Mandor".');
}

public function update(Request $request, Mandor $mandor)
{
    $data = $request->validate([
        'nama'   => 'required|string|max:100',
        'phone'  => 'required|string|max:20',
        'status' => 'required|in:aktif,nonaktif',
    ]);

    $mandor->update([
        'nama'   => $data['nama'],
        'phone'  => $data['phone'],
        'status' => $data['status'],
    ]);

    $mandor->user()->update(['name' => $data['nama'] . ' (' . $mandor->kode_mandor . ')']);

    return back()->with('sukses', 'Data mandor berhasil diperbarui.');
}

    /**
     * Hapus mandor (otomatis hapus akun user karena FK cascade).
     */
    public function destroy(Mandor $mandor)
    {
        $mandor->user()->delete();

        return back()->with('sukses', 'Mandor berhasil dihapus.');
    }

    /**
     * Tab "Kelola Akun": ubah username / password login mandor.
     */
    public function updateAkun(Request $request, Mandor $mandor)
    {
        $user = $mandor->user;

        if (!$user) {
            return back()->withErrors(['username' => 'Mandor ini belum punya akun user.']);
        }

        $data = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->username = $data['username'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return back()
            ->with('sukses', 'Akun mandor berhasil diperbarui.')
            ->with('tab', 'akun');
    }

    /**
     * Tab "Kelola Akun": hapus akun (mandor ikut terhapus karena FK cascade).
     */
    public function destroyAkun(Mandor $mandor)
    {
        $mandor->user->delete();

        return back()->with('sukses', 'Akun mandor berhasil dihapus.');
    }
}