<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanUpahItem extends Model
{
    protected $table = 'laporan_upah_items';

    protected $fillable = [
        'laporan_upah_id', 'tipe', 'pekerja_id', 'mandor_id', 'kode', 'nama',
        'upah_kotor', 'potongan_kasbon', 'upah_bersih', 'rincian', 'rincian_kasbon',
    ];

    protected $casts = [
        'rincian'        => 'array',
        'rincian_kasbon' => 'array',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanUpah::class, 'laporan_upah_id');
    }
}