@extends('layouts.bk')

@section('title', 'Evaluasi')
@section('page-title', 'Evaluasi Permasalahan')
@section('page-subtitle', 'Lakukan evaluasi akhir berdasarkan hasil monitoring siswa')

@section('content')

    @php
        $kategoriLabel = [
            'akademik' => 'Akademik',
            'sosial' => 'Sosial',
            'perilaku' => 'Perilaku',
            'emosional' => 'Emosional',
            'lain-lain' => 'Lain-lain',
        ];

        $riwayatMonitoring = $laporan->monitoring
            ->where('status_monitoring', 'selesai')
            ->sortBy('monitoring_ke');

        $monitoringTerakhir = $riwayatMonitoring->last();

        $statusClass = match ($monitoringTerakhir->status_perkembangan ?? '') {
            'membaik' => 'bg-green-100 text-green-700',
            'menurun' => 'bg-red-100 text-red-700',
            default => 'bg-blue-100 text-blue-700',
        };
    @endphp

    <div class="mb-5 lg:mb-6 flex items-center gap-2 lg:gap-3">
        <a href="{{ route('bk.monitoring.show', $laporan->id) }}"
            class="flex items-center gap-1.5 text-xs lg:text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-xs lg:text-sm text-slate-800 font-semibold">Evaluasi</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- DATA SISWA + DETAIL LAPORAN --}}
        <section class="p-4 sm:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-7">

                {{-- DATA SISWA --}}
                <div>
                    <div class="flex items-center gap-2 mb-3 lg:mb-4 pb-3 border-b border-slate-100">
                        <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                        <h2 class="text-sm font-bold text-slate-700">Data Siswa</h2>
                    </div>

                    <div class="w-full h-32 sm:h-36 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden mb-4">
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

                    <div class="bg-slate-50 border border-slate-100 rounded-xl overflow-hidden">
                        @foreach([
                            ['Nama', optional($laporan->siswa)->nama_siswa],
                            ['Kelas', optional($laporan->siswa)->kelas],
                            ['NIS', optional($laporan->siswa)->nis],
                            ['Orang Tua', optional($laporan->siswa)->nama_ortu],
                        ] as [$label, $val])
                            <div class="px-3.5 sm:px-4 py-2.5 sm:py-3 @if(!$loop->last) border-b border-slate-200 @endif">
                                <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                                <p class="text-sm font-semibold text-slate-700 break-words">{{ $val ?: '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- DETAIL LAPORAN --}}
                <div>
                    <div class="flex items-center gap-2 mb-3 lg:mb-4 pb-3 border-b border-slate-100">
                        <i data-feather="file-text" class="w-4 h-4 text-blue-600"></i>
                        <h2 class="text-sm font-bold text-slate-700">Detail Laporan</h2>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-xl overflow-hidden">
                        @foreach([
                            ['Judul', $laporan->judul_laporan],
                            ['Kategori', $kategoriLabel[$laporan->kategori] ?? ($laporan->kategori ? ucfirst($laporan->kategori) : '-')],
                            ['Jenis Masalah', $laporan->jenis_masalah],
                            ['Guru BK', optional($laporan->guruBk)->nama ?? 'Belum ditentukan'],
                            ['Status', ucfirst($laporan->status ?? '-')],
                        ] as [$label, $val])
                            <div class="px-3.5 sm:px-4 py-2.5 sm:py-3 border-b border-slate-200">
                                <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                                <p class="text-sm font-semibold text-slate-700 break-words">{{ $val ?: '-' }}</p>
                            </div>
                        @endforeach

                        <div class="px-3.5 sm:px-4 py-2.5 sm:py-3 min-h-[100px] sm:min-h-[120px]">
                            <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                            <p class="text-sm text-slate-700 leading-relaxed break-words">
                                {{ $laporan->deskripsi ?: '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- RINGKASAN MONITORING --}}
        <section class="p-4 sm:p-6 border-t border-slate-200">
            <div class="flex items-center gap-2 mb-4 lg:mb-5 pb-3 lg:pb-4 border-b border-slate-100">
                <i data-feather="activity" class="w-4 h-4 text-blue-600"></i>
                <h2 class="text-sm font-bold text-slate-700">Ringkasan Monitoring</h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3">
                    <p class="text-xs text-slate-400 mb-1">Jumlah Monitoring</p>
                    <p class="text-sm font-bold text-slate-800">
                        {{ $riwayatMonitoring->count() }} kali
                    </p>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3">
                    <p class="text-xs text-slate-400 mb-1">Monitoring Terakhir</p>
                    <p class="text-sm font-bold text-slate-800">
                        @if($monitoringTerakhir)
                            {{ \Carbon\Carbon::parse($monitoringTerakhir->tanggal_monitoring)->translatedFormat('d F Y') }}
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3">
                    <p class="text-xs text-slate-400 mb-1">Status Terakhir</p>
                    @if($monitoringTerakhir)
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                            {{ ucfirst($monitoringTerakhir->status_perkembangan) }}
                        </span>
                    @else
                        <p class="text-sm font-bold text-slate-800">-</p>
                    @endif
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3">
                    <p class="text-xs text-slate-400 mb-1">Guru BK</p>
                    <p class="text-sm font-bold text-slate-800 break-words">
                    {{ optional($monitoringTerakhir?->guruBk)->nama ?? optional($laporan->guruBk)->nama ?? '-' }}
                    </p>
                </div>
            </div>
        </section>

        {{-- RIWAYAT MONITORING --}}
        <section class="p-4 sm:p-6 border-t border-slate-200">
            <div class="flex items-center gap-2 mb-4 lg:mb-5 pb-3 lg:pb-4 border-b border-slate-100">
                <i data-feather="list" class="w-4 h-4 text-blue-600"></i>
                <h2 class="text-sm font-bold text-slate-700">Riwayat Monitoring</h2>
            </div>

            <div class="space-y-0">
                @forelse($riwayatMonitoring as $item)
                    @php
                        $badgeClass = match ($item->status_perkembangan) {
                            'membaik' => 'bg-green-100 text-green-700',
                            'menurun' => 'bg-red-100 text-red-700',
                            default => 'bg-blue-100 text-blue-700',
                        };
                    @endphp

                    <div class="flex gap-2.5 sm:gap-4">
                        {{-- Kolom marker: titik + garis, terpisah dari konten --}}
                        <div class="flex flex-col items-center w-3 shrink-0">
                            <span class="w-3 h-3 rounded-full bg-blue-600 shrink-0"></span>
                            @if(!$loop->last)
                                <span class="w-px flex-1 bg-blue-100 mt-1"></span>
                            @endif
                        </div>

                        {{-- Kolom konten --}}
                        <div class="flex-1 min-w-0 pb-5 sm:pb-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">
                                        Monitoring ke-{{ $item->monitoring_ke }}
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ \Carbon\Carbon::parse($item->tanggal_monitoring)->translatedFormat('d F Y') }}
                                        @if(!empty($item->waktu_monitoring))
                                            • {{ \Carbon\Carbon::parse($item->waktu_monitoring)->format('H:i') }} WIB
                                        @endif
                                    </p>
                                </div>

                                <span class="w-fit px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ ucfirst($item->status_perkembangan) }}
                                </span>
                            </div>

                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3">
                                <p class="text-sm text-slate-600 leading-relaxed break-words">
                                    {{ $item->catatan_perkembangan ?: '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 sm:p-8 text-center">
                        <i data-feather="clock" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Belum ada riwayat monitoring.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- FORM EVALUASI --}}
        <section class="p-4 sm:p-6 border-t border-slate-200">
            <div class="flex items-center gap-2 mb-4 lg:mb-5 pb-3 lg:pb-4 border-b border-slate-100">
                <i data-feather="check-square" class="w-4 h-4 text-blue-600"></i>
                <h2 class="text-sm font-bold text-slate-700">Form Evaluasi</h2>
            </div>

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-red-700 mb-2">Ada data yang belum sesuai:</p>

                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bk.evaluasi.store') }}" method="POST">
                @csrf

                <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Tanggal Evaluasi
                        </label>
                        <input type="date" name="tanggal_evaluasi"
                            value="{{ old('tanggal_evaluasi', now()->format('Y-m-d')) }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Status Akhir
                        </label>
                        <select name="status_akhir"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">Pilih status akhir</option>
                            <option value="selesai" {{ old('status_akhir') == 'selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
                            <option value="dirujuk" {{ old('status_akhir') == 'dirujuk' ? 'selected' : '' }}>
                                Dirujuk
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Hasil Evaluasi
                        </label>
                        <textarea name="hasil_evaluasi" rows="6"
                            placeholder="Tuliskan ringkasan hasil evaluasi berdasarkan monitoring..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 placeholder-slate-400 resize-none focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('hasil_evaluasi') }}</textarea>
                    </div>

                    <div class="md:col-span-2 flex justify-stretch sm:justify-end">
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 sm:py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                            Simpan Evaluasi
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <script>
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>

@endsection