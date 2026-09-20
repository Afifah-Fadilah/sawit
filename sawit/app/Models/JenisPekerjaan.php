<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPekerjaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'jenis',
        'satuan',
        'keterangan',
        'warna',
        'status',
    ];

    public function tarifUpah()
    {
        return $this->hasOne(TarifUpah::class);
    }

    // Daftar tetap 4 jenis pekerjaan yang diizinkan (sesuai enum di database)
    public const JENIS_OPSI = ['Pemanen', 'Pemberondol', 'Pemupuk', 'Penyemprot'];
}