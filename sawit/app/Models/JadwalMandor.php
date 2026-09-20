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

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }
}