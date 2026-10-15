<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasbonPotongan extends Model
{
    protected $table = 'kasbon_potongans';

    protected $fillable = [
        'kasbon_id', 'laporan_upah_id', 'laporan_upah_item_id', 'nominal',
    ];

    public function kasbon()
    {
        return $this->belongsTo(Kasbon::class);
    }
}