<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPekerjaan extends Model
{
    protected $table = 'jenis_pekerjaan';

    protected $fillable = [
        'kode',
        'nama_pekerjaan',
        'satuan',
        'keterangan',
        'status',
    ];
}
