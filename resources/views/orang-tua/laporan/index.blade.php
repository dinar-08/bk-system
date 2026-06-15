@extends('layouts.orang-tua')
@section('title', 'Laporan Saya')
@section('content')
    <div class="space-y-6">
        {{-- Warning periode update --}}
        @if(session('warning'))
            <div class="flex gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                {{ session('warning') }}
                <a href="{{ route('orang_tua.update-data') }}" class="underline font-medium ml-1">Perbarui Sekarang →</a>
            </div>
        @endif

        {{-- Laporan Aktif --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Laporan Aktif</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Kasus yang sedang ditangani BK</p>
                </div>
                @if($laporanAktif->isEmpty())
                    <a href="{{ route('orang_tua.laporan.create') }}"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Laporan
                    </a>
                @else
                    <span class="text-xs bg-amber-100 text-amber-700 font-medium px-2.5 py-1 rounded-full">
                        Tidak bisa buat laporan baru selama ada kasus aktif
                    </span>
                @endif
            </div>

            @forelse($laporanAktif as $l)
                <a href="{{ route('orang_tua.laporan.show', $l->id) }}"
                    class="flex items-center justify-between px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition last:border-0">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $l->judul_laporan }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Ditangani: {{ $l->guruBk->nama ?? 'Belum ditangani' }} ·
                            {{ $l->created_at->format('d M Y') }}
                        </p>
                    </div>
                    @php
                        $statusColor = match ($l->status) {
                            'baru' => 'bg-blue-100 text-blue-700',
                            'pemanggilan' => 'bg-amber-100 text-amber-700',
                            'monitoring' => 'bg-purple-100 text-purple-700',
                            default => 'bg-gray-100 text-gray-600'
                        };
                    @endphp
                    <span class="text-xs font-medium {{ $statusColor }} px-2.5 py-1 rounded-full capitalize">
                        {{ str_replace('_', ' ', $l->status) }}
                    </span>
                </a>
            @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-400">Tidak ada laporan aktif saat ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Riwayat Laporan --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">Riwayat Laporan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Kasus yang sudah selesai atau dirujuk</p>
            </div>

            @forelse($laporanRiwayat as $l)
                <a href="{{ route('orang_tua.laporan.show', $l->id) }}"
                    class="flex items-center justify-between px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition last:border-0">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">{{ $l->judul_laporan }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Ditangani: {{ $l->guruBk->nama ?? '-' }} ·
                            {{ $l->created_at->format('d M Y') }}
                        </p>
                    </div>
                    <span class="text-xs font-medium bg-green-100 text-green-700 px-2.5 py-1 rounded-full capitalize">
                        {{ $l->status }}
                    </span>
                </a>
            @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-400">Belum ada riwayat laporan.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection