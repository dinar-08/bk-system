@extends('layouts.orang-tua')
@section('title', 'Perkembangan Anak')
@section('page-title', 'Perkembangan Anak')
@section('page-subtitle', 'Pantau perkembangan dan hasil monitoring dari Guru BK')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Perkembangan Anak</h1>
        <p class="text-slate-500 mt-1 text-sm">Pantau perkembangan dan hasil monitoring dari Guru BK.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($laporan as $item)
            @php
                $initial = strtoupper(substr($item->siswa->nama_siswa, 0, 1));
                $monitoringTerbaru = $item->monitoring->where('status_monitoring', 'selesai')->sortByDesc('tanggal_monitoring')->first();
                $monitoringTerjadwal = $item->monitoring->where('status_monitoring', 'terjadwal')->sortBy('tanggal_monitoring')->first();
                $tanggal = $monitoringTerjadwal?->tanggal_monitoring ?? $monitoringTerbaru?->tanggal_monitoring ?? null;
                $statusPerkembangan = $monitoringTerbaru?->status_perkembangan ?? null;
            @endphp

            <div
                class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

                {{-- Avatar + Nama --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 rounded-xl overflow-hidden flex-shrink-0">
                        @if($item->siswa->foto)
                            <img src="{{ asset('storage/' . $item->siswa->foto) }}" class="w-full h-full object-cover" alt="Foto">
                        @else
                            <div class="w-full h-full flex items-center justify-center font-bold text-lg text-blue-700 bg-blue-100">
                                {{ $initial }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="font-bold text-sm text-slate-900 leading-tight">{{ $item->siswa->nama_siswa }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">NIS {{ $item->siswa->nis }} · {{ $item->siswa->kelas }}</p>
                    </div>
                </div>

                {{-- Judul --}}
                <h3 class="font-bold text-slate-800 text-sm leading-snug mb-3 line-clamp-2">{{ $item->judul_laporan }}</h3>

                {{-- Meta --}}
                <div class="space-y-1.5 text-sm text-slate-500 mb-4">
                    <div class="flex items-center gap-2">
                        <i data-feather="tag" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                        <span>{{ $item->jenis_masalah ?? 'Menunggu verifikasi BK' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-feather="calendar" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                        <span>{{ $tanggal ?? '—' }}</span>
                    </div>
                    @if($statusPerkembangan)
                        <div class="flex items-center gap-2">
                            <i data-feather="trending-up" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($statusPerkembangan == 'membaik') bg-green-100 text-green-700
                                    @elseif($statusPerkembangan == 'stabil') bg-blue-100 text-blue-700
                                    @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($statusPerkembangan) }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Action --}}
                <a href="{{ route('orang_tua.perkembangan.show', $item->id) }}"
                    class="flex items-center justify-center gap-1.5 w-full py-2 bg-blue-50 hover:bg-blue-700 text-blue-700 hover:text-white text-sm font-semibold rounded-xl border border-blue-100 hover:border-blue-700 transition-all duration-200">
                    Lihat Detail
                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

        @empty
            <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-200 p-14 text-center">
                <i data-feather="trending-up" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-500 font-medium">Belum ada data perkembangan anak.</p>
            </div>
        @endforelse
    </div>

@endsection