@extends('layouts.bk')

@section('title', 'Detail Riwayat')
@section('page-title', 'Detail Riwayat')
@section('page-subtitle', 'Rekap lengkap penanganan permasalahan siswa')

@section('content')

@php
    $statusAkhir = optional($laporan->evaluasi)->status_akhir ?? $laporan->status;

    $statusClass = $statusAkhir === 'selesai'
        ? 'bg-green-100 text-green-700 border-green-200'
        : 'bg-red-100 text-red-700 border-red-200';
@endphp

{{-- BREADCRUMB --}}
<div class="mb-6 flex flex-wrap items-center gap-3">
    <a href="{{ route('bk.riwayat.index') }}"
        class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 font-medium">
        <i data-feather="arrow-left" class="w-4 h-4"></i>
        Kembali
    </a>
    <span class="text-slate-300 hidden sm:inline">/</span>
    <span class="text-sm text-slate-800 font-semibold hidden sm:inline">Detail Riwayat Permasalahan</span>

    <div class="w-full sm:w-auto sm:ml-auto flex flex-wrap items-center gap-2">
        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
            {{ ucfirst(str_replace('_', ' ', $statusAkhir)) }}
        </span>

        <a href="{{ route('bk.download.kasus', $laporan->id) }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors flex-1 sm:flex-none">
            <i data-feather="download" class="w-4 h-4"></i>
            <span class="whitespace-nowrap">Download Permasalahan</span>
        </a>
    </div>
</div>

{{-- SINGLE CARD --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100">

    {{-- DATA SISWA + DETAIL LAPORAN --}}
    <div class="p-4 sm:p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-7">

            {{-- DATA SISWA --}}
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">Data Siswa</h2>
                </div>

                <div class="w-full h-28 sm:h-36 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden mb-4">
                    @if(optional($laporan->siswa)->foto)
                        <img src="{{ route('foto.siswa', $laporan->siswa->id) }}"
                            class="w-full h-full object-cover" alt="Foto Siswa">
                    @else
                        <div class="text-center">
                            <i data-feather="user" class="w-8 h-8 text-slate-300 mx-auto mb-1"></i>
                            <p class="text-xs text-slate-400">Foto Siswa</p>
                        </div>
                    @endif
                </div>

                <div class="space-y-2.5">
                    @foreach([
                        ['Nama',        optional($laporan->siswa)->nama_siswa],
                        ['Kelas',       optional($laporan->siswa)->kelas],
                        ['NIS',         optional($laporan->siswa)->nis],
                        ['Orang Tua',   optional($laporan->siswa)->nama_ortu],
                        ['No WhatsApp', optional($laporan->siswa)->no_whatsapp],
                    ] as [$label, $val])
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                            <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                            <p class="text-sm font-semibold text-slate-700 break-words">{{ $val ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- DETAIL LAPORAN --}}
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <i data-feather="file-text" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">Detail Laporan</h2>
                </div>

                <div class="space-y-2.5">
                    @foreach([
                        ['Judul Laporan', $laporan->judul_laporan],
                        ['Kategori',      ucfirst($laporan->kategori ?? '-')],
                        ['Jenis Masalah', $laporan->jenis_masalah],
                        ['Guru BK',       optional($laporan->guruBk)->nama ?? 'Belum ditangani'],
                    ] as [$label, $val])
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                            <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                            <p class="text-sm font-semibold text-slate-700 break-words">{{ $val ?? '-' }}</p>
                        </div>
                    @endforeach

                    <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                        <p class="text-xs text-slate-400 mb-1">Status Akhir</p>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $statusAkhir)) }}
                        </span>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 min-h-[100px] sm:min-h-[120px]">
                        <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                        <p class="text-sm text-slate-700 leading-relaxed break-words">{{ $laporan->deskripsi ?? '-' }}</p>

                        @if($laporan->bukti)
                            <a href="{{ route('bukti.show', $laporan->id) }}" target="_blank"
                                class="inline-flex items-center justify-center gap-2 mt-4 px-4 py-2 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 w-full sm:w-auto">
                                <i data-feather="paperclip" class="w-4 h-4"></i>
                                Lihat Bukti
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- RIWAYAT PEMANGGILAN --}}
    <div class="p-4 sm:p-6">
        <div class="flex items-center gap-2 mb-5">
            <i data-feather="phone" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-sm font-bold text-slate-700">Riwayat Pemanggilan</h2>
        </div>

        @forelse($laporan->pemanggilan as $item)
            <div class="relative flex gap-3 sm:gap-4 {{ !$loop->last ? 'pb-6' : '' }}">

                {{-- TIMELINE --}}
                <div class="flex flex-col items-center shrink-0">
                    <div class="w-3 h-3 rounded-full bg-blue-500 border-2 border-white ring-2 ring-blue-200 mt-1 shrink-0"></div>
                    @if(!$loop->last)
                        <div class="w-px flex-1 bg-slate-200 mt-1"></div>
                    @endif
                </div>

                {{-- KONTEN --}}
                <div class="flex-1 min-w-0 border border-slate-200 bg-slate-50 rounded-xl p-3 sm:p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Tanggal</p>
                            <p class="text-sm font-semibold text-slate-700 break-words">{{ $item->tanggal_pemanggilan }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Waktu</p>
                            <p class="text-sm font-semibold text-slate-700 break-words">{{ $item->waktu_pemanggilan }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Pihak</p>
                            <p class="text-sm font-semibold text-slate-700 break-words">
                                {{ str_replace('_', ' ', ucfirst($item->pihak_dipanggil)) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Kehadiran</p>
                            <p class="text-sm font-semibold text-slate-700 break-words">
                                {{ str_replace('_', ' ', ucfirst($item->status_kehadiran)) }}
                            </p>
                        </div>
                    </div>

                    @if($item->tujuan)
                        <p class="text-sm text-slate-600 leading-relaxed break-words">{{ $item->tujuan }}</p>
                    @endif

                    @if($item->catatan)
                        <p class="text-sm text-slate-600 leading-relaxed mt-2 break-words">
                            <span class="font-semibold">Catatan:</span> {{ $item->catatan }}
                        </p>
                    @endif
                </div>

            </div>
        @empty
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 sm:p-8 text-center">
                <i data-feather="calendar" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm text-slate-400">Belum ada riwayat pemanggilan.</p>
            </div>
        @endforelse
    </div>

    {{-- RIWAYAT MONITORING --}}
    <div class="p-4 sm:p-6">
        <div class="flex items-center gap-2 mb-5">
            <i data-feather="activity" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-sm font-bold text-slate-700">Riwayat Monitoring</h2>
        </div>

        @forelse($laporan->monitoring->where('status_monitoring', 'selesai')->sortBy('monitoring_ke') as $item)
            <div class="relative flex gap-3 sm:gap-4 {{ !$loop->last ? 'pb-6' : '' }}">

                {{-- TIMELINE --}}
                <div class="flex flex-col items-center shrink-0">
                    <div class="w-3 h-3 rounded-full mt-1 shrink-0 border-2 border-white ring-2
                        @if($item->status_perkembangan === 'membaik') bg-green-500 ring-green-200
                        @elseif($item->status_perkembangan === 'menurun') bg-red-500 ring-red-200
                        @else bg-blue-500 ring-blue-200
                        @endif">
                    </div>
                    @if(!$loop->last)
                        <div class="w-px flex-1 bg-slate-200 mt-1"></div>
                    @endif
                </div>

                {{-- KONTEN --}}
                <div class="flex-1 min-w-0 border border-slate-200 bg-slate-50 rounded-xl p-3 sm:p-4">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                        <div>
                            <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                                Monitoring ke-{{ $item->monitoring_ke }}
                            </span>
                            <p class="text-xs text-slate-400 mt-1.5">
                                {{ \Carbon\Carbon::parse($item->tanggal_monitoring)->translatedFormat('d F Y') }}
                            </p>
                        </div>

                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($item->status_perkembangan === 'membaik') bg-green-100 text-green-700
                            @elseif($item->status_perkembangan === 'menurun') bg-red-100 text-red-700
                            @else bg-blue-100 text-blue-700
                            @endif">
                            {{ ucfirst($item->status_perkembangan) }}
                        </span>
                    </div>

                    <p class="text-sm text-slate-600 leading-relaxed break-words">
                        {{ $item->catatan_perkembangan ?? '-' }}
                    </p>
                </div>

            </div>
        @empty
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 sm:p-8 text-center">
                <i data-feather="clock" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm text-slate-400">Belum ada riwayat monitoring.</p>
            </div>
        @endforelse
    </div>

    {{-- EVALUASI AKHIR --}}
    <div class="p-4 sm:p-6">
        <div class="flex items-center gap-2 mb-5">
            <i data-feather="check-square" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-sm font-bold text-slate-700">Evaluasi Akhir</h2>
        </div>

        @if($laporan->evaluasi)
            <div class="space-y-3">
                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                    <p class="text-xs text-slate-400 mb-0.5">Tanggal Evaluasi</p>
                    <p class="text-sm font-semibold text-slate-700">
                        {{ \Carbon\Carbon::parse($laporan->evaluasi->tanggal_evaluasi)->translatedFormat('d F Y') }}
                    </p>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 min-h-[100px] sm:min-h-[120px]">
                    <p class="text-xs text-slate-400 mb-1">Hasil Evaluasi</p>
                    <p class="text-sm text-slate-700 leading-relaxed break-words">
                        {{ $laporan->evaluasi->hasil_evaluasi ?? '-' }}
                    </p>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                    <p class="text-xs text-slate-400 mb-1">Status Akhir</p>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $laporan->evaluasi->status_akhir)) }}
                    </span>
                </div>
            </div>
        @else
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 sm:p-8 text-center">
                <i data-feather="clipboard" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm text-slate-400">Belum ada data evaluasi.</p>
            </div>
        @endif
    </div>

</div>

@endsection