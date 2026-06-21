@extends('layouts.admin')
@section('title', 'Detail Arsip Siswa')
@section('page-title', 'Detail Arsip Siswa')
@section('page-subtitle', 'Riwayat lengkap penanganan BK')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.arsip.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Arsip
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">{{ $siswa->nama_siswa }}</span>
    </div>

    {{-- Profil Siswa --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
        <div class="flex gap-5 items-start">
            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200">
                @if($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-4xl font-bold text-slate-300">
                        {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <h1 class="text-xl font-bold text-slate-900">{{ $siswa->nama_siswa }}</h1>
                    <span
                        class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600 border border-red-200">
                        Arsip
                    </span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-slate-400 font-medium">NIS</p>
                        <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $siswa->nis }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Kelas Terakhir</p>
                        <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $siswa->kelas }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Orang Tua / Wali</p>
                        <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $siswa->nama_ortu }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">WhatsApp</p>
                        <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $siswa->no_whatsapp }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Laporan --}}
    <div class="mb-5 flex items-center gap-3">
        <div class="w-1 h-6 bg-blue-700 rounded-full"></div>
        <h2 class="text-lg font-bold text-slate-900">Riwayat Penanganan BK</h2>
        <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">{{ $siswa->laporan->count() }}
            laporan</span>
    </div>

    @forelse($siswa->laporan as $laporan)

        <div class="bg-white rounded-2xl border border-slate-200 mb-4 overflow-hidden">

            {{-- Header laporan --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-start justify-between gap-4">
                <div>
                    <h3 class="font-bold text-slate-800">{{ $laporan->judul_laporan }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $laporan->created_at->format('d M Y') }}</p>
                </div>
                <span class="flex-shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                    {{ ucfirst($laporan->status) }}
                </span>
            </div>

            <div class="p-6 space-y-6">

                {{-- Deskripsi --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Deskripsi Permasalahan</p>
                    <p class="text-sm text-slate-700 bg-slate-50 rounded-xl p-3 leading-relaxed">{{ $laporan->deskripsi }}</p>
                </div>

                {{-- Pemanggilan --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Riwayat Pemanggilan</p>
                    @forelse($laporan->pemanggilan as $item)
                        <div class="flex items-center gap-4 border border-slate-100 rounded-xl p-3 mb-2 bg-slate-50">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($item->tanggal_panggilan)->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500">Kehadiran: <span
                                        class="font-semibold">{{ ucfirst($item->status_kehadiran) }}</span></p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 bg-slate-50 rounded-xl p-3">Tidak ada data pemanggilan.</p>
                    @endforelse
                </div>

                {{-- Monitoring --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Riwayat Monitoring</p>
                    @forelse($laporan->monitoring as $item)
                        <div class="border border-slate-100 rounded-xl p-3 mb-2 bg-slate-50">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">
                                    Monitoring ke-{{ $item->monitoring_ke }}
                                </span>
                                <span class="text-xs text-slate-500">{{ ucfirst($item->status_perkembangan) }}</span>
                            </div>
                            <p class="text-sm text-slate-700">{{ $item->catatan_perkembangan }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 bg-slate-50 rounded-xl p-3">Tidak ada data monitoring.</p>
                    @endforelse
                </div>

                {{-- Evaluasi --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Evaluasi Akhir</p>
                    @if($laporan->evaluasi)
                        <div class="border border-green-200 rounded-xl p-4 bg-green-50">
                            <p class="text-xs font-semibold text-green-700 mb-1">Hasil Evaluasi</p>
                            <p class="text-sm text-slate-700 mb-3">{{ $laporan->evaluasi->hasil_evaluasi }}</p>
                        </div>
                    @else
                        <p class="text-sm text-slate-400 bg-slate-50 rounded-xl p-3">Belum ada evaluasi.</p>
                    @endif
                </div>

            </div>
        </div>

    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-14 text-center">
            <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-slate-500 font-medium">Tidak ada riwayat laporan pada siswa ini.</p>
        </div>
    @endforelse

@endsection