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

        <div class="flex items-center gap-3 w-full lg:w-auto">
            {{-- SEARCH LIVE — cukup ketik, langsung filter di bawah, tanpa klik --}}
            <div class="relative flex-1 lg:w-72">
                <i data-feather="search"
                    class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" id="cariSiswaLive" autocomplete="off" placeholder="Cari nama siswa..."
                    class="w-full border border-slate-300 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
            </div>

            {{-- EXPORT PDF --}}
            <a href="{{ route('bk.riwayat.exportPdf', request()->only('kategori', 'kelas')) }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition whitespace-nowrap">
                <i data-feather="download" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Export PDF</span>
            </a>
        </div>
    </div>

    {{-- ================= TAMPILAN DEFAULT (periode aktif) ================= --}}
    <div id="defaultKategoriView">
        @forelse($laporanPerKategori as $namaKategori => $dataLaporan)
            @php
                $cfg = $kategoriConfig[$namaKategori] ?? ['label' => ucfirst($namaKategori)];
                $sedangFilterKategori = request('kategori') === $namaKategori;
                $dataPreview = $sedangFilterKategori ? $dataLaporan : $dataLaporan->take($limitTampil);
                $perluLihatSemua = !$sedangFilterKategori && $dataLaporan->count() > $limitTampil;
            @endphp

            <section class="mb-8 bg-white border border-slate-200 rounded-3xl p-4 sm:p-5 lg:p-6 shadow-sm overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">{{ $cfg['label'] }}</h2>
                        <p class="text-sm text-slate-500 mt-1">{{ $dataLaporan->count() }} Permasalahan</p>
                    </div>

                    @if($perluLihatSemua)
                        <a href="{{ route('bk.riwayat.index', array_merge(request()->except('page'), ['kategori' => $namaKategori])) }}"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-800">
                            Lihat Semua
                            <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>

                {{-- MOBILE & TABLET --}}
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

                        <a href="{{ route('bk.riwayat.siswa', $siswa->nis) }}"
                            class="block rounded-2xl border border-slate-200 overflow-hidden bg-white shadow-sm active:scale-[0.98] transition-transform">

                            <div class="relative h-28 w-full bg-center bg-cover bg-slate-100">
                                @if($foto)
                                    <img src="{{ route('foto.siswa', $siswa->nis) }}" alt="{{ $nama }}"
                                        class="absolute inset-0 w-full h-full object-cover"
                                        onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">

                                    <div class="hidden absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                                        <span class="text-white/40 text-3xl font-extrabold">
                                            {{ strtoupper(substr($nama, 0, 1)) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                                        <span class="text-white/40 text-3xl font-extrabold">
                                            {{ strtoupper(substr($nama, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusBadgeMobile }} bg-white shadow-sm">
                                    {{ $statusLabel }}
                                </span>
                                <p class="absolute left-2.5 bottom-2 right-2.5 text-white text-xs font-bold leading-tight line-clamp-2 drop-shadow">
                                    {{ $nama }}
                                </p>
                            </div>

                            <div class="p-3">
                                <p class="text-[11px] text-slate-400 font-medium">
                                    Kelas {{ optional($siswa)->kelas ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-700 font-semibold line-clamp-2 mt-1 leading-snug">
                                    {{ $item->judul_laporan ?? $item->jenis_masalah ?? '-' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- DESKTOP --}}
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
                                <img src="{{ route('foto.siswa', $siswa->nis) }}" alt="{{ $nama }}"
                                    class="absolute inset-0 w-full h-full object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">

                                <div class="hidden absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                                    <span class="text-white/60 text-8xl font-extrabold">
                                        {{ strtoupper(substr($nama, 0, 1)) }}
                                    </span>
                                </div>
                            @else
                                <div class="absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                                    <span class="text-white/60 text-8xl font-extrabold">
                                        {{ strtoupper(substr($nama, 0, 1)) }}
                                    </span>
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-black/25 to-black/90"></div>

                            <p class="absolute left-4 bottom-4 max-w-[250px] text-white text-xs font-extrabold tracking-[2px] uppercase whitespace-nowrap transition-all duration-300 group-hover:opacity-0"
                                style="transform: rotate(-90deg); transform-origin: left bottom;">
                                {{ $nama }}
                            </p>

                            <div class="absolute inset-0 p-5 flex flex-col justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div>
                                    <div class="mb-3 inline-flex items-center px-2.5 py-1 rounded-full {{ $statusBadge }} text-xs font-bold">
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

                                <a href="{{ route('bk.riwayat.siswa', $siswa->nis) }}"
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
    </div>

    {{-- ================= HASIL SEARCH (di-render JS, lintas semua tahun ajaran) ================= --}}
    <div id="searchResultsView" class="hidden"></div>

    <script>
        const KATEGORI_CONFIG = @json($kategoriConfig);
        const SEMUA_LAPORAN = @json($semuaLaporanUntukSearch);

        function buatKartuMobile(item) {
            const statusBadge = item.status === 'selesai'
                ? 'bg-green-100 text-green-700 border border-green-200'
                : 'bg-red-100 text-red-700 border border-red-200';
            const statusLabel = item.status === 'selesai' ? 'Selesai' : 'Dirujuk';
            const inisial = item.nama.charAt(0).toUpperCase();

            const fotoBlock = item.foto_url
                ? `<img src="${item.foto_url}" alt="${item.nama}" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                   <div class="hidden absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                       <span class="text-white/40 text-3xl font-extrabold">${inisial}</span>
                   </div>`
                : `<div class="absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                       <span class="text-white/40 text-3xl font-extrabold">${inisial}</span>
                   </div>`;

            return `
                <a href="${item.show_url}" class="block rounded-2xl border border-slate-200 overflow-hidden bg-white shadow-sm active:scale-[0.98] transition-transform">
                    <div class="relative h-28 w-full bg-center bg-cover bg-slate-100">
                        ${fotoBlock}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>
                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold ${statusBadge} bg-white shadow-sm">${statusLabel}</span>
                        <p class="absolute left-2.5 bottom-2 right-2.5 text-white text-xs font-bold leading-tight line-clamp-2 drop-shadow">${item.nama}</p>
                    </div>
                    <div class="p-3">
                        <p class="text-[11px] text-slate-400 font-medium">Kelas ${item.kelas}</p>
                        <p class="text-xs text-slate-700 font-semibold line-clamp-2 mt-1 leading-snug">${item.judul}</p>
                    </div>
                </a>`;
        }

        function buatKartuDesktop(item, kategoriLabel) {
            const statusBadge = item.status === 'selesai'
                ? 'bg-green-400/20 text-green-200 border border-green-300/30'
                : 'bg-red-400/20 text-red-200 border border-red-300/30';
            const statusLabel = item.status === 'selesai' ? 'Selesai' : 'Dirujuk';
            const inisial = item.nama.charAt(0).toUpperCase();

            const fotoBlock = item.foto_url
                ? `<img src="${item.foto_url}" alt="${item.nama}" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                   <div class="hidden absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                       <span class="text-white/60 text-8xl font-extrabold">${inisial}</span>
                   </div>`
                : `<div class="absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                       <span class="text-white/60 text-8xl font-extrabold">${inisial}</span>
                   </div>`;

            return `
                <div class="group relative shrink-0 h-[330px] border-r-4 border-white bg-center overflow-hidden transition-all duration-500 ease-out cursor-pointer"
                    style="width: 58px;" onmouseenter="this.style.width='284px'" onmouseleave="this.style.width='58px'">
                    ${fotoBlock}
                    <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-black/25 to-black/90"></div>
                    <p class="absolute left-4 bottom-4 max-w-[250px] text-white text-xs font-extrabold tracking-[2px] uppercase whitespace-nowrap transition-all duration-300 group-hover:opacity-0"
                        style="transform: rotate(-90deg); transform-origin: left bottom;">${item.nama}</p>
                    <div class="absolute inset-0 p-5 flex flex-col justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div>
                            <div class="mb-3 inline-flex items-center px-2.5 py-1 rounded-full ${statusBadge} text-xs font-bold">${statusLabel}</div>
                            <h3 class="text-white text-xl font-extrabold leading-tight">${item.nama}</h3>
                            <div class="mt-3 space-y-1.5">
                                <p class="text-blue-100 text-sm">Kelas ${item.kelas}</p>
                                <p class="text-blue-100 text-sm">${kategoriLabel}</p>
                            </div>
                            <p class="text-white/80 text-sm mt-3 line-clamp-3">${item.judul}</p>
                        </div>
                        <a href="${item.show_url}" class="block w-full bg-white text-slate-900 rounded-xl py-3 text-center font-semibold hover:bg-slate-50 transition-colors">
                            Lihat Detail Permasalahan
                        </a>
                    </div>
                </div>`;
        }

        function renderSectionKategori(kategoriKey, items) {
            const label = (KATEGORI_CONFIG[kategoriKey] && KATEGORI_CONFIG[kategoriKey].label) || kategoriKey;
            const mobileCards = items.map(buatKartuMobile).join('');
            const desktopCards = items.map(item => buatKartuDesktop(item, label)).join('');

            return `
                <section class="mb-8 bg-white border border-slate-200 rounded-3xl p-4 sm:p-5 lg:p-6 shadow-sm overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">${label}</h2>
                            <p class="text-sm text-slate-500 mt-1">${items.length} Permasalahan</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:hidden gap-3">${mobileCards}</div>
                    <div class="hidden md:flex flex-row flex-nowrap overflow-x-auto pb-3">${desktopCards}</div>
                </section>`;
        }

        function tampilkanHasilSearch(keyword) {
            const searchView = document.getElementById('searchResultsView');
            const kw = keyword.trim().toLowerCase();

            const hasil = SEMUA_LAPORAN.filter(item => item.nama.toLowerCase().includes(kw));

            if (hasil.length === 0) {
                searchView.innerHTML = `
                    <div class="bg-white rounded-2xl border border-slate-200 p-10 sm:p-14 text-center">
                        <i data-feather="archive" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                        <p class="text-slate-500 font-medium">Tidak ada riwayat siswa dengan nama "${keyword}".</p>
                    </div>`;
                if (typeof feather !== 'undefined') feather.replace();
                return;
            }

            const grouped = {};
            hasil.forEach(item => {
                const key = item.kategori || 'lain-lain';
                if (!grouped[key]) grouped[key] = [];
                grouped[key].push(item);
            });

            searchView.innerHTML = Object.keys(grouped).sort()
                .map(key => renderSectionKategori(key, grouped[key]))
                .join('');

            if (typeof feather !== 'undefined') feather.replace();
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') feather.replace();

            const input = document.getElementById('cariSiswaLive');
            const defaultView = document.getElementById('defaultKategoriView');
            const searchView = document.getElementById('searchResultsView');

            input.addEventListener('input', function () {
                const keyword = this.value.trim();

                if (keyword.length === 0) {
                    defaultView.classList.remove('hidden');
                    searchView.classList.add('hidden');
                    searchView.innerHTML = '';
                    return;
                }

                defaultView.classList.add('hidden');
                searchView.classList.remove('hidden');
                tampilkanHasilSearch(keyword);
            });
        });
    </script>

@endsection