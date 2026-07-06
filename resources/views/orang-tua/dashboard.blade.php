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

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 mb-5">
            <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-5">
                <div class="flex items-start gap-4 w-full sm:w-auto">
                    <div
                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-blue-50 border border-slate-200 flex-shrink-0 flex items-center justify-center">
                        @if($laporanUtama->siswa->foto)
                            <img src="{{ route('foto.siswa', $laporanUtama->siswa->id) }}" class="w-full h-full object-cover"
                                alt="Foto Siswa">
                        @else
                            <i data-feather="user" class="w-7 h-7 sm:w-8 sm:h-8 text-blue-300"></i>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 sm:hidden">
                        <p class="font-bold text-slate-800 text-sm truncate">{{ $laporanUtama->siswa->nama_siswa }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Kelas {{ $laporanUtama->siswa->kelas }} · NIS {{ $laporanUtama->siswa->nis }}
                        </p>
                    </div>
                </div>

                <div class="flex-1 min-w-0 w-full">
                    <p class="hidden sm:block font-bold text-slate-800 text-base">
                        {{ $laporanUtama->siswa->nama_siswa }}
                    </p>
                    <p class="hidden sm:block text-sm text-slate-500 mt-0.5">
                        Kelas {{ $laporanUtama->siswa->kelas }} · NIS {{ $laporanUtama->siswa->nis }}
                    </p>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 break-words">
                        Jenis Masalah:
                        <span class="font-semibold text-slate-800">
                            {{ $laporanUtama->jenis_masalah ?? 'Menunggu verifikasi Guru BK' }}
                        </span>
                    </p>
                </div>

                <a href="{{ route('orang_tua.laporan.show', $laporanUtama->id) }}"
                    class="flex-shrink-0 w-full sm:w-auto flex items-center justify-center gap-1.5 px-4 py-2.5 sm:py-2 bg-blue-50 hover:bg-blue-700 text-blue-700 hover:text-white text-xs font-semibold rounded-xl border border-blue-100 hover:border-blue-700 transition-all">
                    <i data-feather="eye" class="w-3.5 h-3.5"></i>
                    Lihat Detail
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
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
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-5">
            <div class="relative aspect-video sm:aspect-auto">
                <video autoplay muted loop playsinline class="w-full h-full sm:h-auto object-cover">
                    <source src="{{ asset('asset/ayo_bercerita.mp4') }}" type="video/mp4">
                </video>

                <div class="absolute inset-0 bg-blue-900/40"></div>

                <a href="{{ route('orang_tua.laporan.create') }}"
                    class="absolute bottom-4 right-4 sm:bottom-5 sm:right-5 w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-105"
                    title="Buat Laporan Baru">
                    <i data-feather="plus" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </a>
            </div>
        </div>
    @endif
@endsection