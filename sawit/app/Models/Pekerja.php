<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pekerja',
        'nama',
        'no_hp',
        'jenis_pekerjaan_id',
        'blok_id',
        'status',
        'foto',
    ];

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    public function jenisPekerjaan()
    {
        return $this->belongsTo(JenisPekerjaan::class);
    }
}