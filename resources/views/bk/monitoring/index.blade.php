@extends('layouts.bk')
@section('title', 'Monitoring')
@section('page-title', 'Monitoring Permasalahan')
@section('page-subtitle', 'Pantau perkembangan penanganan setiap laporan')

@section('content')

    @php
        $laporanPerKategori = $laporan->groupBy('kategori');
        $kategoriConfig = [
            'akademik' => ['label' => 'Akademik', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'dot' => 'bg-blue-400'],
            'sosial' => ['label' => 'Sosial', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-400'],
            'perilaku' => ['label' => 'Perilaku', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700', 'dot' => 'bg-amber-400'],
            'emosional' => ['label' => 'Emosional', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-700', 'dot' => 'bg-rose-400'],
        ];
        $defaultCfg = ['label' => 'Lainnya', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Monitoring Permasalahan</h1>
        <p class="text-slate-500 mt-1 text-sm">Pantau perkembangan penanganan setiap laporan aktif.</p>
    </div>

    @forelse($laporanPerKategori as $kategori => $dataLaporan)
        @php $cfg = $kategoriConfig[$kategori] ?? array_merge($defaultCfg, ['label' => ucfirst($kategori ?? 'Lainnya')]); @endphp

        <section class="mb-9">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1 h-6 bg-blue-700 rounded-full"></div>
                <h2 class="text-base font-bold text-slate-800">{{ $cfg['label'] }}</h2>
                <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">{{ $dataLaporan->count() }}
                    laporan</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($dataLaporan as $item)
                    <div
                        class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $cfg['bg'] }} border {{ $cfg['border'] }} {{ $cfg['text'] }} text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }} animate-pulse"></span>
                                {{ $cfg['label'] }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono">#{{ $loop->iteration }}</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-sm leading-snug mb-3">{{ $item->siswa->nama_siswa ?? '-' }}</h3>
                        <div class="space-y-1.5 text-sm text-slate-500 mb-5">
                            <div class="flex items-center gap-2">
                                <i data-feather="book-open" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                                <span>Kelas {{ $item->siswa->kelas ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-feather="tag" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                                <span>{{ $item->jenis_masalah ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-feather="clock" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                                <span>{{ $item->monitoring->count() }}× monitoring</span>
                            </div>
                        </div>
                        <a href="{{ route('bk.monitoring.show', $item->id) }}"
                            class="flex items-center justify-center gap-1.5 w-full py-2 bg-blue-50 hover:bg-blue-700 text-blue-700 hover:text-white text-sm font-semibold rounded-xl border border-blue-100 hover:border-blue-700 transition-all duration-200">
                            Lihat Detail
                            <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-14 text-center">
            <i data-feather="activity" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-400 text-sm font-medium">Belum ada laporan dalam proses monitoring.</p>
        </div>
    @endforelse

@endsection