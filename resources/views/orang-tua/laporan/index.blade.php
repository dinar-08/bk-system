@extends('layouts.orang-tua')

@section('title', 'Laporan Saya')
@section('page-title', 'Laporan Saya')
@section('page-subtitle', 'Pantau laporan permasalahan anak')

@section('content')

<<<<<<< HEAD
    <div class="space-y-5">

        @if(session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl text-sm">
                <i data-feather="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
=======
    @php
        $statusClass = [
            'baru' => 'bg-blue-50 text-blue-700 border-blue-200',
            'pemanggilan' => 'bg-amber-50 text-amber-700 border-amber-200',
            'monitoring' => 'bg-purple-50 text-purple-700 border-purple-200',
            'selesai' => 'bg-green-50 text-green-700 border-green-200',
            'dirujuk' => 'bg-red-50 text-red-700 border-red-200',
        ];

        $statusLabel = [
            'baru' => 'Baru',
            'pemanggilan' => 'Pemanggilan',
            'monitoring' => 'Monitoring',
            'selesai' => 'Selesai',
            'dirujuk' => 'Dirujuk',
        ];
    @endphp

    <div class="space-y-6">

        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
                {{ session('success') }}
>>>>>>> 4226421 (backup)
            </div>
        @endif

        @if(session('warning'))
<<<<<<< HEAD
            <div
                class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-5 py-4 rounded-2xl text-sm">
                <i data-feather="alert-triangle" class="w-5 h-5"></i>
                <span>{{ session('warning') }}</span>
=======
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-700">
                {{ session('warning') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-blue-700 mb-1">Monitoring Permasalahan</p>
                <h1 class="text-2xl font-extrabold text-slate-900">Laporan Saya</h1>
                <p class="text-sm text-slate-500 mt-1.5">
                    Pantau laporan aktif dan riwayat penanganan oleh Guru BK.
                </p>
            </div>

            @if($laporanAktif->isEmpty())
                <a href="{{ route('orang_tua.laporan.create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition">
                    <i data-feather="plus" class="w-4 h-4"></i>
                    Buat Laporan
                </a>
            @endif
        </div>

        @if($laporanAktif->isNotEmpty())
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3.5 text-sm text-amber-700">
                <div class="flex items-center gap-2">
                    <i data-feather="info" class="w-4 h-4"></i>
                    <span class="font-semibold">
                        Tidak bisa membuat laporan baru selama masih ada permasalahan aktif.
                    </span>
                </div>
>>>>>>> 4226421 (backup)
            </div>
        @endif

        {{-- Laporan Aktif --}}
<<<<<<< HEAD
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
=======
        <section>
            <div class="mb-3">
                <h2 class="text-lg font-extrabold text-slate-900">Laporan Aktif</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Permasalahan yang masih dalam proses penanganan.
                </p>
            </div>

            <div class="space-y-3">
                @forelse($laporanAktif as $l)
                    @php
                        $status = $l->status ?? 'baru';
                    @endphp

                    <a href="{{ route('orang_tua.laporan.show', $l->id) }}"
                        class="group block rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm hover:border-blue-200 hover:shadow-md transition">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <span
                                        class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass[$status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                        {{ $statusLabel[$status] ?? ucfirst($status) }}
                                    </span>

                                    <span class="text-sm text-slate-400">
                                        {{ $l->created_at->format('d M Y') }}
                                    </span>
                                </div>

                                <h3
                                    class="text-lg font-extrabold text-slate-900 leading-tight group-hover:text-blue-700 transition">
                                    {{ $l->judul_laporan }}
                                </h3>

                                <p class="text-sm text-slate-500 mt-2">
                                    Guru BK:
                                    <span class="font-semibold text-slate-700">
                                        {{ $l->guruBk->nama ?? 'Belum ditangani' }}
                                    </span>
                                </p>

                                <p class="text-sm text-slate-600 mt-2 leading-relaxed line-clamp-2">
                                    {{ $l->deskripsi }}
                                </p>
                            </div>

                            <div class="shrink-0 sm:pt-9">
                                <span
                                    class="inline-flex items-center gap-1 text-sm font-bold text-blue-700 group-hover:text-blue-900">
                                    Detail
                                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-10 text-center">
                        <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-slate-50">
                            <i data-feather="inbox" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <p class="text-base font-bold text-slate-800">Tidak ada laporan aktif</p>
                        <p class="text-sm text-slate-500 mt-1">
                            Silakan buat laporan jika ada permasalahan yang perlu ditangani Guru BK.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Riwayat --}}
        <section>
            <div class="mb-3 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Riwayat Laporan
                        <span class="text-slate-400">({{ $laporanRiwayat->count() }})</span>
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Laporan yang sudah selesai atau dirujuk.
                    </p>
                </div>

                @if($laporanRiwayat->isNotEmpty())
                    <button type="button" onclick="toggleRiwayat()"
                        class="inline-flex items-center gap-1 text-sm font-bold text-blue-700 hover:text-blue-900">
                        <span id="riwayatText">Tampilkan</span>
                        <i data-feather="chevron-down" id="riwayatIcon" class="w-4 h-4 transition-transform"></i>
                    </button>
                @endif
            </div>

            <div id="riwayatContent" class="space-y-3 hidden">
                @forelse($laporanRiwayat as $l)
                    @php
                        $status = $l->status ?? 'selesai';
                    @endphp

                    <a href="{{ route('orang_tua.laporan.show', $l->id) }}"
                        class="group block rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm hover:border-blue-200 hover:shadow-md transition">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <span
                                        class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass[$status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                        {{ $statusLabel[$status] ?? ucfirst($status) }}
                                    </span>

                                    <span class="text-sm text-slate-400">
                                        {{ $l->created_at->format('d M Y') }}
                                    </span>
                                </div>

                                <h3
                                    class="text-base font-extrabold text-slate-900 leading-tight group-hover:text-blue-700 transition">
                                    {{ $l->judul_laporan }}
                                </h3>

                                <p class="text-sm text-slate-600 mt-2 leading-relaxed line-clamp-2">
                                    {{ $l->deskripsi }}
                                </p>
                            </div>

                            <div class="shrink-0 sm:pt-9">
                                <span
                                    class="inline-flex items-center gap-1 text-sm font-bold text-blue-700 group-hover:text-blue-900">
                                    Detail
                                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-10 text-center">
                        <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-slate-50">
                            <i data-feather="archive" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <p class="text-base font-bold text-slate-800">Belum ada riwayat laporan</p>
                        <p class="text-sm text-slate-500 mt-1">
                            Riwayat akan muncul setelah permasalahan selesai atau dirujuk.
>>>>>>> 4226421 (backup)
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
<<<<<<< HEAD

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

=======
            </div>
        </section>
>>>>>>> 4226421 (backup)
    </div>

    <script>
        function toggleRiwayat() {
            const content = document.getElementById('riwayatContent');
            const icon = document.getElementById('riwayatIcon');
<<<<<<< HEAD
=======
            const text = document.getElementById('riwayatText');
>>>>>>> 4226421 (backup)

            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');

<<<<<<< HEAD
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
=======
            text.textContent = content.classList.contains('hidden') ? 'Tampilkan' : 'Sembunyikan';
>>>>>>> 4226421 (backup)

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
<<<<<<< HEAD
=======

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
>>>>>>> 4226421 (backup)
    </script>

@endsection