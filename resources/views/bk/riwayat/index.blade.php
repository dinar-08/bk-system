@extends('layouts.bk')

@section('title', 'Riwayat Kasus')
@section('page-title', 'Riwayat Kasus')
@section('page-subtitle', 'Semua kasus yang telah selesai atau dirujuk')

@section('content')

    @php
<<<<<<< HEAD
        $laporanPerKategori = $laporan->groupBy('kategori');
=======
>>>>>>> 4226421 (backup)
        $limitTampil = 10;

        $kategoriConfig = [
            'akademik' => ['label' => 'Akademik'],
            'sosial' => ['label' => 'Sosial'],
            'perilaku' => ['label' => 'Perilaku'],
            'emosional' => ['label' => 'Emosional'],
            'lain-lain' => ['label' => 'Lain-lain'],
        ];

<<<<<<< HEAD
        $defaultCfg = [
            'label' => 'Tanpa Kategori',
            'gradient' => 'from-slate-600 via-slate-700 to-slate-900',
        ];
    @endphp

    <div class="bg-blue-700 -mx-6 px-8 py-5 mb-6">
        <h2 class="text-white font-bold text-xl">Riwayat Permasalahan Siswa</h2>
        <p class="text-blue-100 text-sm mt-1">{{ $laporan->count() }} kasus ditemukan</p>
=======
        $laporanPerKategori = $laporan
            ->groupBy(fn($item) => $item->kategori ?: 'lain-lain')
            ->sortKeys();
    @endphp

    <div class="mb-7 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Riwayat Permasalahan Siswa</h1>
        </div>
>>>>>>> 4226421 (backup)
    </div>

    <form id="filterRiwayat" method="GET" action="{{ route('bk.riwayat.index') }}"
        class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-7">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

            <div>
<<<<<<< HEAD
                <label class="text-xs font-medium text-slate-600 block mb-1">Nama Siswa</label>
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..." autocomplete="off"
                    class="filter-input w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="text-xs font-medium text-slate-600 block mb-1">Kelas</label>
                <select name="kelas"
                    class="filter-input w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
=======
                <label for="filter-nama" class="text-xs font-medium text-slate-600 block mb-1">Nama Siswa</label>
                <input type="text" id="filter-nama" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..." autocomplete="off"
                    class="filter-input filter-input-text w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label for="filter-kelas" class="text-xs font-medium text-slate-600 block mb-1">Kelas</label>
                <select id="filter-kelas" name="kelas"
                    class="filter-input filter-input-select w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
>>>>>>> 4226421 (backup)
                    <option value="">Semua Kelas</option>
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ request('kelas') === $k ? 'selected' : '' }}>
                            {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
<<<<<<< HEAD
                <label class="text-xs font-medium text-slate-600 block mb-1">Kategori</label>
                <select name="kategori"
                    class="filter-input w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                            {{ ucfirst($kat) }}
=======
                <label for="filter-kategori" class="text-xs font-medium text-slate-600 block mb-1">Kategori</label>
                <select id="filter-kategori" name="kategori"
                    class="filter-input filter-input-select w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                            {{ $kategoriConfig[$kat]['label'] ?? ucfirst($kat) }}
>>>>>>> 4226421 (backup)
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

<<<<<<< HEAD
    <div>
        @forelse($laporanPerKategori as $namaKategori => $dataLaporan)
            @php
                $cfg = $kategoriConfig[$namaKategori] ?? array_merge($defaultCfg, [
                    'label' => ucfirst($namaKategori ?? 'Tanpa Kategori'),
                ]);

                $sedangFilterKategori = request('kategori') === $namaKategori;

                $dataPreview = $sedangFilterKategori
                    ? $dataLaporan
                    : $dataLaporan->take($limitTampil);

                $perluLihatSemua = !$sedangFilterKategori && $dataLaporan->count() > $limitTampil;
            @endphp

            <section class="mb-12">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900">{{ $cfg['label'] }}</h2>
                        <p class="text-slate-500">{{ $dataLaporan->count() }} kasus</p>
                    </div>

                    @if($perluLihatSemua)
                        <a href="{{ route('bk.riwayat.index', array_merge(request()->except('page'), ['kategori' => $namaKategori])) }}"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700 mt-2">
                            Lihat Semua
                            <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>

                <div class="flex overflow-x-auto min-h-[320px] gap-2 pb-4">
                    @foreach($dataPreview as $item)
                        @php
                            $status = $item->status ?? '-';

                            $statusBadge = $status === 'selesai'
                                ? 'bg-emerald-400/90 text-emerald-950'
                                : 'bg-rose-400/90 text-rose-950';
=======
    @forelse($laporanPerKategori as $namaKategori => $dataLaporan)
        @php
            $cfg = $kategoriConfig[$namaKategori] ?? ['label' => ucfirst($namaKategori)];

            $sedangFilterKategori = request('kategori') === $namaKategori;

            $dataPreview = $sedangFilterKategori
                ? $dataLaporan
                : $dataLaporan->take($limitTampil);

            $perluLihatSemua = !$sedangFilterKategori && $dataLaporan->count() > $limitTampil;
        @endphp

        <section class="mb-8 bg-white border border-slate-200 rounded-3xl p-5 lg:p-6 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">
                        {{ $cfg['label'] }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ $dataLaporan->count() }} kasus
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
>>>>>>> 4226421 (backup)

            <div class="flex flex-row flex-nowrap overflow-x-auto pb-3">
                @foreach($dataPreview as $item)
                    @php
                        $status = $item->status ?? '-';

                        $statusBadge = $status === 'selesai'
                            ? 'bg-green-400/20 text-green-200 border border-green-300/30'
                            : 'bg-red-400/20 text-red-200 border border-red-300/30';

<<<<<<< HEAD
                        <div class="group relative h-[300px] w-[70px] hover:w-[280px] shrink-0 overflow-hidden rounded-xl transition-all duration-500 shadow-lg bg-gradient-to-br {{ $cfg['gradient'] }} bg-cover bg-center"
                            @if($foto) style="background-image:url('{{ asset('storage/' . $foto) }}')" @endif>
=======
                        $statusLabel = $status === 'selesai' ? 'Selesai' : 'Dirujuk';
>>>>>>> 4226421 (backup)

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
                                Lihat Detail Kasus
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-14 text-center">
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

    <script>
        const formFilter = document.getElementById('filterRiwayat');
        const filterInputs = document.querySelectorAll('.filter-input');
        let timer;

        filterInputs.forEach(input => {
            input.addEventListener('input', function () {
                clearTimeout(timer);

                timer = setTimeout(() => {
                    formFilter.submit();
                }, 500);
            });

            input.addEventListener('change', function () {
                formFilter.submit();
            });
        });
    </script>

@endsection