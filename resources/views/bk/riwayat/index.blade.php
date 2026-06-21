@extends('layouts.bk')

@section('title', 'Riwayat Kasus')
@section('page-title', 'Riwayat Kasus')
@section('page-subtitle', 'Semua kasus yang telah selesai atau dirujuk')

@section('content')

    @php
        $laporanPerKategori = $laporan->groupBy('kategori');
        $limitTampil = 10;

        $kategoriConfig = [
            'akademik' => ['label' => 'Akademik', 'gradient' => 'from-blue-600 via-indigo-700 to-slate-900'],
            'sosial' => ['label' => 'Sosial', 'gradient' => 'from-emerald-600 via-emerald-700 to-slate-900'],
            'perilaku' => ['label' => 'Perilaku', 'gradient' => 'from-amber-600 via-amber-700 to-slate-900'],
            'emosional' => ['label' => 'Emosional', 'gradient' => 'from-rose-600 via-rose-700 to-slate-900'],
            'lain-lain' => ['label' => 'Lain-lain', 'gradient' => 'from-slate-600 via-slate-700 to-slate-900'],
        ];

        $defaultCfg = [
            'label' => 'Tanpa Kategori',
            'gradient' => 'from-slate-600 via-slate-700 to-slate-900',
        ];
    @endphp

    <div class="bg-blue-700 -mx-6 px-8 py-5 mb-6">
        <h2 class="text-white font-bold text-xl">Riwayat Permasalahan Siswa</h2>
        <p class="text-blue-100 text-sm mt-1">{{ $laporan->count() }} kasus ditemukan</p>
    </div>

    <form id="filterRiwayat" method="GET" action="{{ route('bk.riwayat.index') }}"
        class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-7">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

            <div>
                <label class="text-xs font-medium text-slate-600 block mb-1">Nama Siswa</label>
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..." autocomplete="off"
                    class="filter-input w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="text-xs font-medium text-slate-600 block mb-1">Kelas</label>
                <select name="kelas"
                    class="filter-input w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Kelas</option>
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ request('kelas') === $k ? 'selected' : '' }}>
                            {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-medium text-slate-600 block mb-1">Kategori</label>
                <select name="kategori"
                    class="filter-input w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                            {{ ucfirst($kat) }}
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

                            $statusLabel = $status === 'selesai' ? 'Selesai' : 'Dirujuk';

                            $siswa = $item->siswa;
                            $nama = optional($siswa)->nama_siswa ?? '-';
                            $judul = $item->judul_laporan ?? $nama;
                            $foto = optional($siswa)->foto;
                        @endphp

                        <div class="group relative h-[300px] w-[70px] hover:w-[280px] shrink-0 overflow-hidden rounded-xl transition-all duration-500 shadow-lg bg-gradient-to-br {{ $cfg['gradient'] }} bg-cover bg-center"
                            @if($foto) style="background-image:url('{{ asset('storage/' . $foto) }}')" @endif>

                            <div
                                class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/50 to-black/85 group-hover:from-black/30 group-hover:via-black/40 group-hover:to-black/90 transition-colors duration-300">
                            </div>

                            <div class="absolute inset-0 flex items-center justify-center group-hover:opacity-0 transition duration-300 px-2"
                                style="writing-mode: vertical-rl; transform: rotate(180deg);">
                                <span class="text-white text-xs font-bold tracking-[3px] uppercase line-clamp-1">
                                    {{ $judul }}
                                </span>
                            </div>

                            <div
                                class="absolute inset-0 p-5 flex flex-col justify-between opacity-0 group-hover:opacity-100 transition duration-300 delay-75">
                                <div>
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white mb-4">
                                        {{ $cfg['label'] }}
                                    </span>

                                    <h3 class="text-white text-xl font-bold leading-tight break-words">
                                        {{ $nama }}
                                    </h3>

                                    <p class="text-white/70 text-sm mt-2 line-clamp-2">
                                        {{ $item->judul_laporan ?? $item->jenis_masalah ?? '-' }}
                                    </p>

                                    <div class="flex items-center gap-2 mt-4 text-sm">
                                        <span class="text-white/50">Kelas</span>
                                        <span class="text-white font-semibold">
                                            {{ optional($siswa)->kelas ?? '-' }}
                                        </span>
                                    </div>

                                    <p class="mt-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusBadge }}">
                                            {{ $statusLabel }}
                                        </span>
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
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
                <p class="text-slate-500">Tidak ada riwayat yang cocok dengan filter.</p>
            </div>
        @endforelse
    </div>

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