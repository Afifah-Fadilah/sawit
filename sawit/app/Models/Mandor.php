<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mandor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kode_mandor',
        'nama',
        'phone',
        'afdeling',
        'blok_kelola',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bloks()
    {
        return $this->belongsToMany(
            Blok::class,
            'mandor_blok',
            'mandor_id',
            'blok_id'
        )->withTimestamps();
    }
}