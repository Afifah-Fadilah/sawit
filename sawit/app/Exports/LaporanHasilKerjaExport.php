<?php

namespace App\Exports;

use App\Models\HasilKerja;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanHasilKerjaExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query(): Builder
    {
        $query = HasilKerja::query()
            ->with(['pekerja', 'blok', 'jenisPekerjaan', 'mandor']);

        if (!empty($this->filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $this->filters['tanggal_mulai']);
        }

        if (!empty($this->filters['tanggal_akhir'])) {
            $query->whereDate('tanggal', '<=', $this->filters['tanggal_akhir']);
        }

        if (!empty($this->filters['blok'])) {
            $query->where('blok_id', $this->filters['blok']);
        }

        if (!empty($this->filters['jenis'])) {
            $query->where('jenis_pekerjaan_id', $this->filters['jenis']);
        }

        if (!empty($this->filters['q'])) {
            $q = $this->filters['q'];

            $query->whereHas('pekerja', function ($p) use ($q) {
                $p->where('nama', 'like', "%{$q}%");
            });
        }

        return $query->orderByDesc('tanggal')->orderByDesc('id');
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Nama Pekerja',
            'Jenis Pekerjaan',
            'Blok',
            'Mandor',
            'Jumlah Hasil',
            'Satuan',
            'Keterangan',
        ];
    }

    public function map($hasil): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $hasil->tanggal?->format('d-m-Y') ?? '-',
            $hasil->pekerja->nama ?? '-',
            $hasil->jenisPekerjaan->jenis ?? '-',
            $hasil->blok->nama_blok ?? '-',
            $hasil->mandor->nama ?? '-',
            $hasil->jumlah,
            $hasil->jenisPekerjaan->satuan ?? '-',
            $hasil->keterangan ?? '-',
        ];
    }
}
