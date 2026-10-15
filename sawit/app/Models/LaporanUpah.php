<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanUpah extends Model
{
    protected $table = 'laporan_upahs';

    protected $fillable = [
        'kode', 'tanggal_mulai', 'tanggal_selesai', 'keterangan', 'status', 'finalized_at',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'finalized_at'    => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(LaporanUpahItem::class, 'laporan_upah_id');
    }

    public function isFinal(): bool
    {
        return $this->status === 'final';
    }

    public function getPeriodeTeksAttribute(): string
    {
        $a = $this->tanggal_mulai;
        $b = $this->tanggal_selesai;

        return $a->isSameDay($b)
            ? $a->translatedFormat('d M Y')
            : $a->translatedFormat('d M Y') . ' – ' . $b->translatedFormat('d M Y');
    }
}