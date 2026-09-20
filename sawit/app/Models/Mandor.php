<?php
// app/Models/Mandor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mandor extends Model
{
    protected $fillable = [
        'user_id', 'kode_mandor', 'nama', 'phone', 'afdeling', 'blok_kelola', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalMandor::class);
    }
}