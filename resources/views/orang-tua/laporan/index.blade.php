@extends('layouts.orang-tua')

@section('title', 'Laporan Saya')
@section('page-title', 'Laporan Saya')
@section('page-subtitle', 'Pantau laporan permasalahan anak')

@section('content')

    <div class="space-y-5">

        @if(session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl text-sm">
                <i data-feather="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div
                class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-5 py-4 rounded-2xl text-sm">
                <i data-feather="alert-triangle" class="w-5 h-5"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        {{-- Laporan Aktif --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-900">Laporan Aktif</h2>
                <p class="text-sm text-slate-500 mt-1">Permasalahan yang sedang ditangani oleh Guru BK.</p>
            </div>

            @if($laporanAktif->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center">
                    <p class="text-sm font-semibold text-slate-700">Tidak ada laporan aktif.</p>
                    <p class="text-sm text-slate-400 mt-1">Silakan buat laporan jika ada permasalahan anak.</p>

                    <a href="{{ route('orang_tua.laporan.create') }}"
                        class="inline-flex items-center justify-center gap-2 mt-4 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl">
                        <i data-feather="plus" class="w-4 h-4"></i>
                        Buat Laporan
                    </a>
                </div>
            @else
                <div
                    class="mb-5 flex items-center gap-2 bg-amber-50 text-amber-700 border border-amber-200 text-sm font-semibold px-4 py-3 rounded-xl">
                    <i data-feather="info" class="w-4 h-4"></i>
                    Tidak bisa membuat laporan baru selama ada permasalahan aktif
                </div>

                <div class="space-y-3">
                    @foreach($laporanAktif as $l)
                        @php
                            $statusColor = match ($l->status) {
                                'baru' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'pemanggilan' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'monitoring' => 'bg-purple-50 text-purple-700 border-purple-100',
                                default => 'bg-slate-50 text-slate-600 border-slate-100',
                            };

                            $statusLabel = match ($l->status) {
                                'baru' => 'Baru',
                                'pemanggilan' => 'Pemanggilan',
                                'monitoring' => 'Monitoring',
                                default => ucfirst($l->status),
                            };
                        @endphp

                        <a href="{{ route('orang_tua.laporan.show', $l->id) }}"
                            class="block rounded-2xl border border-slate-200 p-5 hover:bg-slate-50 transition">
                            <h3 class="text-base font-bold text-slate-900">{{ $l->judul_laporan }}</h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Ditangani:
                                <span class="font-medium text-slate-700">
                                    {{ $l->guruBk->nama ?? 'Belum ditangani' }}
                                </span>
                                · {{ $l->created_at->format('d M Y') }}
                            </p>

                            <p class="text-sm text-slate-500 mt-3 line-clamp-2">
                                {{ $l->deskripsi }}
                            </p>

                            <span class="inline-flex mt-4 text-xs font-semibold {{ $statusColor }} border px-3 py-1.5 rounded-full">
                                {{ $statusLabel }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Riwayat Laporan --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <button type="button" onclick="toggleRiwayat()"
                class="w-full flex items-center justify-between px-6 py-5 text-left">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Riwayat Laporan
                        <span class="text-slate-500">({{ $laporanRiwayat->count() }})</span>
                    </h2>
                </div>

                <i data-feather="chevron-up" id="riwayatIcon" class="w-5 h-5 text-blue-700 transition-transform"></i>
            </button>

            <div id="riwayatContent" class="px-6 pb-5">
                @forelse($laporanRiwayat->take(5) as $l)
                    <a href="{{ route('orang_tua.laporan.show', $l->id) }}"
                        class="flex items-center justify-between gap-3 py-3 border-t border-slate-100 hover:bg-slate-50 transition -mx-2 px-2 rounded-lg">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <i data-feather="file-text" class="w-4 h-4 text-blue-700"></i>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $l->judul_laporan }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $l->status === 'dirujuk' ? 'Dirujuk' : 'Selesai' }}
                                </p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 flex-shrink-0">
                            {{ $l->created_at->format('d M Y') }}
                        </p>
                    </a>
                @empty
                    <div class="py-8 text-center border-t border-slate-100">
                        <p class="text-sm font-semibold text-slate-700">Belum ada riwayat laporan.</p>
                        <p class="text-sm text-slate-400 mt-1">
                            Riwayat akan muncul setelah permasalahan selesai atau dirujuk.
                        </p>
                    </div>
                @endforelse

                @if($laporanRiwayat->count() > 5)
                    <button type="button" onclick="toggleSemuaRiwayat()"
                        class="mt-3 text-sm font-semibold text-blue-700 hover:text-blue-800">
                        <span id="lihatSemuaText">Lihat semua laporan</span>
                    </button>

                    <div id="semuaRiwayatContent" class="hidden mt-2">
                        @foreach($laporanRiwayat->skip(5) as $l)
                            <a href="{{ route('orang_tua.laporan.show', $l->id) }}"
                                class="flex items-center justify-between gap-3 py-3 border-t border-slate-100 hover:bg-slate-50 transition -mx-2 px-2 rounded-lg">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i data-feather="file-text" class="w-4 h-4 text-blue-700"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">
                                            {{ $l->judul_laporan }}
                                        </p>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $l->status === 'dirujuk' ? 'Dirujuk' : 'Selesai' }}
                                        </p>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-400 flex-shrink-0">
                                    {{ $l->created_at->format('d M Y') }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        function toggleRiwayat() {
            const content = document.getElementById('riwayatContent');
            const icon = document.getElementById('riwayatIcon');

            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }

        function toggleSemuaRiwayat() {
            const content = document.getElementById('semuaRiwayatContent');
            const text = document.getElementById('lihatSemuaText');

            content.classList.toggle('hidden');

            if (content.classList.contains('hidden')) {
                text.textContent = 'Lihat semua laporan';
            } else {
                text.textContent = 'Sembunyikan laporan';
            }

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    </script>

@endsection