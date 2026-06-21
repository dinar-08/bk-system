@extends('layouts.bk')
@section('title', 'Detail Monitoring')
@section('page-title', 'Detail Monitoring')
@section('page-subtitle', 'Catat perkembangan dan hasil monitoring siswa')

@section('content')

    @php
        $monitoringTerjadwal = $laporan->monitoring
            ->where('status_monitoring', 'terjadwal')
            ->sortBy('tanggal_monitoring')
            ->first();
        $riwayatMonitoring = $laporan->monitoring
            ->where('status_monitoring', 'selesai')
            ->sortBy('monitoring_ke');
    @endphp

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('bk.monitoring.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Detail Monitoring</span>
    </div>

    {{-- CARD UTAMA: Data Siswa + Detail Laporan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">

            {{-- DATA SISWA --}}
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">Data Siswa</h2>
                </div>
                <div
                    class="w-full h-36 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden mb-4">
                    @if(optional($laporan->siswa)->foto)
                        <img src="{{ asset('storage/' . $laporan->siswa->foto) }}" class="w-full h-full object-cover"
                            alt="Foto Siswa">
                    @else
                        <div class="text-center">
                            <i data-feather="user" class="w-8 h-8 text-slate-300 mx-auto mb-1"></i>
                            <p class="text-xs text-slate-400">Foto Siswa</p>
                        </div>
                    @endif
                </div>
                <div class="space-y-2.5">
                    @foreach([['Nama', optional($laporan->siswa)->nama_siswa], ['Kelas', optional($laporan->siswa)->kelas], ['NIS', optional($laporan->siswa)->nis], ['Orang Tua', optional($laporan->siswa)->nama_ortu]] as [$label, $val])
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $val ?? '-' }}</p>
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
                    @foreach([['Judul', $laporan->judul_laporan], ['Kategori', ucfirst($laporan->kategori ?? '-')], ['Jenis Masalah', $laporan->jenis_masalah]] as [$label, $val])
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $val ?? '-' }}</p>
                        </div>
                    @endforeach
                    <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 min-h-[120px]">
                        <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $laporan->deskripsi ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM MONITORING --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
        <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
            <i data-feather="edit-3" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-sm font-bold text-slate-700">
                @if($monitoringTerjadwal)
                    Monitoring ke-{{ $monitoringTerjadwal->monitoring_ke }}
                @else
                    Monitoring
                @endif
            </h2>
        </div>

        @if($monitoringTerjadwal)
            <form action="{{ route('bk.monitoring.store') }}" method="POST">
                @csrf
                <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                <input type="hidden" name="monitoring_id" value="{{ $monitoringTerjadwal->id }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Monitoring</label>
                        <input type="date" name="tanggal_monitoring" value="{{ $monitoringTerjadwal->tanggal_monitoring }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Perkembangan</label>
                        <select name="status_perkembangan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="membaik">Membaik</option>
                            <option value="stabil">Stabil</option>
                            <option value="menurun">Menurun</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Catatan Perkembangan</label>
                        <textarea name="catatan_perkembangan" rows="4"
                            placeholder="Tuliskan hasil monitoring yang sudah dilakukan..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 placeholder-slate-400 resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jadwal Monitoring Berikutnya</label>
                        <input type="date" name="tanggal_monitoring_berikutnya"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div class="flex items-end justify-end">
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                            Simpan Hasil Monitoring
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div
                class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="font-semibold text-blue-700 text-sm">Tidak ada jadwal monitoring aktif.</p>
                    <p class="text-blue-600 text-xs mt-0.5">
                        Jika monitoring dirasa sudah cukup, silakan lanjut ke tahap evaluasi.
                    </p>
                </div>

                <a href="{{ route('bk.evaluasi.show', $laporan->id) }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                    <i data-feather="check-square" class="w-4 h-4"></i>
                    Lanjut Evaluasi
                </a>
            </div>
        @endif
    </div>

    {{-- RIWAYAT MONITORING --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
            <i data-feather="list" class="w-4 h-4 text-blue-600"></i>
            <h2 class="text-sm font-bold text-slate-700">Riwayat Monitoring</h2>
        </div>
        <div class="space-y-3">
            @forelse($riwayatMonitoring as $item)
                <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                                Monitoring ke-{{ $item->monitoring_ke }}
                            </span>
                            <p class="text-xs text-slate-400 mt-1.5">
                                {{ \Carbon\Carbon::parse($item->tanggal_monitoring)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($item->status_perkembangan == 'membaik') bg-green-100 text-green-700
                                        @elseif($item->status_perkembangan == 'menurun') bg-red-100 text-red-700
                                        @else bg-blue-100 text-blue-700 @endif">
                            {{ ucfirst($item->status_perkembangan) }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $item->catatan_perkembangan }}</p>
                </div>
            @empty
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-8 text-center">
                    <i data-feather="clock" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-400">Belum ada riwayat monitoring selesai.</p>
                </div>
            @endforelse
        </div>
    </div>

@endsection