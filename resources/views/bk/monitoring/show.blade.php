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

        $kategoriLabel = [
            'akademik' => 'Akademik',
            'sosial' => 'Sosial',
            'perilaku' => 'Perilaku',
            'emosional' => 'Emosional',
            'lain-lain' => 'Lain-lain',
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('bk.monitoring.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300 hidden sm:inline">/</span>
        <span class="text-sm text-slate-800 font-semibold hidden sm:inline">Detail Monitoring</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- DATA SISWA + DETAIL LAPORAN --}}
        <section class="p-4 sm:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-7">

                {{-- DATA SISWA --}}
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                        <h2 class="text-sm font-bold text-slate-700">Data Siswa</h2>
                    </div>
                    <div
                        class="w-full h-28 sm:h-36 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden mb-4">
                        @if(optional($laporan->siswa)->foto)
                            <img src="{{ route('foto.siswa', $laporan->siswa->nis) }}"
                                class="w-full h-full object-cover" alt="Foto Siswa">
                        @else
                            <div class="text-center">
                                <i data-feather="user" class="w-8 h-8 text-slate-300 mx-auto mb-1"></i>
                                <p class="text-xs text-slate-400">Foto Siswa</p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-xl overflow-hidden">
                        @foreach([
                            ['Nama', optional($laporan->siswa)->nama_siswa],
                            ['Kelas', optional($laporan->siswa)->kelas],
                            ['NIS', optional($laporan->siswa)->nis],
                            ['Orang Tua', optional($laporan->siswa)->nama_ortu],
                        ] as [$label, $val])
                            <div class="px-3 sm:px-4 py-2.5 sm:py-3 @if(!$loop->last) border-b border-slate-200 @endif">
                                <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                                <p class="text-sm font-semibold text-slate-700 break-words">{{ $val ?: '-' }}</p>
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

                    <div class="bg-slate-50 border border-slate-100 rounded-xl overflow-hidden">
                        @foreach([
                            ['Judul', $laporan->judul_laporan],
                            ['Kategori', $kategoriLabel[$laporan->kategori] ?? ($laporan->kategori ? ucfirst($laporan->kategori) : '-')],
                            ['Jenis Masalah', $laporan->jenis_masalah ?: '-'],
                        ] as [$label, $val])
                            <div class="px-3 sm:px-4 py-2.5 sm:py-3 border-b border-slate-200">
                                <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                                <p class="text-sm font-semibold text-slate-700 break-words">{{ $val ?: '-' }}</p>
                            </div>
                        @endforeach

                        <div class="px-3 sm:px-4 py-2.5 sm:py-3 min-h-[100px] sm:min-h-[120px]">
                            <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                            <p class="text-sm text-slate-700 leading-relaxed break-words">
                                {{ $laporan->deskripsi ?: '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- FORM MONITORING --}}
        <section class="p-4 sm:p-6 border-t border-slate-200">
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

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-red-700 mb-2">
                        Ada data yang belum sesuai:
                    </p>

                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($monitoringTerjadwal)
                <form action="{{ route('bk.monitoring.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="laporan_id" value="{{ $laporan->laporan_id }}">
                    <input type="hidden" name="monitoring_id" value="{{ $monitoringTerjadwal->monitoring_id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Monitoring</label>
                            <input type="date" name="tanggal_monitoring"
                                value="{{ old('tanggal_monitoring', $monitoringTerjadwal->tanggal_monitoring) }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Monitoring</label>
                            <input type="time" name="waktu_monitoring" step="60"
                                value="{{ old('waktu_monitoring', $monitoringTerjadwal->waktu_monitoring ? \Carbon\Carbon::parse($monitoringTerjadwal->waktu_monitoring)->format('H:i') : '') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Perkembangan</label>
                            <select name="status_perkembangan"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="membaik" {{ old('status_perkembangan') == 'membaik' ? 'selected' : '' }}>Membaik</option>
                                <option value="stabil" {{ old('status_perkembangan', 'stabil') == 'stabil' ? 'selected' : '' }}>Stabil</option>
                                <option value="menurun" {{ old('status_perkembangan') == 'menurun' ? 'selected' : '' }}>Menurun</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jadwal Monitoring Berikutnya</label>
                            <input type="date" name="tanggal_monitoring_berikutnya"
                                value="{{ old('tanggal_monitoring_berikutnya') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Monitoring Berikutnya</label>
                            <input type="time" name="waktu_monitoring_berikutnya" step="60"
                                value="{{ old('waktu_monitoring_berikutnya') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Catatan Perkembangan</label>
                            <textarea name="catatan_perkembangan" rows="4"
                                placeholder="Tuliskan hasil monitoring yang sudah dilakukan..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 placeholder-slate-400 resize-none focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('catatan_perkembangan') }}</textarea>
                        </div>

                        <div class="md:col-span-2 flex justify-stretch sm:justify-end">
                            <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                                Simpan Hasil Monitoring
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div
                    class="bg-blue-50 border border-blue-200 rounded-xl px-4 sm:px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="font-semibold text-blue-700 text-sm">Tidak ada jadwal monitoring aktif.</p>
                        <p class="text-blue-600 text-xs mt-0.5">
                            Jika monitoring dirasa sudah cukup, silakan lanjut ke tahap evaluasi.
                        </p>
                    </div>

                    <a href="{{ route('bk.evaluasi.show', $laporan->laporan_id) }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors w-full md:w-auto">
                        <i data-feather="check-square" class="w-4 h-4"></i>
                        Lanjut Evaluasi
                    </a>
                </div>
            @endif
        </section>

        {{-- RIWAYAT MONITORING --}}
        <section class="p-4 sm:p-6 border-t border-slate-200">
            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
                <i data-feather="list" class="w-4 h-4 text-blue-600"></i>
                <h2 class="text-sm font-bold text-slate-700">Riwayat Monitoring</h2>
            </div>

            <div class="space-y-3">
                @forelse($riwayatMonitoring as $item)
                    <div class="border border-slate-200 bg-slate-50 rounded-xl p-3 sm:p-4">
                        <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                            <div>
                                <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                                    Monitoring ke-{{ $item->monitoring_ke }}
                                </span>
                                <p class="text-xs text-slate-400 mt-1.5">
                                    {{ \Carbon\Carbon::parse($item->tanggal_monitoring)->translatedFormat('d F Y') }}
                                    @if(!empty($item->waktu_monitoring))
                                        • {{ \Carbon\Carbon::parse($item->waktu_monitoring)->format('H:i') }} WIB
                                    @endif
                                </p>
                            </div>

                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($item->status_perkembangan == 'membaik') bg-green-100 text-green-700
                                @elseif($item->status_perkembangan == 'menurun') bg-red-100 text-red-700
                                @else bg-blue-100 text-blue-700 @endif">
                                {{ ucfirst($item->status_perkembangan) }}
                            </span>
                        </div>

                        <p class="text-sm text-slate-600 leading-relaxed break-words">
                            {{ $item->catatan_perkembangan ?: '-' }}
                        </p>
                    </div>
                @empty
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 sm:p-8 text-center">
                        <i data-feather="clock" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Belum ada riwayat monitoring selesai.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

@endsection