@extends('layouts.orang-tua')
@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan')
@section('page-subtitle', 'Informasi laporan, pemanggilan, monitoring, dan evaluasi anak')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('orang_tua.laporan.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Detail Laporan</span>
    </div>

    {{-- DATA ANAK + LAPORAN --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">

            {{-- DATA ANAK --}}
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">Data Anak</h2>
                </div>
                <div class="space-y-2.5">
                    @foreach([['Nama', $laporan->siswa->nama_siswa], ['NIS', $laporan->siswa->nis], ['Kelas', $laporan->siswa->kelas], ['Orang Tua', $laporan->siswa->nama_ortu]] as [$label, $val])
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $val ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- DATA LAPORAN --}}
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <i data-feather="file-text" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">Data Laporan</h2>
                </div>
                <div class="space-y-2.5">
                    <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                        <p class="text-xs text-slate-400 mb-0.5">Judul</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $laporan->judul_laporan }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">Kategori</p>
                            <p class="text-sm font-semibold text-slate-700">
                                {{ $laporan->kategori ? ucfirst($laporan->kategori) : 'Menunggu verifikasi BK' }}</p>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">Jenis Masalah</p>
                            <p class="text-sm font-semibold text-slate-700">
                                {{ $laporan->jenis_masalah ?? 'Menunggu verifikasi BK' }}</p>
                        </div>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                        <p class="text-xs text-slate-400 mb-0.5">Status</p>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            {{ ucfirst($laporan->status) }}
                        </span>
                    </div>
                    @if($laporan->bukti)
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-1">Lampiran</p>
                            <a href="{{ asset('storage/' . $laporan->bukti) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-700 text-white rounded-lg text-xs font-semibold hover:bg-blue-800 transition-colors">
                                <i data-feather="paperclip" class="w-3 h-3"></i>
                                Lihat Bukti
                            </a>
                        </div>
                    @endif
                    <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                        <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $laporan->deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIWAYAT PEMANGGILAN --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
        <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
            <i data-feather="phone" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-sm font-bold text-slate-700">Riwayat Pemanggilan</h2>
        </div>
        <div class="space-y-3">
            @forelse($laporan->pemanggilan as $item)
                <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach([['Tanggal', $item->tanggal_pemanggilan], ['Waktu', $item->waktu_pemanggilan], ['Pihak Dipanggil', str_replace('_', ' ', ucfirst($item->pihak_dipanggil))], ['Status Kehadiran', str_replace('_', ' ', ucfirst($item->status_kehadiran))]] as [$lbl, $val])
                            <div>
                                <p class="text-xs text-slate-400 mb-0.5">{{ $lbl }}</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $val }}</p>
                            </div>
                        @endforeach
                    </div>
                    @if($item->tujuan)
                        <p class="text-sm text-slate-600 mt-3 pt-3 border-t border-slate-200">{{ $item->tujuan }}</p>
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
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
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
                    <p class="text-sm text-slate-400">Belum ada data monitoring.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- EVALUASI AKHIR --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
            <i data-feather="check-square" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-sm font-bold text-slate-700">Evaluasi Akhir</h2>
        </div>
        @if($laporan->evaluasi)
            <div class="space-y-3">
                <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                    <p class="text-xs text-slate-400 mb-0.5">Tanggal Evaluasi</p>
                    <p class="text-sm font-semibold text-slate-700">{{ $laporan->evaluasi->tanggal_evaluasi }}</p>
                </div>
                <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                    <p class="text-xs text-slate-400 mb-1">Hasil Evaluasi</p>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $laporan->evaluasi->hasil_evaluasi }}</p>
                </div>
            </div>
        @else
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-8 text-center">
                <i data-feather="clipboard" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm text-slate-400">Belum ada evaluasi akhir dari Guru BK.</p>
            </div>
        @endif
    </div>

@endsection