<?php
// app/Models/JadwalMandor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMandor extends Model
{
    protected $fillable = ['mandor_id', 'blok_id', 'tanggal_mulai', 'tanggal_selesai', 'keterangan'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function mandor()
    {
        return $this->belongsTo(Mandor::class);
    }

    /**
     * Kolom blok_id lama (blok pertama). Dipertahankan supaya kode lain
     * yang masih memanggil $jadwal->blok tidak error.
     */
    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    /**
     * Relasi baru: satu jadwal bisa punya sampai 2 blok.
     */
    public function bloks()
    {
        return $this->belongsToMany(Blok::class, 'blok_jadwal_mandor');
    }
}