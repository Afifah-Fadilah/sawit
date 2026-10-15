@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard">
    <p class="sapaan">Selamat datang, Admin!</p>

    <div class="statistik-grid">
        <div class="statistik-card">
            <div class="ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="8" width="16" height="13" rx="3"/><path d="M8.5 8V6.5a3.5 3.5 0 0 1 7 0V8"/></svg>
            </div>
            <div>
                <p>Total Jenis Pekerjaan</p>
                <h2>{{ $totalJenisPekerjaan }}</h2>
                <small>Jenis</small>
            </div>
        </div>

        <div class="statistik-card">
            <div class="ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3"/><circle cx="5.5" cy="9.5" r="2.2"/><circle cx="18.5" cy="9.5" r="2.2"/><path d="M7 20v-1.5A3.5 3.5 0 0 1 10.5 15h3a3.5 3.5 0 0 1 3.5 3.5V20"/><path d="M2.5 18.5v-.8A2.7 2.7 0 0 1 5.2 15M21.5 18.5v-.8a2.7 2.7 0 0 0-2.7-2.7"/></svg>
            </div>
            <div>
                <p>Total Pekerja</p>
                <h2>{{ $totalPekerja }}</h2>
                <small>Orang</small>
            </div>
        </div>

        <div class="statistik-card">
            <div class="ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>
            </div>
            <div>
                <p>Total Blok</p>
                <h2>{{ $totalBlok }}</h2>
                <small>Blok</small>
            </div>
        </div>

        <div class="statistik-card">
            <div class="ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 6.5v11"/><path d="M14.8 9.4c-.4-.9-1.4-1.4-2.8-1.4-1.6 0-2.7.8-2.7 2s1 1.7 2.7 2c1.7.3 2.8.8 2.8 2.1 0 1.2-1.2 2-2.8 2-1.4 0-2.5-.6-2.9-1.5"/></svg>
            </div>
            <div>
                <p>Total Upah</p>
                <h2 class="nominal">
                    Rp {{ number_format($totalUpah, 0, ',', '.') }}
                </h2>
                <small>Total Upah Laporan Final</small>
            </div>
        </div>
    </div>

    <div class="konten-grid">
        <section class="panel">
            <div class="panel-header">
                <h3>Rekap Upah Per Jenis Pekerjaan</h3>

                {{-- Pilih tanggal (default hari ini) --}}
                <form method="GET" class="tanggal-chip">
                    <input type="hidden" name="tanggal_hasil" value="{{ $tanggalHasil }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}</span>
                    <input type="date"
                           name="tanggal"
                           value="{{ $tanggal }}"
                           aria-label="Pilih tanggal"
                           onclick="this.showPicker && this.showPicker()"
                           onchange="this.form.submit()">
                </form>
            </div>

            <div class="grafik">
                @if (empty($rekapUpah))
                    <p class="grafik-kosong">Belum ada data upah pada tanggal ini.</p>
                @else
                    <canvas id="grafikUpah"></canvas>
                @endif
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <h3>Hasil Kerja</h3>

                <div class="panel-aksi">
                    {{-- Pilih tanggal khusus tabel (terpisah dari grafik) --}}
                    <form method="GET" class="tanggal-chip">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <span>{{ \Carbon\Carbon::parse($tanggalHasil)->locale('id')->translatedFormat('d F Y') }}</span>
                        <input type="date"
                               name="tanggal_hasil"
                               value="{{ $tanggalHasil }}"
                               aria-label="Pilih tanggal hasil kerja"
                               onclick="this.showPicker && this.showPicker()"
                               onchange="this.form.submit()">
                    </form>

                    <a href="{{ route('admin.laporanhasilkerja') }}"
                       class="btn-lihat">
                        Lihat Semua
                    </a>
                </div>
            </div>

            <div class="tabel-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Blok</th>
                            <th>Jenis Pekerjaan</th>
                            <th>Pekerja</th>
                            <th>Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hasilKerjaTerbaru as $hasil)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($hasil->tanggal)->locale('id')->translatedFormat('d F Y') }}
                                </td>
                                <td>{{ $hasil->nama_blok }}</td>
                                <td>{{ $hasil->jenis }}</td>
                                <td>{{ $hasil->nama_pekerja }}</td>
                                <td>
                                    {{ number_format($hasil->jumlah, 2, ',', '.') }}
                                    {{ $hasil->satuan }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="kosong">
                                    Belum ada data hasil kerja pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<style>
    .dashboard {
        /* Tinggi area atas (top bar + padding halaman). Sesuaikan bila masih ada scroll. */
        --tinggi-atas: 130px;

        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--tinggi-atas));
        min-height: 520px;
        font-family: 'Poppins', sans-serif;
        color: #26352d;
    }

    .sapaan {
        margin: 0 0 14px;
        color: #657455;
        font-size: 14px;
    }

    .statistik-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 14px;
    }

    .statistik-card {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        min-height: 86px;
        padding: 12px 16px;
        background: #f3f0d8;
        border: 1px solid #e6e1c8;
        border-radius: 20px;
        box-shadow: 0 4px 6px #0000001f;
    }

    .ikon {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #b88a64;
        box-shadow: 0 3px 6px rgba(120, 85, 50, 0.28);
        color: #fff;
    }

    .ikon svg {
        width: 24px;
        height: 24px;
    }

    .statistik-card p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 600;
    }

    .statistik-card h2 {
        margin: 0 0 4px;
        color: #000;
        font-size: 24px;
        line-height: 1.2;
    }

    .statistik-card h2.nominal {
        font-size: clamp(13px, 1.2vw, 19px);
        overflow-wrap: anywhere;
    }

    .statistik-card small {
        font-size: 10px;
        font-weight: 600;
    }

    .konten-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: minmax(0, 1fr);
        gap: 16px;
        flex: 1;
        min-height: 0;
    }

    .panel {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 0;
        padding: 14px 16px;
        background: #f3f0d8;
        border: 1px solid #e6e1c8;
        border-radius: 20px;
        box-shadow: 0 4px 6px #0000001f;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .panel-header h3 {
        margin: 0;
        color: #657455;
        font-size: 13px;
    }

    /* Tombol tanggal di header grafik */
    .tanggal-chip {
        position: relative;
        margin: 0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border: 1px solid #d9d4bc;
        border-radius: 9px;
        background: #fff;
        color: #26352d;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }

    .tanggal-chip svg {
        width: 15px;
        height: 15px;
    }

    /* Input tanggal transparan di atas chip, supaya klik di mana saja membuka kalender */
    .tanggal-chip input[type="date"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .grafik {
        position: relative;
        flex: 1;
        min-height: 0;
        padding: 14px 16px 10px;
        border-radius: 14px;
        background: #fff;
    }

    .panel-aksi {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .grafik-kosong {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        margin: 0;
        color: #718078;
        font-size: 12px;
    }

    .btn-lihat {
        padding: 9px 17px;
        border-radius: 9px;
        background: #657455;
        color: white;
        font-size: 10px;
        text-decoration: none;
        white-space: nowrap;
    }

    .tabel-wrapper {
        flex: 1;
        min-height: 0;
        overflow: auto;
        border-radius: 8px;
        background: white;
    }

    .tabel-wrapper table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-size: 10px;
    }

    .tabel-wrapper th {
        position: sticky;
        top: 0;
        padding: 10px;
        background: #e4d9b8;
        text-align: left;
    }

    .tabel-wrapper td {
        padding: 10px;
        border-bottom: 1px solid #eeeadd;
    }

    .kosong {
        color: #718078;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .dashboard {
            display: block;
            height: auto;
            min-height: 0;
        }

        .konten-grid {
            grid-template-rows: none;
        }

        .panel {
            display: block;
        }

        .grafik {
            height: 260px;
        }

        .statistik-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .konten-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 550px) {
        .statistik-grid {
            grid-template-columns: 1fr;
        }

        .panel-header {
            flex-wrap: wrap;
        }
    }
</style>

{{-- Chart.js untuk grafik --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const elemenGrafik = document.getElementById('grafikUpah');

        if (!elemenGrafik || typeof Chart === 'undefined') {
            return;
        }

        const labels = @json(array_keys($rekapUpah));
        const dataUpah = @json(array_values($rekapUpah));
        const formatAngka = new Intl.NumberFormat('id-ID');

        // Menampilkan nilai (hijau) di atas setiap batang
        const labelNilai = {
            id: 'labelNilai',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                ctx.save();
                ctx.font = '600 9px Poppins, sans-serif';
                ctx.fillStyle = '#22c55e';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                chart.getDatasetMeta(0).data.forEach(function (batang, i) {
                    const nilai = chart.data.datasets[0].data[i];
                    ctx.fillText(formatAngka.format(nilai), batang.x, batang.y - 6);
                });

                ctx.restore();
            }
        };

        new Chart(elemenGrafik, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Upah',
                    data: dataUpah,
                    backgroundColor: '#ffbd00',
                    borderRadius: 2,
                    maxBarThickness: 34
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 18 }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' + formatAngka.format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#26352d',
                            font: { size: 11 },
                            padding: 8
                        }
                    },
                    y: {
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        grid: {
                            color: '#e5e7eb',
                            borderDash: [4, 4],
                            drawTicks: false
                        },
                        ticks: {
                            color: '#26352d',
                            font: { size: 10 },
                            padding: 10,
                            callback: function (value) {
                                return formatAngka.format(value);
                            }
                        }
                    }
                }
            },
            plugins: [labelNilai]
        });
    });
</script>
@endsection