<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifUpah extends Model
{
    protected $fillable = [
        'jenis_pekerjaan_id',
        'tarif',
        'status',
    ];

    public function jenisPekerjaan()
    {
        return $this->belongsTo(JenisPekerjaan::class);
    }
}