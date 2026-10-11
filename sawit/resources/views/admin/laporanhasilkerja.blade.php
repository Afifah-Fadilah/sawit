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
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
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

    .ringkasan-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .ringkasan-item {
        padding: 20px;
        background: #fff;
        border: 1px solid #e6ece7;
        border-radius: 12px;
    }

    .ringkasan-item p {
        margin: 0 0 10px;
        color: #718078;
        font-size: 12px;
    }

    .ringkasan-item strong {
        font-size: 25px;
    }

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
        background: #f4f7f4;
        color: #536258;
        font-weight: 600;
        text-align: left;
    }

    .tabel-laporan th,
    .tabel-laporan td {
        padding: 13px 12px;
        border-bottom: 1px solid #edf0ed;
    }

    .tabel-laporan tbody tr:hover {
        background: #fafcfb;
    }

    .kosong {
        padding: 28px !important;
        color: #718078;
        text-align: center;
    }

    .pagination {
        margin-top: 18px;
    }

    @media (max-width: 900px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {

        .filter-grid,
        .ringkasan-grid {
            grid-template-columns: 1fr;
        }

        .laporan-header h2 {
            font-size: 21px;
        }
    }


    /* Warna dasar halaman laporan */

    .laporan {
        font-family: 'Poppins', sans-serif;
        color: #26352d;
    }

    .laporan-card {
        background: #fff;
        border: 1px solid #e6ece7;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }


    /* Kartu ringkasan */

    .ringkasan-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .ringkasan-item {
        padding: 20px;
        background: #f3f0d8;
        border: 1px solid #e5ddba;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(91, 100, 68, 0.12);
    }

    .ringkasan-item p {
        margin: 0 0 10px;
        color: #718078;
        font-size: 12px;
    }

    .ringkasan-item strong {
        font-size: 25px;
        color: #26352d;
    }



    /* Efek saat kartu disentuh kursor */
    .ringkasan-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 14px rgba(80, 90, 60, 0.10);
    }

    /* Header tabel berwarna cokelat krem */
    .tabel-laporan th {
        background: #e4d9b8;
        color: #4e5945;
        font-weight: 600;
        text-align: left;
    }

    /* Isi tabel tetap putih agar mudah dibaca */
    .tabel-laporan tbody {
        background: #ffffff;
    }

    /* Garis tabel dibuat lebih lembut */
    .tabel-laporan th,
    .tabel-laporan td {
        border-bottom: 1px solid #e8e4d6;
    }

    /* Efek saat kursor diarahkan ke baris tabel */
    .tabel-laporan tbody tr:hover {
        background: #f9f7ed;
    }

    /* Judul bagian */
    .laporan-card h3 {
        color: #657455;
    }
</style>

<div class="laporan">
    <div class="laporan-header">
        <h2>Laporan Hasil Kerja</h2>
        <p>Lihat dan pantau pencatatan hasil kerja pekerja perkebunan.</p>
    </div>

    {{-- Filter laporan --}}
    <div class="laporan-card">
        <h3>Filter Laporan</h3>

        <form method="GET"
            action="{{ route('admin.laporanhasilkerja') }}">

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


            {{-- Ringkasan --}}

            <div class="ringkasan-grid">
                {{-- Total pekerja --}}
                <div class="ringkasan-item">
                    <p>Total Pekerja</p>
                    <strong>{{ number_format($totalPekerja, 0, ',', '.') }}</strong>
                    <p>Orang</p>
                </div>

                {{-- Total catatan hasil kerja --}}
                <div class="ringkasan-item">
                    <p>Total Catatan Hasil Kerja</p>
                    <strong>{{ number_format($totalData, 0, ',', '.') }}</strong>
                    <p>Data</p>
                </div>

                {{-- Total produksi setiap jenis pekerjaan --}}
                @foreach ($ringkasanProduksi as $produksi)
                <div class="ringkasan-item">

                    <p>Hasil {{ $produksi['jenis'] === 'Pemanen' ? 'Pemanenan' : ($produksi['jenis'] === 'Pemberondol' ? 'Pemberondolan' : ($produksi['jenis'] === 'Pemupuk' ? 'Pemupukan' : ($produksi['jenis'] === 'Penyemprot' ? 'Penyemprotan' : $produksi['jenis']))) }}</p>

                    <strong>
                        {{ number_format($produksi['total'], 2, ',', '.') }}
                    </strong>
                    <p>{{ $produksi['satuan'] }}</p>
                </div>
                @endforeach
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