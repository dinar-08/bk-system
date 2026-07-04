@extends('layouts.orang-tua')
@section('title', 'Detail Perkembangan')
@section('page-title', 'Detail Perkembangan Anak')
@section('page-subtitle', 'Informasi monitoring dan evaluasi dari Guru BK')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('orang_tua.perkembangan') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Detail Perkembangan</span>
    </div>

    @php
        $monitoringTerjadwal = $laporan->monitoring
            ->where('status_monitoring', 'terjadwal')
            ->sortBy('tanggal_monitoring')
            ->first();
    @endphp

    {{-- SATU CARD BESAR, SETIAP SECTION DIPISAH GARIS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100">

        {{-- DATA ANAK + DATA PERMASALAHAN --}}
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">

                {{-- DATA ANAK --}}
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                        <h2 class="text-sm font-bold text-slate-700">Data Anak</h2>
                    </div>

                    {{-- Foto --}}
                    <div
                        class="w-full h-32 rounded-xl overflow-hidden bg-blue-50 border border-slate-200 flex items-center justify-center mb-4">
                        @if($laporan->siswa->foto)
                            <img src="{{ asset('storage/' . $laporan->siswa->foto) }}" class="w-full h-full object-cover"
                                alt="Foto Siswa">
                        @else
                            <i data-feather="user" class="w-10 h-10 text-blue-200"></i>
                        @endif
                    </div>

                    <div class="space-y-2.5">
                        @foreach([['Nama', $laporan->siswa->nama_siswa], ['Kelas', $laporan->siswa->kelas], ['NIS', $laporan->siswa->nis], ['Orang Tua', $laporan->siswa->nama_ortu]] as [$label, $val])
                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                                <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $val ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- DATA PERMASALAHAN --}}
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <i data-feather="file-text" class="w-4 h-4 text-blue-600"></i>
                        <h2 class="text-sm font-bold text-slate-700">Data Permasalahan</h2>
                    </div>
                    <div class="space-y-2.5">
                        @foreach([['Judul', $laporan->judul_laporan], ['Kategori', ucfirst($laporan->kategori ?? '-')], ['Jenis Masalah', $laporan->jenis_masalah ?? '-'], ['Status', ucfirst($laporan->status)]] as [$label, $val])
                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                                <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $val }}</p>
                            </div>
                        @endforeach
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                            <p class="text-sm text-slate-700 leading-relaxed">{{ $laporan->deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- JADWAL MONITORING BERIKUTNYA --}}
            @if($monitoringTerjadwal)
                <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 mt-6 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i data-feather="calendar" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-blue-700 text-sm">Jadwal Monitoring Berikutnya</p>
                        <p class="text-blue-600 text-sm mt-0.5">
                            Monitoring ke-{{ $monitoringTerjadwal->monitoring_ke }} pada tanggal
                            <span class="font-bold">{{ $monitoringTerjadwal->tanggal_monitoring }}</span>
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- RIWAYAT PEMANGGILAN --}}
        <div class="p-6">
            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
                <i data-feather="phone" class="w-4 h-4 text-blue-600"></i>
                <h2 class="text-sm font-bold text-slate-700">Riwayat Pemanggilan</h2>
            </div>
            <div class="space-y-3">
                @forelse($laporan->pemanggilan as $item)
                    <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-3">
                            <div>
                                <p class="text-xs text-slate-400 mb-0.5">Tanggal</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $item->tanggal_pemanggilan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 mb-0.5">Waktu</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $item->waktu_pemanggilan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 mb-0.5">Pihak Dipanggil</p>
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $item->pihak_dipanggil ? str_replace('_', ' ', ucfirst($item->pihak_dipanggil)) : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 mb-0.5">Status Kehadiran</p>
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $item->status_kehadiran ? str_replace('_', ' ', ucfirst($item->status_kehadiran)) : '-' }}
                                </p>
                            </div>
                        </div>

                        @if($item->tujuan)
                            <div class="pt-3 border-t border-slate-200">
                                <p class="text-xs text-slate-400 mb-0.5">Tujuan</p>
                                <p class="text-sm text-slate-700 leading-relaxed">{{ $item->tujuan }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-8 text-center">
                        <i data-feather="calendar" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Belum ada jadwal pemanggilan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIWAYAT MONITORING --}}
        <div class="p-6">
            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
                <i data-feather="activity" class="w-4 h-4 text-blue-600"></i>
                <h2 class="text-sm font-bold text-slate-700">Riwayat Monitoring</h2>
            </div>
            <div class="space-y-3">
                @forelse($laporan->monitoring->where('status_monitoring', 'selesai')->sortBy('monitoring_ke') as $item)
                    <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                                    Monitoring ke-{{ $item->monitoring_ke }}
                                </span>
                                <p class="text-xs text-slate-400 mt-1.5">{{ $item->tanggal_monitoring }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($item->status_perkembangan == 'membaik') bg-green-100 text-green-700
                                            @elseif($item->status_perkembangan == 'stabil') bg-blue-100 text-blue-700
                                            @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($item->status_perkembangan) }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $item->catatan_perkembangan }}</p>
                    </div>
                @empty
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-8 text-center">
                        <i data-feather="clock" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Belum ada hasil monitoring.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- EVALUASI --}}
        @if($laporan->evaluasi)
            <div class="p-6">
                <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
                    <i data-feather="check-square" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">Evaluasi Guru BK</h2>
                </div>
                <div class="space-y-3">
                    <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                        <p class="text-xs text-slate-400 mb-1">Hasil Evaluasi</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $laporan->evaluasi->hasil_evaluasi }}</p>
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection