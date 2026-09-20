<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilKerja extends Model
{
    protected $fillable = [
        'pekerja_id',
        'blok_id',
        'jenis_pekerjaan_id',
        'mandor_id',
        'tanggal',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pekerja()
    {
        return $this->belongsTo(Pekerja::class);
    }

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    public function jenisPekerjaan()
    {
        return $this->belongsTo(JenisPekerjaan::class);
    }

    public function mandor()
    {
        return $this->belongsTo(Mandor::class);
    }
}