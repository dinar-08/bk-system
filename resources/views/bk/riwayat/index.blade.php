@extends('layouts.bk')

@section('title', 'Riwayat Permasalahan')
@section('page-title', 'Riwayat Permasalahan')
@section('page-subtitle', 'Semua permasalahan yang telah selesai atau dirujuk')

@section('content')

    @php
        $laporanPerKategori = $laporan
            ->groupBy(fn($item) => $item->kategori ?: 'lain-lain')
            ->sortKeys();

        $limitTampil = 10;

        $kategoriConfig = [
            'akademik' => ['label' => 'Akademik'],
            'sosial' => ['label' => 'Sosial'],
            'perilaku' => ['label' => 'Perilaku'],
            'emosional' => ['label' => 'Emosional'],
            'lain-lain' => ['label' => 'Lain-lain'],
        ];
    @endphp

    <div class="mb-7 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Riwayat Permasalahan Siswa</h1>
        </div>
    </div>

    <form id="filterRiwayat" method="GET" action="{{ route('bk.riwayat.index') }}"
        class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-7">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

            <div>
                <label for="filter-nama" class="text-xs font-medium text-slate-600 block mb-1">Nama Siswa</label>
                <input type="text" id="filter-nama" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..."
                    autocomplete="off"
                    class="filter-input filter-input-text w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label for="filter-kelas" class="text-xs font-medium text-slate-600 block mb-1">Kelas</label>
                <select id="filter-kelas" name="kelas"
                    class="filter-input filter-input-select w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Kelas</option>
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ request('kelas') === $k ? 'selected' : '' }}>
                            {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter-kategori" class="text-xs font-medium text-slate-600 block mb-1">Kategori</label>
                <select id="filter-kategori" name="kategori"
                    class="filter-input filter-input-select w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                            {{ $kategoriConfig[$kat]['label'] ?? ucfirst($kat) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <a href="{{ route('bk.riwayat.exportPdf', request()->query()) }}"
                    class="flex-1 inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 rounded-xl transition">
                    <i data-feather="download" class="w-4 h-4"></i>
                    PDF
                </a>

                <a href="{{ route('bk.riwayat.index') }}"
                    class="flex-1 inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium py-2 rounded-xl transition">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    Reset
                </a>
            </div>

        </div>
    </form>

    @forelse($laporanPerKategori as $namaKategori => $dataLaporan)
        @php
            $cfg = $kategoriConfig[$namaKategori] ?? ['label' => ucfirst($namaKategori)];

            $sedangFilterKategori = request('kategori') === $namaKategori;

            $dataPreview = $sedangFilterKategori
                ? $dataLaporan
                : $dataLaporan->take($limitTampil);

            $perluLihatSemua = !$sedangFilterKategori && $dataLaporan->count() > $limitTampil;
        @endphp

        <section class="mb-8 bg-white border border-slate-200 rounded-3xl p-4 sm:p-5 lg:p-6 shadow-sm overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">
                        {{ $cfg['label'] }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ $dataLaporan->count() }} Permasalahan
                    </p>
                </div>

                @if($perluLihatSemua)
                    <a href="{{ route('bk.riwayat.index', array_merge(request()->except('page'), ['kategori' => $namaKategori])) }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-800">
                        Lihat Semua
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

            {{-- MOBILE & TABLET: kartu statis (hover tidak bisa dipakai di layar sentuh) --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:hidden gap-3">
                @foreach($dataPreview as $item)
                    @php
                        $status = $item->status ?? '-';
                        $statusBadgeMobile = $status === 'selesai'
                            ? 'bg-green-100 text-green-700 border border-green-200'
                            : 'bg-red-100 text-red-700 border border-red-200';
                        $statusLabel = $status === 'selesai' ? 'Selesai' : 'Dirujuk';

                        $siswa = $item->siswa;
                        $nama = optional($siswa)->nama_siswa ?? '-';
                        $foto = optional($siswa)->foto;
                    @endphp

                    <a href="{{ route('bk.riwayat.show', $item->id) }}"
                        class="block rounded-2xl border border-slate-200 overflow-hidden bg-slate-50 active:scale-[0.98] transition-transform">

                        <div class="relative h-32 w-full bg-center bg-cover">
                            @if($foto)
                                <img src="{{ asset('storage/' . $foto) }}" alt="{{ $nama }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <div
                                    class="absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                                    <span class="text-white/30 text-4xl font-extrabold">
                                        {{ strtoupper(substr($nama, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                            <span
                                class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusBadgeMobile }} bg-white/90">
                                {{ $statusLabel }}
                            </span>
                            <p class="absolute left-2 bottom-2 right-2 text-white text-xs font-bold leading-tight line-clamp-2">
                                {{ $nama }}
                            </p>
                        </div>

                        <div class="p-2.5">
                            <p class="text-[11px] text-slate-500">
                                Kelas {{ optional($siswa)->kelas ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-700 font-medium line-clamp-2 mt-0.5">
                                {{ $item->judul_laporan ?? $item->jenis_masalah ?? '-' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- DESKTOP: strip kartu dengan efek hover melebar --}}
            <div class="hidden md:flex flex-row flex-nowrap overflow-x-auto pb-3">
                @foreach($dataPreview as $item)
                    @php
                        $status = $item->status ?? '-';

                        $statusBadge = $status === 'selesai'
                            ? 'bg-green-400/20 text-green-200 border border-green-300/30'
                            : 'bg-red-400/20 text-red-200 border border-red-300/30';

                        $statusLabel = $status === 'selesai' ? 'Selesai' : 'Dirujuk';

                        $siswa = $item->siswa;
                        $nama = optional($siswa)->nama_siswa ?? '-';
                        $foto = optional($siswa)->foto;
                    @endphp

                    <div class="group relative shrink-0 h-[330px] border-r-4 border-white bg-center overflow-hidden transition-all duration-500 ease-out cursor-pointer"
                        style="width: 58px;" onmouseenter="this.style.width='284px'" onmouseleave="this.style.width='58px'">

                        @if($foto)
                            <img src="{{ asset('storage/' . $foto) }}" alt="{{ $nama }}"
                                class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div
                                class="absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                                <span class="text-white/20 text-8xl font-extrabold">
                                    {{ strtoupper(substr($nama, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-black/25 to-black/90"></div>

                        <p class="absolute left-4 bottom-4 max-w-[250px] text-white text-xs font-extrabold tracking-[2px] uppercase whitespace-nowrap transition-all duration-300 group-hover:opacity-0"
                            style="transform: rotate(-90deg); transform-origin: left bottom;">
                            {{ $nama }}
                        </p>

                        <div
                            class="absolute inset-0 p-5 flex flex-col justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div>
                                <div
                                    class="mb-3 inline-flex items-center px-2.5 py-1 rounded-full {{ $statusBadge }} text-xs font-bold">
                                    {{ $statusLabel }}
                                </div>

                                <h3 class="text-white text-xl font-extrabold leading-tight">
                                    {{ $nama }}
                                </h3>

                                <div class="mt-3 space-y-1.5">
                                    <p class="text-blue-100 text-sm">
                                        Kelas {{ optional($siswa)->kelas ?? '-' }}
                                    </p>
                                    <p class="text-blue-100 text-sm">
                                        {{ $cfg['label'] }}
                                    </p>
                                </div>

                                <p class="text-white/80 text-sm mt-3 line-clamp-3">
                                    {{ $item->judul_laporan ?? $item->jenis_masalah ?? '-' }}
                                </p>
                            </div>

                            <a href="{{ route('bk.riwayat.show', $item->id) }}"
                                class="block w-full bg-white text-slate-900 rounded-xl py-3 text-center font-semibold hover:bg-slate-50 transition-colors">
                                Lihat Detail Permasalahan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-10 sm:p-14 text-center">
            <i data-feather="archive" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-500 font-medium">Tidak ada riwayat yang cocok dengan filter.</p>
        </div>
    @endforelse

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formFilter = document.getElementById('filterRiwayat');
            let timer;

            // Input teks: submit otomatis setelah berhenti mengetik (debounce).
            document.querySelectorAll('.filter-input-text').forEach(function (input) {
                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        formFilter.submit();
                    }, 500);
                });
            });

            // Select: submit langsung saat dipilih, tanpa debounce.
            // (sengaja tidak dipasangi listener 'input' juga, agar tidak submit dua kali)
            document.querySelectorAll('.filter-input-select').forEach(function (select) {
                select.addEventListener('change', function () {
                    clearTimeout(timer);
                    formFilter.submit();
                });
            });

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>

@endsection