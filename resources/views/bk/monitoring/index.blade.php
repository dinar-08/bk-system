@extends('layouts.bk')

@section('title', 'Monitoring')
@section('page-title', 'Monitoring Permasalahan')
@section('page-subtitle', 'Pantau perkembangan penanganan setiap laporan')

@section('content')

    @php
        use Carbon\Carbon;

        $kategoriConfig = [
            'akademik' => ['label' => 'Akademik', 'text' => 'text-blue-600', 'border' => 'border-blue-500', 'bg' => 'bg-blue-50'],
            'sosial' => ['label' => 'Sosial', 'text' => 'text-emerald-600', 'border' => 'border-emerald-500', 'bg' => 'bg-emerald-50'],
            'perilaku' => ['label' => 'Perilaku', 'text' => 'text-amber-600', 'border' => 'border-amber-500', 'bg' => 'bg-amber-50'],
            'emosional' => ['label' => 'Emosional', 'text' => 'text-rose-600', 'border' => 'border-rose-500', 'bg' => 'bg-rose-50'],
            'lain-lain' => ['label' => 'Lainnya', 'text' => 'text-slate-600', 'border' => 'border-slate-400', 'bg' => 'bg-slate-50'],
        ];

        $kategoriAktif = request('kategori', 'semua');

        $laporanFiltered = $laporan
            ->filter(function ($item) use ($kategoriAktif) {
                return $kategoriAktif === 'semua' || $item->kategori === $kategoriAktif;
            })
            ->values();
    @endphp

    {{-- Filter Kategori --}}
    <form method="GET" action="{{ route('bk.monitoring.index') }}" class="mb-6 lg:mb-8">
        <div
            class="flex flex-nowrap lg:flex-wrap items-center gap-2 overflow-x-auto pb-1 -mx-4 px-4 lg:mx-0 lg:px-0 lg:overflow-visible scrollbar-hide">
            <button type="submit" name="kategori" value="semua" class="shrink-0 h-9 lg:h-11 px-4 lg:px-5 text-xs lg:text-sm font-semibold rounded-full transition whitespace-nowrap
                                {{ $kategoriAktif === 'semua'
        ? 'bg-blue-600 text-white shadow-sm'
        : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-400 hover:text-blue-600' }}">
                Semua
            </button>

            @foreach($kategoriConfig as $key => $cfg)
                <button type="submit" name="kategori" value="{{ $key }}" class="shrink-0 h-9 lg:h-11 px-4 lg:px-5 text-xs lg:text-sm font-semibold rounded-full transition whitespace-nowrap
                                                    {{ $kategoriAktif === $key
                ? 'bg-blue-600 text-white shadow-sm'
                : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-400 hover:text-blue-600' }}">
                    {{ $cfg['label'] }}
                </button>
            @endforeach
        </div>
    </form>

    {{-- Judul section --}}
    <div class="mb-3 lg:mb-4">
        <h2 class="text-sm lg:text-base font-bold text-slate-800">Daftar Monitoring</h2>
    </div>

    {{-- List Monitoring --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-0">
        @forelse($laporanFiltered as $item)
            @php
                $cfg = $kategoriConfig[$item->kategori] ?? $kategoriConfig['lain-lain'];

                $monitoringTerjadwal = $item->monitoring
                    ->where('status_monitoring', 'terjadwal')
                    ->sortBy('tanggal_monitoring')
                    ->first();

                $monitoringTerakhir = $item->monitoring
                    ->where('status_monitoring', 'selesai')
                    ->sortByDesc('monitoring_ke')
                    ->first();

                $jumlahMonitoring = $item->monitoring
                    ->where('status_monitoring', 'selesai')
                    ->count();

                $tanggalAcuan = $monitoringTerjadwal?->tanggal_monitoring
                    ?? $monitoringTerakhir?->tanggal_monitoring
                    ?? $item->updated_at
                    ?? $item->created_at;

                $tanggal = Carbon::parse($tanggalAcuan)->locale('id');

                $jamMonitoring = $monitoringTerjadwal?->waktu_monitoring
                    ? Carbon::parse($monitoringTerjadwal->waktu_monitoring)->format('H:i') . ' WIB'
                    : 'Jam belum diatur';

                $statusPerkembangan = $monitoringTerakhir->status_perkembangan ?? 'Belum ada monitoring';

                $statusClass = match ($statusPerkembangan) {
                    'membaik' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                    'stabil' => 'bg-blue-50 text-blue-700 border-blue-300',
                    'menurun' => 'bg-rose-50 text-rose-700 border-rose-300',
                    default => 'bg-slate-50 text-slate-600 border-slate-300',
                };
            @endphp

            <div
                class="relative group/list flex items-start lg:items-center justify-between gap-x-3 lg:gap-x-8 py-4 lg:py-5 border-b border-blue-300 overflow-hidden">

                {{-- Tanggal + Panah Hover --}}
                <div class="relative shrink-0">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 hidden lg:block">
                        <i data-feather="arrow-right"
                            class="w-5 h-5 text-blue-600 scale-0 group-hover/list:scale-100 transition-all duration-300"></i>
                    </div>

                    <div
                        class="flex items-center gap-1.5 lg:gap-2 min-w-[70px] lg:min-w-[130px] group-hover/list:lg:translate-x-8 transition-all duration-300">
                        <span class="text-2xl lg:text-4xl font-extrabold text-blue-600 leading-none w-[36px] lg:w-[48px]">
                            {{ $tanggal->format('d') }}
                        </span>

                        <div class="leading-tight">
                            <div class="text-[10px] lg:text-sm font-semibold uppercase text-slate-700">
                                {{ strtoupper($tanggal->translatedFormat('M')) }}
                            </div>
                            <div class="text-[10px] lg:text-sm text-slate-500">
                                {{ $tanggal->format('Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Isi --}}
                <div class="flex items-start lg:items-center justify-between gap-x-2 lg:gap-x-5 w-full min-w-0">
                    <div class="flex-1 min-w-0 group-hover/list:lg:translate-x-4 transition-all duration-300">

                        <div class="flex flex-wrap items-center gap-1.5 mb-1">
                            <h3 class="text-sm lg:text-base font-semibold text-slate-900 truncate max-w-[160px] sm:max-w-none">
                                {{ $item->siswa->nama_siswa ?? '-' }}
                            </h3>

                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full border {{ $cfg['border'] }} {{ $cfg['text'] }} bg-white text-[10px] lg:text-xs font-semibold">
                                {{ $cfg['label'] }}
                            </span>
                        </div>

                        <p class="text-xs lg:text-sm text-slate-500 truncate">
                            {{ $item->jenis_masalah ?? $item->judul_laporan ?? '-' }}
                        </p>

                        <div class="mt-1.5 flex flex-wrap items-center gap-1 lg:gap-1.5 text-[10px] lg:text-xs">
                            <span class="inline-flex items-center gap-1 text-cyan-800">
                                <span class="w-1.5 h-1.5 lg:w-2 lg:h-2 rounded-full bg-cyan-700"></span>
                                Kelas {{ $item->siswa->kelas ?? '-' }}
                            </span>

                            <span class="hidden sm:inline text-slate-300">•</span>

                            <span class="hidden sm:inline text-cyan-800 font-medium">
                                Monitoring Berikutnya
                            </span>

                            <span class="hidden sm:inline text-slate-300">•</span>

                            <span class="text-cyan-800">
                                {{ $jamMonitoring }}
                            </span>

                            <span
                                class="inline-flex items-center px-1.5 lg:px-2 py-0.5 rounded-full border font-semibold {{ $statusClass }} capitalize">
                                {{ $statusPerkembangan }}
                            </span>

                            <span
                                class="inline-flex items-center px-1.5 lg:px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-semibold whitespace-nowrap">
                                {{ $jumlahMonitoring }}x Monitoring
                            </span>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <a href="{{ route('bk.monitoring.show', $item->laporan_id) }}"
                        class="relative shrink-0 inline-flex items-center justify-center w-9 h-9 lg:w-[140px] lg:h-10 rounded-full border border-blue-500 text-blue-600 bg-white hover:bg-blue-50 transition-colors duration-500 overflow-hidden group/button uppercase mt-0.5 lg:mt-0">

                        <span
                            class="hidden lg:inline-block text-xs font-semibold transition-transform duration-500 group-hover/button:-translate-x-5 group-hover/list:-translate-x-5">
                            Lihat Detail
                        </span>

                        <i data-feather="arrow-right" class="lg:hidden w-4 h-4"></i>

                        <span
                            class="hidden lg:flex absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-blue-600 text-white items-center justify-center opacity-0 group-hover/button:opacity-100 group-hover/list:opacity-100 transition-opacity duration-500">
                            <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </span>
                    </a>
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 bg-white rounded-2xl border border-dashed border-slate-200 p-10 lg:p-14 text-center">
                <i data-feather="activity" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-400 text-sm font-medium">
                    Belum ada laporan dalam proses monitoring pada filter ini.
                </p>
            </div>
        @endforelse
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') feather.replace();
        });
    </script>

@endsection