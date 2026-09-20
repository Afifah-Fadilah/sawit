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
        $query = Mandor::with('user');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('kode_mandor', 'like', "%{$q}%");
            });
        }

        if ($request->filled('blok')) {
            $query->where('blok_kelola', $request->blok);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mandors = $query->orderBy('nama')->paginate(10, ['*'], 'page')->withQueryString();

        $totalMandor    = Mandor::count();
        $mandorAktif    = Mandor::where('status', 'aktif')->count();
        $mandorNonaktif = Mandor::where('status', 'nonaktif')->count();

        $bloks = Blok::where('status', 'aktif')->orderBy('nama_blok')->get();

        $jadwals = JadwalMandor::with(['mandor', 'blok'])
            ->orderByDesc('tanggal_mulai')
            ->paginate(10, ['*'], 'jadwal_page')
            ->withQueryString();

        return view('admin.mandor', compact(
            'mandors',
            'totalMandor',
            'mandorAktif',
            'mandorNonaktif',
            'bloks',
            'jadwals'
        ));
    }

    /**
     * Simpan mandor baru sekaligus buat akun user (username auto, password default).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'        => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
            'afdeling'    => 'required|string|max:100',
            'blok_kelola' => 'required|string|max:100',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($data) {
            $nomorUrut = Mandor::max('id') + 1;
            $kode      = 'MDR' . str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);
            $username  = Str::slug($data['nama'], '') . rand(10, 99);

            // 'name' harus unique di tabel users -> tambahkan kode_mandor supaya tidak bentrok
            // walau ada mandor lain dengan nama sama persis.
            $user = User::create([
                'name'     => $data['nama'] . ' (' . $kode . ')',
                'username' => $username,
                'password' => Hash::make('mandor123'), // password default, wajib diganti lewat tab Kelola Akun
                'role'     => 'mandor',
            ]);

            Mandor::create(array_merge($data, [
                'user_id'     => $user->id,
                'kode_mandor' => $kode,
            ]));
        });

        return back()->with('sukses', 'Mandor baru berhasil ditambahkan. Atur username & password final di tab "Kelola Akun".');
    }

    /**
     * Ubah data profil mandor.
     */
    public function update(Request $request, Mandor $mandor)
    {
        $data = $request->validate([
            'nama'        => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
            'afdeling'    => 'required|string|max:100',
            'blok_kelola' => 'required|string|max:100',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        $mandor->update($data);
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

        // Assign langsung supaya tidak tergantung $fillable
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