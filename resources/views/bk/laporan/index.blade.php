@extends('layouts.bk')

@section('title', 'Data Laporan')

@section('page-title', 'Laporan Permasalahan')
@section('page-subtitle', 'Kelola semua laporan masuk dari orang tua')

@section('content')

    @php
        $statusConfig = [
            'baru' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'dot' => 'bg-yellow-400', 'text' => 'text-yellow-700', 'label' => 'Baru', 'pulse' => true],
            'proses' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'dot' => 'bg-blue-400', 'text' => 'text-blue-700', 'label' => 'Proses', 'pulse' => false],
            'selesai' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'dot' => 'bg-green-400', 'text' => 'text-green-700', 'label' => 'Selesai', 'pulse' => false],
            'ditolak' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'dot' => 'bg-red-400', 'text' => 'text-red-700', 'label' => 'Ditolak', 'pulse' => false],
        ];
    @endphp

    {{-- Header: stack di mobile, sejajar di desktop --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Laporan Permasalahan</h1>
            <p class="text-slate-500 mt-1 text-xs sm:text-sm">Kelola semua laporan masuk dari orang tua.</p>
        </div>
        <a href="{{ route('bk.laporan.create') }}"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 active:bg-blue-900 transition-colors w-full sm:w-auto">
            <i data-feather="plus" class="w-4 h-4"></i>
            Buat Laporan
        </a>
    </div>

    {{-- Laporan Baru --}}
    <section class="mb-8">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse flex-shrink-0"></span>
            <h2 class="text-xs sm:text-sm font-bold text-slate-600 uppercase tracking-widest">Laporan Baru</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
            @forelse($laporan->where('status', 'baru') as $item)
                @php $cfg = $statusConfig['baru']; @endphp
                <div
                    class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md active:shadow-md sm:hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3 sm:mb-4 gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $cfg['bg'] }} border {{ $cfg['border'] }} {{ $cfg['text'] }} text-xs font-semibold rounded-full whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }} animate-pulse flex-shrink-0"></span>
                            {{ $cfg['label'] }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono flex-shrink-0">#{{ $item->id }}</span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm leading-snug mb-3 line-clamp-2">{{ $item->judul_laporan }}</h3>
                    <div class="space-y-1.5 text-xs sm:text-sm text-slate-500 mb-4 sm:mb-5">
                        <div class="flex items-center gap-2">
                            <i data-feather="user" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                            <span class="truncate">{{ $item->siswa->nama_siswa ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-feather="tag" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                            <span class="truncate">{{ ucfirst($item->kategori ?? '-') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('bk.laporan.show', $item->id) }}"
                        class="flex items-center justify-center gap-1.5 w-full py-2.5 sm:py-2 bg-blue-50 hover:bg-blue-700 active:bg-blue-800 text-blue-700 hover:text-white text-sm font-semibold rounded-xl border border-blue-100 hover:border-blue-700 transition-all duration-200">
                        Lihat Detail
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-200 p-8 sm:p-10 text-center">
                    <i data-feather="inbox" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-slate-400 text-sm">Tidak ada laporan baru.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Laporan Proses & Lainnya --}}
    <section>
        <div class="flex items-center gap-2 mb-4">
            <span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>
            <h2 class="text-xs sm:text-sm font-bold text-slate-600 uppercase tracking-widest">Dalam Pemanggilan</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
            @forelse($laporan->where('status', '!=', 'baru') as $item)
                @php $cfg = $statusConfig[$item->status] ?? ['bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'dot' => 'bg-slate-400', 'text' => 'text-slate-600', 'label' => ucfirst($item->status)]; @endphp
                <div
                    class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md active:shadow-md sm:hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3 sm:mb-4 gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $cfg['bg'] }} border {{ $cfg['border'] }} {{ $cfg['text'] }} text-xs font-semibold rounded-full whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }} flex-shrink-0"></span>
                            {{ $cfg['label'] }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono flex-shrink-0">#{{ $item->id }}</span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm leading-snug mb-3 line-clamp-2">{{ $item->judul_laporan }}</h3>
                    <div class="space-y-1.5 text-xs sm:text-sm text-slate-500 mb-4 sm:mb-5">
                        <div class="flex items-center gap-2">
                            <i data-feather="user" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                            <span class="truncate">{{ $item->siswa->nama_siswa ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-feather="tag" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                            <span class="truncate">{{ ucfirst($item->kategori ?? '-') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('bk.laporan.show', $item->id) }}"
                        class="flex items-center justify-center gap-1.5 w-full py-2.5 sm:py-2 bg-slate-50 hover:bg-blue-700 active:bg-blue-800 text-slate-600 hover:text-white text-sm font-semibold rounded-xl border border-slate-200 hover:border-blue-700 transition-all duration-200">
                        Lihat Detail
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-200 p-8 sm:p-10 text-center">
                    <i data-feather="folder" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-slate-400 text-sm">Belum ada laporan yang diproses.</p>
                </div>
            @endforelse
        </div>
    </section>

@endsection