@extends('layouts.orang-tua')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan')
@section('page-subtitle', 'Informasi laporan, pemanggilan, monitoring, dan evaluasi anak')

@section('content')

    @php
        $statusClass = [
            'baru' => 'bg-blue-50 text-blue-700 border-blue-200',
            'pemanggilan' => 'bg-amber-50 text-amber-700 border-amber-200',
            'monitoring' => 'bg-purple-50 text-purple-700 border-purple-200',
            'selesai' => 'bg-green-50 text-green-700 border-green-200',
            'dirujuk' => 'bg-red-50 text-red-700 border-red-200',
        ];

        $statusLabel = [
            'baru' => 'Baru',
            'pemanggilan' => 'Pemanggilan',
            'monitoring' => 'Monitoring',
            'selesai' => 'Selesai',
            'dirujuk' => 'Dirujuk',
        ];

        $status = $laporan->status ?? 'baru';
    @endphp

    <div class="mb-5 sm:mb-6 flex flex-wrap items-center gap-2 sm:gap-3">
        <a href="{{ route('orang_tua.laporan.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Detail Laporan</span>
    </div>

    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- HEADER LAPORAN --}}
        <div class="p-4 sm:p-6 border-b border-slate-200">
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-3">
                <span
                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass[$status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                    {{ $statusLabel[$status] ?? ucfirst($status) }}
                </span>

                <span class="text-xs sm:text-sm text-slate-400">
                    {{ $laporan->created_at->format('d M Y') }}
                </span>
            </div>

            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">
                {{ $laporan->judul_laporan }}
            </h1>

            <p class="text-sm text-slate-500 mt-2">
                Guru BK:
                <span class="font-semibold text-slate-700">
                    {{ $laporan->guruBk->nama ?? 'Belum ditangani' }}
                </span>
            </p>
        </div>

        {{-- DATA ANAK --}}
        <section class="p-4 sm:p-6 border-b border-slate-200">
            <div class="flex items-center gap-2 mb-4 sm:mb-5">
                <i data-feather="user" class="w-4 h-4 text-blue-700"></i>
                <h2 class="text-base font-extrabold text-slate-900">
                    Data Anak
                </h2>
            </div>

            <div class="flex flex-col md:flex-row gap-5 md:gap-6">

                {{-- FOTO --}}
                <div class="flex justify-center">
                    <img src="{{ $laporan->siswa->foto
        ? route('foto.siswa', $laporan->siswa->id)
        : asset('asset/default-user.png') }}"
                        class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl object-cover border border-slate-200 shadow-sm">
                </div>

                {{-- DATA --}}
                <div class="flex-1">
                    <div class="grid grid-cols-2 sm:grid-cols-2 gap-x-4 gap-y-4 sm:gap-x-8 sm:gap-y-5">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Nama Siswa
                            </p>
                            <p class="text-sm sm:text-base font-bold text-slate-900 mt-1">
                                {{ $laporan->siswa->nama_siswa }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                NIS
                            </p>
                            <p class="text-sm sm:text-base font-semibold text-slate-700 mt-1">
                                {{ $laporan->siswa->nis }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Kelas
                            </p>
                            <p class="text-sm sm:text-base font-semibold text-slate-700 mt-1">
                                {{ $laporan->siswa->kelas }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Jenis Kelamin
                            </p>
                            <p class="text-sm sm:text-base font-semibold text-slate-700 mt-1">
                                {{ $laporan->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Nama Orang Tua
                            </p>
                            <p class="text-sm sm:text-base font-semibold text-slate-700 mt-1">
                                {{ $laporan->siswa->nama_ortu }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                No. WhatsApp
                            </p>
                            <p class="text-sm sm:text-base font-semibold text-slate-700 mt-1">
                                {{ $laporan->siswa->no_whatsapp }}
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        {{-- DATA LAPORAN --}}
        <section class="p-4 sm:p-6 border-b border-slate-200">
            <div class="flex items-center gap-2 mb-4">
                <i data-feather="file-text" class="w-4 h-4 text-blue-700"></i>
                <h2 class="text-base font-extrabold text-slate-900">Data Laporan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Kategori</p>
                    <p class="text-sm font-semibold text-slate-800 mt-1">
                        {{ $laporan->kategori ? ucfirst($laporan->kategori) : 'Menunggu verifikasi BK' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Jenis Masalah</p>
                    <p class="text-sm font-semibold text-slate-800 mt-1">
                        {{ $laporan->jenis_masalah ?? 'Menunggu verifikasi BK' }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-xs font-bold text-slate-400 uppercase">Deskripsi</p>
                    <p class="text-sm text-slate-700 mt-1 leading-relaxed">
                        {{ $laporan->deskripsi }}
                    </p>
                </div>

                @if($laporan->bukti)
                    <div class="md:col-span-2">
                        <a href="{{ route('bukti.show', $laporan->id) }}" target="_blank"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-bold hover:bg-blue-800 transition">
                            <i data-feather="paperclip" class="w-4 h-4"></i>
                            Lihat Bukti
                        </a>
                    </div>
                @endif
            </div>
        </section>

        {{-- RIWAYAT PEMANGGILAN --}}
        <section class="p-4 sm:p-6 border-b border-slate-200">
            <div class="flex items-center gap-2 mb-4">
                <i data-feather="phone" class="w-4 h-4 text-blue-700"></i>
                <h2 class="text-base font-extrabold text-slate-900">Riwayat Pemanggilan</h2>
            </div>

            <div class="space-y-3 sm:space-y-4">
                @forelse($laporan->pemanggilan as $item)
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3.5 sm:p-4">
                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Tanggal</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1">
                                    {{ $item->tanggal_pemanggilan ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Waktu</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1">
                                    {{ $item->waktu_pemanggilan ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Pihak Dipanggil</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1">
                                    {{ $item->pihak_dipanggil ? str_replace('_', ' ', ucfirst($item->pihak_dipanggil)) : '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Status Kehadiran</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1">
                                    {{ $item->status_kehadiran ? str_replace('_', ' ', ucfirst($item->status_kehadiran)) : '-' }}
                                </p>
                            </div>
                        </div>

                        @if($item->tujuan)
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                <p class="text-xs font-bold text-slate-400 uppercase">Tujuan</p>
                                <p class="text-sm text-slate-700 mt-1 leading-relaxed">
                                    {{ $item->tujuan }}
                                </p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5 sm:p-6 text-center">
                        <i data-feather="calendar" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Belum ada jadwal pemanggilan.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- RIWAYAT MONITORING --}}
        <section class="p-4 sm:p-6 border-b border-slate-200">
            <div class="flex items-center gap-2 mb-4">
                <i data-feather="activity" class="w-4 h-4 text-blue-700"></i>
                <h2 class="text-base font-extrabold text-slate-900">Riwayat Monitoring</h2>
            </div>

            <div class="space-y-3 sm:space-y-4">
                @forelse($laporan->monitoring->where('status_monitoring', 'selesai')->sortBy('monitoring_ke') as $item)
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3.5 sm:p-4">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 sm:gap-3 mb-3">
                            <div>
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 text-xs font-bold">
                                    Monitoring ke-{{ $item->monitoring_ke }}
                                </span>

                                <p class="text-xs text-slate-400 mt-2">
                                    {{ $item->tanggal_monitoring }}
                                </p>
                            </div>

                            <span class="inline-flex w-fit px-3 py-1 rounded-full text-xs font-bold
                                                                @if($item->status_perkembangan == 'membaik')
                                                                    bg-green-50 text-green-700 border border-green-200
                                                                @elseif($item->status_perkembangan == 'stabil')
                                                                    bg-blue-50 text-blue-700 border border-blue-200
                                                                @else
                                                                    bg-red-50 text-red-700 border border-red-200
                                                                @endif">
                                {{ ucfirst($item->status_perkembangan) }}
                            </span>
                        </div>

                        <p class="text-sm text-slate-700 leading-relaxed">
                            {{ $item->catatan_perkembangan }}
                        </p>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5 sm:p-6 text-center">
                        <i data-feather="clock" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Belum ada data monitoring.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- EVALUASI AKHIR --}}
        <section class="p-4 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <i data-feather="check-square" class="w-4 h-4 text-blue-700"></i>
                <h2 class="text-base font-extrabold text-slate-900">Evaluasi Akhir</h2>
            </div>

            @if($laporan->evaluasi)
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3.5 sm:p-4">
                    <div class="mb-4">
                        <p class="text-xs font-bold text-slate-400 uppercase">Tanggal Evaluasi</p>
                        <p class="text-sm font-semibold text-slate-800 mt-1">
                            {{ $laporan->evaluasi->tanggal_evaluasi }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Hasil Evaluasi</p>
                        <p class="text-sm text-slate-700 mt-1 leading-relaxed">
                            {{ $laporan->evaluasi->hasil_evaluasi }}
                        </p>
                    </div>
                </div>
            @else
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5 sm:p-6 text-center">
                    <i data-feather="clipboard" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-400">Belum ada evaluasi akhir dari Guru BK.</p>
                </div>
            @endif
        </section>
    </div>

@endsection