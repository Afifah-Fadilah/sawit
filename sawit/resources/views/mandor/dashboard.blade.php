@extends('mandor.layouts.mandor')

@section('title', 'Dashboard')

@section('content')
    <div style="padding: 20px 4px 0;">

        <!-- Kartu ringkasan -->
        <div style="display:flex; gap:12px; margin-bottom:18px;">
            <div style="flex:1; background:#fefae0; border-radius:18px; padding:16px; text-align:center;">
                <p style="margin:0 0 8px; font-size:13px; font-weight:600; color:#5f6f52;">Total Blok</p>
                <div style="font-size:26px; font-weight:700; color:#3f4a35;">{{ $totalBlok }}</div>
                <p style="margin:2px 0 0; font-size:12px; color:#7a8a68;">Blok</p>
            </div>
            <div style="flex:1; background:#fefae0; border-radius:18px; padding:16px; text-align:center;">
                <p style="margin:0 0 8px; font-size:13px; font-weight:600; color:#5f6f52;">Total Pekerja</p>
                <div style="font-size:26px; font-weight:700; color:#3f4a35;">{{ $totalPekerja }}</div>
                <p style="margin:2px 0 0; font-size:12px; color:#7a8a68;">Orang</p>
            </div>
        </div>

        <!-- Log aktivitas -->
        <div style="background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 4px 12px rgba(95,111,82,.1);">
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:14px 18px; background:#eef1e0;">
                <span style="font-weight:700; color:#3f4a35; font-size:14px;">Log Aktivitas</span>
                <a href="#"
                    style="font-size:12px; font-weight:600; color:#5f6f52; background:#dfe5cf; padding:6px 12px; border-radius:20px; text-decoration:none;">Lihat
                    Semua</a>
            </div>

            @php
                $logs = [
                    ['nama' => 'Bowo', 'kerja' => 'Pemberondol', 'hasil' => '7 karung', 'waktu' => '7 mnt lalu'],
                    ['nama' => 'Andi', 'kerja' => 'Pemanen', 'hasil' => '150 kg', 'waktu' => '10 mnt lalu'],
                    ['nama' => 'Fufu', 'kerja' => 'Pemupuk', 'hasil' => '3 sak', 'waktu' => '14 mnt lalu'],
                    ['nama' => 'Fafa', 'kerja' => 'Pemupuk', 'hasil' => '5 sak', 'waktu' => '21 mnt lalu'],
                    ['nama' => 'Oslo', 'kerja' => 'Penyemprot', 'hasil' => '1 kep', 'waktu' => '23 mnt lalu'],
                ];
            @endphp

            @foreach ($logs as $log)
                <div
                    style="display:flex; align-items:center; justify-content:space-between; padding:12px 18px; border-top:1px solid #f0efe6;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span
                            style="width:30px; height:30px; border-radius:50%; background:#eef1e0; display:grid; place-items:center; color:#5f6f52;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <div>
                            <p style="margin:0; font-size:13.5px; font-weight:600; color:#b5651d;">{{ $log['nama'] }} -
                                {{ $log['kerja'] }}</p>
                            <p style="margin:0; font-size:12px; color:#8a8a7a;">Hasil: {{ $log['hasil'] }}</p>
                        </div>
                    </div>
                    <span style="font-size:11.5px; color:#9a9a8a; white-space:nowrap;">({{ $log['waktu'] }})</span>
                </div>
            @endforeach
        </div>

    </div>
@endsection
