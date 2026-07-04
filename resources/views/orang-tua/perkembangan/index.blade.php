@extends('layouts.orang-tua')

@section('title', 'Perkembangan Anak')
@section('page-title', 'Perkembangan Anak')
@section('page-subtitle', 'Pantau perkembangan dan hasil monitoring dari Guru BK')

@section('content')

    <div>
        <div class="mb-5">
            <p class="text-sm text-slate-500">
                Data perkembangan hanya muncul jika laporan sudah masuk tahap monitoring.
            </p>
        </div>

        @forelse($laporan as $item)
            @php
                $monitoringTerbaru = $item->monitoring
                    ->sortByDesc('tanggal_monitoring')
                    ->first();

                $tanggalMonitoring = $monitoringTerbaru?->tanggal_monitoring;
                $statusPerkembangan = $monitoringTerbaru?->status_perkembangan;

                $statusLaporanColor = match ($item->status) {
                    'monitoring' => 'bg-purple-600',
                    'selesai' => 'bg-green-600',
                    'dirujuk' => 'bg-red-600',
                    default => 'bg-slate-500',
                };

                $statusLaporanLabel = match ($item->status) {
                    'monitoring' => 'Monitoring',
                    'selesai' => 'Selesai',
                    'dirujuk' => 'Dirujuk',
                    default => ucfirst($item->status),
                };

                $perkembanganColor = match ($statusPerkembangan) {
                    'membaik' => 'text-green-700',
                    'stabil' => 'text-blue-700',
                    'menurun' => 'text-red-700',
                    default => 'text-slate-400',
                };

                $perkembanganLabel = match ($statusPerkembangan) {
                    'membaik' => 'Perkembangan membaik',
                    'stabil' => 'Perkembangan stabil',
                    'menurun' => 'Perkembangan menurun',
                    default => 'Belum ada catatan monitoring',
                };

                $tanggalLaporan = $item->created_at->format('d M Y');

                $jadwalMonitoring = $tanggalMonitoring
                    ? \Carbon\Carbon::parse($tanggalMonitoring)->format('d M Y')
                    : null;
            @endphp

            <div class="border-t border-slate-200 py-6 first:border-t-0 first:pt-0">

                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <span
                        class="inline-flex items-center rounded-full {{ $statusLaporanColor }} text-white px-3 py-1 text-xs font-bold">
                        {{ $statusLaporanLabel }}
                    </span>
                    <span class="text-sm text-slate-400">
                        {{ $tanggalLaporan }}
                    </span>
                </div>

                <a href="{{ route('orang_tua.perkembangan.show', $item->id) }}" class="group block">
                    <h3 class="text-xl font-extrabold text-slate-900 leading-snug group-hover:text-blue-700 transition">
                        {{ $item->judul_laporan }}
                    </h3>
                </a>

                <p class="text-sm text-slate-500 mt-2">
                    {{ $item->siswa->nama_siswa }}
                    @if($item->jenis_masalah)
                        · {{ $item->jenis_masalah }}
                    @endif
                </p>

                <div class="flex items-center gap-1.5 text-sm text-slate-600 mt-2">
                    <i data-feather="calendar" class="w-4 h-4 text-slate-400"></i>
                    <span>
                        Jadwal monitoring:
                        <span class="font-semibold">
                            {{ $jadwalMonitoring ?? 'Belum dijadwalkan' }}
                        </span>
                    </span>
                </div>

                <p class="text-sm font-semibold {{ $perkembanganColor }} mt-2">
                    {{ $perkembanganLabel }}
                </p>

                <a href="{{ route('orang_tua.perkembangan.show', $item->id) }}"
                    class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-700 hover:text-blue-900 mt-3 transition">
                    Lihat Selengkapnya
                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-14 text-center">
                <i data-feather="trending-up" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                <p class="font-semibold text-slate-700">
                    Belum ada data perkembangan anak.
                </p>
                <p class="text-sm text-slate-400 mt-1">
                    Data akan muncul setelah laporan masuk tahap monitoring oleh Guru BK.
                </p>
            </div>
        @endforelse
    </div>

@endsection