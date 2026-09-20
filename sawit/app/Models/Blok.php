<?php

// app/Models/Blok.php

namespace App\Models;
use App\Models\Pekerja;
use Illuminate\Database\Eloquent\Model;

class Blok extends Model
{
    protected $table = 'blok';

    protected $fillable = [
        'nama_blok',
        'afdeling',
        'luas',
        'tahun_tanam',
        'status',
    ];

    protected $casts = [
        'luas' => 'decimal:2',
        'tahun_tanam' => 'integer',
    ];

        public function pekerjas()
    {
        return $this->hasMany(Pekerja::class, 'blok_id');
    }
}