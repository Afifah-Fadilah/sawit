<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kasbon extends Model
{
    protected $fillable = [
        'tipe_peminjam',
        'pekerja_id',
        'mandor_id',
        'blok_id',
        'tanggal_kasbon',
        'nominal_kasbon',
        'tujuan_kasbon',
        'tanggal_rencana_potongan',
        'nominal_potongan_per_periode',
        'nominal_terbayar',
        'status',
        'tanggal_lunas',
    ];

    protected $casts = [
        'tanggal_kasbon'            => 'date',
        'tanggal_rencana_potongan'  => 'date',
        'tanggal_lunas'             => 'date',
        'nominal_kasbon'            => 'decimal:2',
        'nominal_potongan_per_periode' => 'decimal:2',
        'nominal_terbayar'          => 'decimal:2',
    ];

    public function pekerja()
    {
        return $this->belongsTo(Pekerja::class);
    }

    public function mandor()
    {
        return $this->belongsTo(Mandor::class);
    }

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    /**
     * Nama peminjam, baik itu pekerja maupun mandor.
     */
    public function getNamaPeminjamAttribute(): ?string
    {
        return $this->tipe_peminjam === 'mandor'
            ? optional($this->mandor)->nama
            : optional($this->pekerja)->nama;
    }

    /**
     * Kode peminjam (kode_pekerja / kode_mandor), untuk sub-label di tabel.
     */
    public function getKodePeminjamAttribute(): ?string
    {
        return $this->tipe_peminjam === 'mandor'
            ? optional($this->mandor)->kode_mandor
            : optional($this->pekerja)->kode_pekerja;
    }

    /**
     * Sisa pinjaman yang belum dibayar.
     */
    public function getSisaPinjamanAttribute(): float
    {
        return max(0, (float) $this->nominal_kasbon - (float) $this->nominal_terbayar);
    }

    /**
     * Apakah kasbon aktif ini sudah lewat tanggal rencana potongan (terlambat).
     */
    public function getTerlambatAttribute(): bool
    {
        return $this->status === 'aktif'
            && $this->tanggal_rencana_potongan !== null
            && $this->tanggal_rencana_potongan->lt(Carbon::today());
    }

    /**
     * Jumlah hari keterlambatan (0 jika tidak terlambat).
     */
    public function getKeterlambatanHariAttribute(): int
    {
        if (! $this->terlambat) {
            return 0;
        }

        return (int) $this->tanggal_rencana_potongan->diffInDays(Carbon::today());
    }

    /**
     * Estimasi tanggal lunas berdasarkan sisa pinjaman dan nominal potongan per minggu.
     */
    public function getEstimasiLunasAttribute(): ?Carbon
    {
        if ($this->status !== 'aktif' || (float) $this->nominal_potongan_per_periode <= 0) {
            return null;
        }

        $sisa = $this->sisa_pinjaman;
        if ($sisa <= 0) {
            return $this->tanggal_rencana_potongan;
        }

        $jumlahPeriode = (int) ceil($sisa / (float) $this->nominal_potongan_per_periode);

        return $this->tanggal_rencana_potongan->copy()->addWeeks(max(0, $jumlahPeriode - 1));
    }

    /**
     * Status tampilan: aktif | menunggu | terlambat | lunas
     * (menunggu & terlambat adalah turunan dari status aktif, bukan nilai tersimpan).
     */
    public function getStatusTampilAttribute(): string
    {
        if ($this->status === 'lunas') {
            return 'lunas';
        }

        return $this->terlambat ? 'terlambat' : 'menunggu';
    }

    public function scopeMenungguPotongan($query)
    {
        return $query->where('status', 'aktif')
            ->whereDate('tanggal_rencana_potongan', '>=', Carbon::today());
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'aktif')
            ->whereDate('tanggal_rencana_potongan', '<', Carbon::today());
    }
}