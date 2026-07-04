@extends('layouts.orang-tua')

@section('title', 'Laporan Saya')
@section('page-title', 'Laporan Saya')
@section('page-subtitle', 'Pantau laporan permasalahan anak')

@section('content')
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

        @if(session('warning'))
            <div
                class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-5 py-4 rounded-2xl text-sm">
                <i data-feather="alert-triangle" class="w-5 h-5"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Laporan Saya</h1>
                <p class="text-sm text-slate-500 mt-1.5">
                    Pantau laporan aktif dan riwayat penanganan oleh Guru BK.
                </p>
            </div>

            @if(!$adaLaporanBerjalan)
                <a href="{{ route('orang_tua.laporan.create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition">
                    <i data-feather="plus" class="w-4 h-4"></i>
                    Buat Laporan
                </a>
            @endif
        </div>

        @if($adaLaporanBerjalan)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3.5 text-sm text-amber-700">
                <div class="flex items-center gap-2">
                    <i data-feather="info" class="w-4 h-4"></i>
                    <span class="font-semibold">
                        Tidak bisa membuat laporan baru selama masih ada permasalahan aktif.
                    </span>
                </div>
            </div>
        @endif

        {{-- ===== TAB NAV (mirip Home / News JKT48) ===== --}}
        <div class="flex items-center gap-6 border-b border-slate-200">
            <button type="button" onclick="showLaporanTab('aktif')" id="tabAktifBtn"
                class="relative pb-3 text-sm font-extrabold text-blue-700 transition">
                Laporan Aktif
                <span id="tabAktifUnderline" class="absolute left-0 -bottom-px h-0.5 w-full bg-blue-700"></span>
            </button>

            <button type="button" onclick="showLaporanTab('riwayat')" id="tabRiwayatBtn"
                class="relative pb-3 text-sm font-extrabold text-slate-400 hover:text-slate-600 transition">
                Riwayat Laporan
                <span id="tabRiwayatUnderline" class="absolute left-0 -bottom-px h-0.5 w-full bg-blue-700 hidden"></span>
            </button>
        </div>

        {{-- ===== TAB CONTENT: LAPORAN AKTIF ===== --}}
        <section id="tabAktifContent">

            <div class="space-y-3">
                @forelse($laporanAktif as $l)
                    @continue(($l->status ?? 'baru') === 'monitoring')
                    @php $status = $l->status ?? 'baru'; @endphp

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

        {{-- ===== TAB CONTENT: RIWAYAT (gaya list JKT48 News) ===== --}}
        <section id="tabRiwayatContent" class="hidden">
            <div class="mb-4">
                <p class="text-sm text-slate-500">
                    Laporan yang sudah selesai atau dirujuk.
                </p>
            </div>

            @forelse($laporanRiwayat as $l)
                @php $status = $l->status ?? 'selesai'; @endphp

                <div class="border-t border-slate-200 py-6 first:border-t-0 first:pt-0">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <span
                            class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass[$status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                            {{ $statusLabel[$status] ?? ucfirst($status) }}
                        </span>
                        <span class="text-sm text-slate-400">
                            {{ $l->created_at->format('d M Y') }}
                        </span>
                    </div>

                    <a href="{{ route('orang_tua.laporan.show', $l->id) }}" class="group block">
                        <h3 class="text-xl font-extrabold text-slate-900 leading-snug group-hover:text-blue-700 transition">
                            {{ $l->judul_laporan }}
                        </h3>
                    </a>

                    <p class="text-sm text-slate-600 mt-2 leading-relaxed line-clamp-2 max-w-2xl">
                        {{ $l->deskripsi }}
                    </p>

                    <a href="{{ route('orang_tua.laporan.show', $l->id) }}"
                        class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-700 hover:text-blue-900 mt-3 transition">
                        Lihat Selengkapnya
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-10 text-center">
                    <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-slate-50">
                        <i data-feather="archive" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <p class="text-base font-bold text-slate-800">Belum ada riwayat laporan</p>
                    <p class="text-sm text-slate-500 mt-1">
                        Riwayat akan muncul setelah permasalahan selesai atau dirujuk.
                    </p>
                </div>
            @endforelse
        </section>

    </div>

    <script>
        function showLaporanTab(tab) {
            const aktifContent = document.getElementById('tabAktifContent');
            const riwayatContent = document.getElementById('tabRiwayatContent');
            const aktifBtn = document.getElementById('tabAktifBtn');
            const riwayatBtn = document.getElementById('tabRiwayatBtn');
            const aktifUnderline = document.getElementById('tabAktifUnderline');
            const riwayatUnderline = document.getElementById('tabRiwayatUnderline');

            const isAktif = tab === 'aktif';

            aktifContent.classList.toggle('hidden', !isAktif);
            riwayatContent.classList.toggle('hidden', isAktif);

            aktifBtn.classList.toggle('text-blue-700', isAktif);
            aktifBtn.classList.toggle('text-slate-400', !isAktif);
            riwayatBtn.classList.toggle('text-blue-700', !isAktif);
            riwayatBtn.classList.toggle('text-slate-400', isAktif);

            aktifUnderline.classList.toggle('hidden', !isAktif);
            riwayatUnderline.classList.toggle('hidden', isAktif);

            // simpan pilihan tab biar tetap kebuka saat halaman di-refresh
            try { localStorage.setItem('laporanSayaTab', tab); } catch (e) { }

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            let savedTab = 'aktif';
            try { savedTab = localStorage.getItem('laporanSayaTab') || 'aktif'; } catch (e) { }
            showLaporanTab(savedTab);

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endsection