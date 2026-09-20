@extends('mandor.layouts.mandor')

@section('title', 'Dashboard')

@section('content')
    <div style="padding: 20px 4px 0;">

        @php
            $statsMandor = [
                [
                    'label' => 'Total Blok',
                    'value' => $totalBlok,
                    'unit' => 'Blok',
                    'icon' =>
                        '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/>',
                ],
                [
                    'label' => 'Total Pekerja',
                    'value' => $totalPekerja,
                    'unit' => 'Orang',
                    'icon' =>
                        '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                ],
            ];
        @endphp

        <!-- Kartu ringkasan -->
        <div style="display:flex; gap:12px; margin-bottom:18px;">
            @foreach ($statsMandor as $s)
                <div
                    style="flex:1; background:#fefae0; border-radius:18px; padding:16px; text-align:center; box-shadow:0 6px 16px rgba(95,111,82,.16);">
                    <p style="margin:0 0 10px; font-size:13px; font-weight:600; color:#5f6f52;">{{ $s['label'] }}</p>
                    <div style="display:flex; align-items:center; justify-content:center; gap:12px;">
                        <span
                            style="display:grid; place-items:center; flex:none; width:42px; height:42px; border-radius:12px; background:#b8926e; color:#fefae0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                {!! $s['icon'] !!}
                            </svg>
                        </span>
                        <div style="text-align:left;">
                            <div style="font-size:26px; font-weight:700; color:#3f4a35; line-height:1;">
                                {{ number_format($s['value'], 0, ',', '.') }}</div>
                            <p style="margin:3px 0 0; font-size:12px; color:#7a8a68;">{{ $s['unit'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Log aktivitas -->
        <div style="background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 4px 12px rgba(95,111,82,.1);">
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:14px 18px; background:#eef1e0;">
                <span style="font-weight:700; color:#3f4a35; font-size:14px;">Log Aktivitas</span>
                <a href="{{ url('mandor/riwayat') }}"
                    style="font-size:12px; font-weight:600; color:#5f6f52; background:#dfe5cf; padding:6px 12px; border-radius:20px; text-decoration:none;">Lihat
                    Semua</a>
            </div>

            @forelse ($logs as $log)
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
            @empty
                <div style="padding:24px 18px; text-align:center; font-size:12.5px; color:#8a8a7a;">
                    Belum ada aktivitas input hasil kerja hari ini.
                </div>
            @endforelse
        </div>

    </div>
@endsection