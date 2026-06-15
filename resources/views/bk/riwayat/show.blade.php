@extends('layouts.bk')

@section('title', 'Detail Riwayat')

@section('content')

    @php
        $statusAkhir = optional($laporan->evaluasi)->status_akhir ?? $laporan->status;

        $statusClass = $statusAkhir == 'selesai'
            ? 'bg-green-100 text-green-700 border-green-200'
            : 'bg-red-100 text-red-700 border-red-200';
    @endphp

    <div class="max-w-6xl mx-auto">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-5 px-6 py-4 flex items-center gap-5">
            <a href="{{ route('bk.riwayat.index') }}" class="text-slate-400 hover:text-slate-600">
                ←
            </a>

            <div class="h-7 w-px bg-slate-200"></div>

            <h1 class="text-base font-bold text-slate-800">
                Detail Riwayat Kasus
            </h1>

            <span class="ml-auto px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                {{ ucfirst(str_replace('_', ' ', $statusAkhir)) }}
            </span>
        </div>

        {{-- CARD UTAMA --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">

                {{-- DATA SISWA --}}
                <div>
                    <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                        <h2 class="text-sm font-bold text-slate-700">Data Siswa</h2>
                    </div>

                    <div class="flex justify-center mb-6">
                        <div
                            class="w-44 h-36 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                            @if(optional($laporan->siswa)->foto)
                                <img src="{{ asset('storage/' . $laporan->siswa->foto) }}" class="w-full h-full object-cover"
                                    alt="Foto Siswa">
                            @else
                                <span class="text-xs font-bold text-slate-400">FOTO SISWA</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Nama</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->nama_siswa ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Kelas</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->kelas ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">NIS</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->nis ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Orang Tua</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->nama_ortu ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">No WhatsApp</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->no_whatsapp ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- DATA LAPORAN --}}
                <div>
                    <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                        <h2 class="text-sm font-bold text-slate-700">Data Laporan</h2>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Judul Laporan</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ $laporan->judul_laporan ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Kategori</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ ucfirst($laporan->kategori ?? '-') }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Jenis Masalah</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ $laporan->jenis_masalah ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Status Akhir</p>
                            <span
                                class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $statusAkhir)) }}
                            </span>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4 min-h-48">
                            <p class="text-sm text-slate-400 mb-2">Deskripsi</p>
                            <p class="text-base text-slate-800 leading-7">
                                {{ $laporan->deskripsi ?? '-' }}
                            </p>

                            @if($laporan->bukti)
                                <a href="{{ asset('storage/' . $laporan->bukti) }}" target="_blank"
                                    class="inline-flex mt-4 px-4 py-2 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800">
                                    Lihat Bukti
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIWAYAT PEMANGGILAN --}}
            <div class="mt-8">
                <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                    <h2 class="text-sm font-bold text-slate-700">Riwayat Pemanggilan</h2>
                </div>

                <div class="space-y-4">
                    @forelse($laporan->pemanggilan as $item)
                        <div class="border border-slate-200 bg-slate-50 rounded-xl p-5">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm mb-4">
                                <div>
                                    <p class="text-slate-400">Tanggal</p>
                                    <p class="font-semibold text-slate-800">{{ $item->tanggal_pemanggilan }}</p>
                                </div>

                                <div>
                                    <p class="text-slate-400">Waktu</p>
                                    <p class="font-semibold text-slate-800">{{ $item->waktu_pemanggilan }}</p>
                                </div>

                                <div>
                                    <p class="text-slate-400">Pihak</p>
                                    <p class="font-semibold text-slate-800">
                                        {{ str_replace('_', ' ', ucfirst($item->pihak_dipanggil)) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-slate-400">Kehadiran</p>
                                    <p class="font-semibold text-slate-800">
                                        {{ str_replace('_', ' ', ucfirst($item->status_kehadiran)) }}
                                    </p>
                                </div>
                            </div>

                            @if($item->tujuan)
                                <p class="text-sm text-slate-600 leading-7">
                                    {{ $item->tujuan }}
                                </p>
                            @endif

                            @if($item->catatan)
                                <p class="text-sm text-slate-600 leading-7 mt-2">
                                    <span class="font-semibold">Catatan:</span> {{ $item->catatan }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 text-center">
                            <p class="text-sm text-slate-400">
                                Belum ada riwayat pemanggilan.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- RIWAYAT MONITORING --}}
            <div class="mt-8">
                <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                    <h2 class="text-sm font-bold text-slate-700">Riwayat Monitoring</h2>
                </div>

                <div class="space-y-4">
                    @forelse($laporan->monitoring->where('status_monitoring', 'selesai')->sortBy('monitoring_ke') as $item)
                        <div class="border border-slate-200 bg-slate-50 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">
                                        Monitoring ke-{{ $item->monitoring_ke }}
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $item->tanggal_monitoring }}
                                    </p>
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($item->status_perkembangan == 'membaik') bg-green-100 text-green-700
                                        @elseif($item->status_perkembangan == 'menurun') bg-red-100 text-red-700
                                        @else bg-blue-100 text-blue-700
                                        @endif">
                                    {{ ucfirst($item->status_perkembangan) }}
                                </span>
                            </div>

                            <p class="text-sm text-slate-600 leading-7">
                                {{ $item->catatan_perkembangan }}
                            </p>
                        </div>
                    @empty
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 text-center">
                            <p class="text-sm text-slate-400">
                                Belum ada riwayat monitoring.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- EVALUASI AKHIR --}}
            <div class="mt-8">
                <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                    <h2 class="text-sm font-bold text-slate-700">Evaluasi Akhir</h2>
                </div>

                @if($laporan->evaluasi)
                    <div class="space-y-3">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Tanggal Evaluasi</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ $laporan->evaluasi->tanggal_evaluasi }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4 min-h-40">
                            <p class="text-sm text-slate-400 mb-2">Hasil Evaluasi</p>
                            <p class="text-base text-slate-800 leading-7">
                                {{ $laporan->evaluasi->hasil_evaluasi }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Status Akhir</p>
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $laporan->evaluasi->status_akhir)) }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 text-center">
                        <p class="text-sm text-slate-400">
                            Belum ada data evaluasi.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>

@endsection