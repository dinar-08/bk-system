@extends('layouts.orang-tua')

@section('title', 'Perkembangan Anak')
@section('page-title', 'Perkembangan Anak')
@section('page-subtitle', 'Pantau perkembangan dan hasil monitoring dari Guru BK')

@section('content')

    <div class="space-y-6">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-900">Perkembangan Anak</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Data perkembangan hanya muncul jika laporan sudah masuk tahap monitoring.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($laporan as $item)

                    @php
                        $initial = strtoupper(substr($item->siswa->nama_siswa, 0, 1));

                        $monitoringTerbaru = $item->monitoring
                            ->sortByDesc('tanggal_monitoring')
                            ->first();

                        $tanggalMonitoring = $monitoringTerbaru?->tanggal_monitoring;
                        $statusPerkembangan = $monitoringTerbaru?->status_perkembangan;

                        $statusLaporanColor = match ($item->status) {
                            'monitoring' => 'bg-purple-50 text-purple-700 border-purple-100',
                            'selesai' => 'bg-green-50 text-green-700 border-green-100',
                            'dirujuk' => 'bg-red-50 text-red-700 border-red-100',
                            default => 'bg-slate-50 text-slate-600 border-slate-100',
                        };

                        $statusLaporanLabel = match ($item->status) {
                            'monitoring' => 'Monitoring',
                            'selesai' => 'Selesai',
                            'dirujuk' => 'Dirujuk',
                            default => ucfirst($item->status),
                        };

                        $perkembanganColor = match ($statusPerkembangan) {
                            'membaik' => 'bg-green-100 text-green-700',
                            'stabil' => 'bg-blue-100 text-blue-700',
                            'menurun' => 'bg-red-100 text-red-700',
                            default => 'bg-slate-100 text-slate-600',
                        };

                        $perkembanganLabel = match ($statusPerkembangan) {
                            'membaik' => 'Membaik',
                            'stabil' => 'Stabil',
                            'menurun' => 'Menurun',
                            default => 'Belum ada monitoring',
                        };
                    @endphp

                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">

                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-3 mb-4">

                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0">
                                    @if($item->siswa->foto)
                                        <img src="{{ asset('storage/' . $item->siswa->foto) }}"
                                            class="w-full h-full object-cover"
                                            alt="Foto">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-700 font-bold">
                                            {{ $initial }}
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <p class="font-bold text-slate-900">
                                        {{ $item->siswa->nama_siswa }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        NIS {{ $item->siswa->nis }} · {{ $item->siswa->kelas }}
                                    </p>
                                </div>
                            </div>

                            <span
                                class="text-xs font-semibold px-3 py-1 rounded-full border {{ $statusLaporanColor }}">
                                {{ $statusLaporanLabel }}
                            </span>

                        </div>

                        {{-- Judul --}}
                        <h3 class="font-bold text-slate-800 mb-4 line-clamp-2">
                            {{ $item->judul_laporan }}
                        </h3>

                        {{-- Informasi --}}
                        <div class="space-y-2 text-sm text-slate-500 mb-5">

                            <div class="flex items-center gap-2">
                                <i data-feather="tag" class="w-4 h-4 text-slate-400"></i>
                                <span>{{ $item->jenis_masalah ?? 'Belum ditentukan BK' }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <i data-feather="calendar" class="w-4 h-4 text-slate-400"></i>
                                <span>
                                    {{ $tanggalMonitoring
                                        ? \Carbon\Carbon::parse($tanggalMonitoring)->format('d M Y')
                                        : 'Belum ada tanggal monitoring' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <i data-feather="trending-up" class="w-4 h-4 text-slate-400"></i>

                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $perkembanganColor }}">
                                    {{ $perkembanganLabel }}
                                </span>
                            </div>

                        </div>

                        {{-- Tombol --}}
                        <a href="{{ route('orang_tua.perkembangan.show', $item->id) }}"
                            class="flex items-center justify-center gap-2 w-full py-2.5
                            bg-blue-50 hover:bg-blue-700
                            text-blue-700 hover:text-white
                            border border-blue-100 hover:border-blue-700
                            rounded-xl font-semibold text-sm
                            transition-all duration-200">
                            Lihat Detail
                            <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </a>

                    </div>

                @empty

                    <div
                        class="col-span-full bg-white rounded-2xl border border-dashed border-slate-200 p-14 text-center">

                        <i data-feather="trending-up"
                            class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>

                        <p class="font-semibold text-slate-700">
                            Belum ada data perkembangan anak.
                        </p>

                        <p class="text-sm text-slate-400 mt-1">
                            Data akan muncul setelah laporan masuk tahap monitoring oleh Guru BK.
                        </p>

                    </div>

                @endforelse
            </div>
        </div>

    </div>

@endsection