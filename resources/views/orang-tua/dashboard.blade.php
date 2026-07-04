@extends('layouts.orang-tua')

@section('title', 'Dashboard Orang Tua')
@section('page-title', 'Dashboard Orang Tua')
@section('page-subtitle', 'Pantau laporan dan perkembangan anak')

@section('content')
    @php
        $jam = now('Asia/Jakarta')->hour;

        $salam = match (true) {
            $jam >= 5 && $jam < 11 => 'Selamat Pagi',
            $jam >= 11 && $jam < 15 => 'Selamat Siang',
            $jam >= 15 && $jam < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };

        $statusLabel = [
            'baru' => 'Baru Dilaporkan',
            'pemanggilan' => 'Pemanggilan',
            'monitoring' => 'Dalam Monitoring',
            'selesai' => 'Selesai',
            'dirujuk' => 'Dirujuk',
        ];

        $statusClass = [
            'baru' => 'bg-blue-100 text-blue-700 border-blue-200',
            'pemanggilan' => 'bg-amber-100 text-amber-700 border-amber-200',
            'monitoring' => 'bg-green-100 text-green-700 border-green-200',
            'selesai' => 'bg-slate-100 text-slate-600 border-slate-200',
            'dirujuk' => 'bg-orange-100 text-orange-700 border-orange-200',
        ];

        $laporanUtama = $laporan->whereNotIn('status', ['selesai'])->first();
        $adaLaporanAktif = !is_null($laporanUtama);
    @endphp

    @if($adaLaporanAktif)
        @php
            $badgeLabel = $statusLabel[$laporanUtama->status] ?? ucfirst($laporanUtama->status);
            $badgeClass = $statusClass[$laporanUtama->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
            $pemanggilanTerbaru = $laporanUtama->pemanggilan->sortByDesc('tanggal_pemanggilan')->first();
        @endphp

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">
            <div class="flex items-start gap-5">
                <div
                    class="w-20 h-20 rounded-xl overflow-hidden bg-blue-50 border border-slate-200 flex-shrink-0 flex items-center justify-center">
                    @if($laporanUtama->siswa->foto)
                        <img src="{{ asset('storage/' . $laporanUtama->siswa->foto) }}" class="w-full h-full object-cover"
                            alt="Foto Siswa">
                    @else
                        <i data-feather="user" class="w-8 h-8 text-blue-300"></i>
                    @endif
                </div>

                <div class="flex-1">
                    <p class="font-bold text-slate-800 text-base">{{ $laporanUtama->siswa->nama_siswa }}</p>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Kelas {{ $laporanUtama->siswa->kelas }} · NIS {{ $laporanUtama->siswa->nis }}
                    </p>
                    <p class="text-sm text-slate-600 mt-2">
                        Jenis Masalah:
                        <span class="font-semibold text-slate-800">
                            {{ $laporanUtama->jenis_masalah ?? 'Menunggu verifikasi Guru BK' }}
                        </span>
                    </p>
                </div>

                <a href="{{ route('orang_tua.laporan.show', $laporanUtama->id) }}"
                    class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 bg-blue-50 hover:bg-blue-700 text-blue-700 hover:text-white text-xs font-semibold rounded-xl border border-blue-100 hover:border-blue-700 transition-all">
                    <i data-feather="eye" class="w-3.5 h-3.5"></i>
                    Lihat Detail
                </a>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="bg-slate-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-slate-400 mb-1.5">Status Laporan</p>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>
                </div>

                <div class="bg-slate-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-slate-400 mb-1.5">Jadwal Pemanggilan</p>
                    <p class="text-sm font-semibold text-slate-800">
                        @if($pemanggilanTerbaru)
                            {{ $pemanggilanTerbaru->tanggal_pemanggilan }} · {{ $pemanggilanTerbaru->waktu_pemanggilan }}
                        @else
                            Belum dijadwalkan
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="mb-5">
            <div class="text-center mb-6">
                <p class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-1.5">
                    Berikut tata cara pembuatan laporan
                </p>
                <h2 class="text-2xl font-extrabold text-slate-800">
                    Buat Laporan <span class="text-blue-600">Konsultasi</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Langkah 1 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="relative bg-blue-50 mx-4 mt-4 rounded-xl overflow-hidden" style="aspect-ratio: 4/3;">
                        <svg viewBox="0 0 200 150" class="w-full h-full">
                            <rect x="0" y="0" width="200" height="150" fill="#EFF6FF" />
                            <text x="14" y="24" font-size="20" fill="#BFDBFE" font-family="Arial, sans-serif"
                                font-weight="bold">+</text>
                            <text x="170" y="140" font-size="20" fill="#BFDBFE" font-family="Arial, sans-serif"
                                font-weight="bold">+</text>
                            <circle cx="100" cy="75" r="42" fill="#2563EB" />
                            <rect x="94" y="55" width="12" height="40" rx="6" fill="white" />
                            <rect x="80" y="69" width="40" height="12" rx="6" fill="white" />
                        </svg>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Mulai</p>
                        </div>
                        <p class="font-bold text-slate-800 text-base mb-1.5">Klik Tombol Buat Laporan</p>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Tekan tombol bulat di pojok kanan bawah untuk memulai laporan konsultasi baru.
                        </p>
                    </div>
                </div>

                {{-- Langkah 2 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="relative bg-blue-50 mx-4 mt-4 rounded-xl overflow-hidden" style="aspect-ratio: 4/3;">
                        <svg viewBox="0 0 200 150" class="w-full h-full">
                            <rect x="0" y="0" width="200" height="150" fill="#EFF6FF" />
                            <rect x="60" y="24" width="80" height="102" rx="10" fill="white" stroke="#BFDBFE"
                                stroke-width="2" />
                            <rect x="74" y="42" width="52" height="6" rx="3" fill="#93C5FD" />
                            <rect x="74" y="56" width="40" height="6" rx="3" fill="#DBEAFE" />
                            <rect x="74" y="76" width="52" height="6" rx="3" fill="#93C5FD" />
                            <rect x="74" y="90" width="30" height="6" rx="3" fill="#DBEAFE" />
                            <rect x="74" y="104" width="52" height="14" rx="7" fill="#2563EB" />
                        </svg>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Isi Data</p>
                        </div>
                        <p class="font-bold text-slate-800 text-base mb-1.5">Lengkapi Detail Masalah</p>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Pilih data anak dan jelaskan permasalahan yang ingin dikonsultasikan secara singkat.
                        </p>
                    </div>
                </div>

                {{-- Langkah 3 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="relative bg-blue-50 mx-4 mt-4 rounded-xl overflow-hidden" style="aspect-ratio: 4/3;">
                        <svg viewBox="0 0 200 150" class="w-full h-full">
                            <rect x="0" y="0" width="200" height="150" fill="#EFF6FF" />
                            <circle cx="100" cy="70" r="38" fill="white" stroke="#BFDBFE" stroke-width="2" />
                            <path d="M82 70 L94 82 L120 56" stroke="#2563EB" stroke-width="7" fill="none" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <rect x="70" y="122" width="60" height="10" rx="5" fill="#93C5FD" />
                        </svg>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Selesai</p>
                        </div>
                        <p class="font-bold text-slate-800 text-base mb-1.5">Kirim & Pantau Prosesnya</p>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Kirim laporan Anda, lalu pantau status dan perkembangannya langsung dari dashboard ini.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-5">
                <a href="{{ route('orang_tua.laporan.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
                    <i data-feather="plus" class="w-4 h-4"></i>
                    Buat Laporan Sekarang
                </a>
            </div>
        </div>
    @endif
@endsection