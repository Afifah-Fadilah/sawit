@extends('admin.layouts.admin')

@section('title', 'Laporan Hasil Kerja')

@section('content')
<style>
    .laporan {
        font-family: 'Poppins', sans-serif;
        color: #26352d;
    }

    .laporan-header {
        margin-bottom: 24px;
    }

    .laporan-header h2 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
    }

    .laporan-header p {
        margin: 0;
        color: #718078;
        font-size: 13px;
    }

    .laporan-card {
        background: #fff;
        border: 1px solid #e6ece7;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .laporan-card h3 {
        font-size: 15px;
        margin: 0 0 16px;
        color: #657455;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
    }

    .filter-field label {
        display: block;
        margin-bottom: 7px;
        font-size: 12px;
        font-weight: 600;
    }

    .filter-field input,
    .filter-field select {
        box-sizing: border-box;
        width: 100%;
        padding: 10px;
        border: 1px solid #dce4de;
        border-radius: 8px;
        background: #fff;
        font: inherit;
        font-size: 12px;
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }

    .btn-laporan {
        display: inline-block;
        border: 0;
        border-radius: 8px;
        padding: 10px 16px;
        font: inherit;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-filter {
        background: #28794e;
        color: white;
    }

    .btn-reset {
        background: #edf2ee;
        color: #435249;
    }

    /* Kartu ringkasan: satu baris, ikon di kiri */
    .ringkasan-grid {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(210px, 1fr);
        gap: 16px;
        margin-bottom: 20px;
        overflow-x: auto;
        padding: 4px 4px 14px;
    }

    .ringkasan-item {
        box-sizing: border-box;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-height: 120px;
        padding: 18px;
        background: #f3f0d8;
        border-radius: 20px;
        box-shadow: 0 6px 14px rgba(91, 100, 68, 0.14);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .ringkasan-ikon {
        flex: 0 0 46px;
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #b88a64;
        color: #fff;
    }

    .ringkasan-ikon svg {
        width: 22px;
        height: 22px;
    }

    .ringkasan-isi {
        min-width: 0;
    }

    .ringkasan-isi .label {
        margin: 0;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.3;
        color: #26352d;
    }

    .ringkasan-isi strong {
        display: block;
        margin: 2px 0;
        font-size: 30px;
        line-height: 1.25;
        color: #26352d;
    }

    .ringkasan-isi .satuan {
        margin: 0;
        font-size: 13px;
        color: #26352d;
    }

    /* Tabel */
    .tabel-wrapper {
        overflow-x: auto;
    }

    .tabel-laporan {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-size: 12px;
    }

    .tabel-laporan th {
        background: #e4d9b8;
        color: #4e5945;
        font-weight: 600;
        text-align: left;
    }

    .tabel-laporan th,
    .tabel-laporan td {
        padding: 13px 12px;
        border-bottom: 1px solid #e8e4d6;
    }

    .tabel-laporan tbody {
        background: #ffffff;
    }

    .tabel-laporan tbody tr:hover {
        background: #f9f7ed;
    }

    .kosong {
        padding: 28px !important;
        color: #718078;
        text-align: center;
    }

    .pagination {
        margin-top: 18px;
    }

    @media (max-width: 1100px) {
        .filter-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .laporan-header h2 {
            font-size: 21px;
        }
    }
</style>

<div class="laporan">
    <div class="ringkasan-grid">
        {{-- Total pekerja --}}
        <div class="ringkasan-item">
            <div class="ringkasan-ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M21 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="ringkasan-isi">
                <p class="label">Total Pekerja</p>
                <strong>{{ number_format($totalPekerja, 0, ',', '.') }}</strong>
                <p class="satuan">Orang</p>
            </div>
        </div>

        {{-- Total catatan hasil kerja --}}
        <div class="ringkasan-item">
            <div class="ringkasan-ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg>
            </div>
            <div class="ringkasan-isi">
                <p class="label">Total Hasil Kerja</p>
                <strong>{{ number_format($totalData, 0, ',', '.') }}</strong>
                <p class="satuan">Data</p>
            </div>
        </div>

        {{-- Total produksi setiap jenis pekerjaan --}}
        @foreach ($ringkasanProduksi->where('jenis', '!=', 'Mandor') as $produksi)
        <div class="ringkasan-item">
            <div class="ringkasan-ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 5-6"/></svg>
            </div>
            <div class="ringkasan-isi">
                <p class="label">Hasil {{ $produksi['jenis'] === 'Pemanen' ? 'Pemanenan' : ($produksi['jenis'] === 'Pemberondol' ? 'Pemberondolan' : ($produksi['jenis'] === 'Pemupuk' ? 'Pemupukan' : ($produksi['jenis'] === 'Penyemprot' ? 'Penyemprotan' : $produksi['jenis']))) }}</p>
                <strong>{{ number_format($produksi['total'], 2, ',', '.') }}</strong>
                <p class="satuan">{{ $produksi['satuan'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter laporan --}}
    <div class="laporan-card">
        <h3>Filter Laporan</h3>

        <form method="GET" action="{{ route('admin.laporanhasilkerja') }}">
            <div class="filter-grid">
                <div class="filter-field">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') }}">
                </div>

                <div class="filter-field">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date"
                        id="tanggal_akhir"
                        name="tanggal_akhir"
                        value="{{ request('tanggal_akhir') }}">
                </div>

                <div class="filter-field">
                    <label for="blok">Blok</label>
                    <select id="blok" name="blok">
                        <option value="">Semua Blok</option>

                        @foreach ($bloks as $blok)
                        <option value="{{ $blok->id }}"
                            {{ request('blok') == $blok->id ? 'selected' : '' }}>
                            {{ $blok->nama_blok }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label for="jenis">Jenis Pekerjaan</label>
                    <select id="jenis" name="jenis">
                        <option value="">Semua Jenis</option>

                        @foreach ($jenisPekerjaans as $jenis)
                        <option value="{{ $jenis->id }}"
                            {{ request('jenis') == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->jenis }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label for="q">Nama Pekerja</label>
                    <input type="text"
                        id="q"
                        name="q"
                        placeholder="Cari nama pekerja..."
                        value="{{ request('q') }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-laporan btn-filter">
                    Terapkan Filter
                </button>

                <a href="{{ route('admin.laporanhasilkerja') }}"
                    class="btn-laporan btn-reset">
                    Reset
                </a>

                <a href="{{ route('admin.laporanhasilkerja.unduh', request()->query()) }}"
                    class="btn-laporan"
                    style="background: #e4f3e9; color: #28794e;">
                    Unduh Excel
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel laporan --}}
    <div class="laporan-card">
        <h3>Detail Hasil Kerja</h3>

        <div class="tabel-wrapper">
            <table class="tabel-laporan">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Nama Pekerja</th>
                        <th>Jenis Pekerjaan</th>
                        <th>Blok</th>
                        <th>Mandor</th>
                        <th>Jumlah Hasil</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($hasilKerjas as $hasil)
                    <tr>
                        <td>{{ $hasilKerjas->firstItem() + $loop->index }}</td>

                        <td>
                            {{ $hasil->tanggal?->format('d-m-Y') ?? '-' }}
                        </td>

                        <td>{{ $hasil->pekerja->nama ?? '-' }}</td>

                        <td>{{ $hasil->jenisPekerjaan->jenis ?? '-' }}</td>

                        <td>{{ $hasil->blok->nama_blok ?? '-' }}</td>

                        <td>{{ $hasil->mandor->nama ?? '-' }}</td>

                        <td>
                            {{ number_format($hasil->jumlah, 2, ',', '.') }}
                            {{ $hasil->jenisPekerjaan->satuan ?? '' }}
                        </td>

                        <td>{{ $hasil->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="kosong">
                            Belum ada data hasil kerja yang sesuai dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $hasilKerjas->links() }}
        </div>
    </div>
</div>
@endsection