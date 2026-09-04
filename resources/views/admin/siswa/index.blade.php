@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Kelola data siswa dan akun orang tua berdasarkan kelas')

@section('content')

    @php
        function urutanKelas($kelas)
        {
            $kelas = trim($kelas);

            $isInternasional = stripos($kelas, 'internasional') !== false;
            $grup = $isInternasional ? 1 : 0;

            preg_match('/(\d+)/', $kelas, $angka);
            $tingkat = isset($angka[1]) ? (int) $angka[1] : 999;

            preg_match('/([A-Za-z]+)\s*$/', $kelas, $huruf);
            $section = isset($huruf[1]) ? strtoupper($huruf[1]) : 'ZZ';

            if (!isset($angka[1]) && !$isInternasional) {
                $grup = 2;
            }

            return sprintf('%d-%03d-%s', $grup, $tingkat, $section);
        }

        $siswaAktif = $siswa->filter(
            fn($item) => ($item->user->status_akun ?? 'aktif') === 'aktif'
        );

        $siswaPerKelas = $siswaAktif
            ->groupBy('kelas')
            ->sortKeysUsing(fn($a, $b) => urutanKelas($a) <=> urutanKelas($b))
            ->map(fn($group) => $group->sortBy('nama_siswa'));

        $batasTampil = 15;
    @endphp

    <div class="mb-7 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Siswa</h1>
            <p class="text-slate-500 mt-1 text-sm">Kelola data siswa dan akun orang tua berdasarkan kelas.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" onclick="openDownloadModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                <i data-feather="download" class="w-4 h-4"></i>
                Download Data
            </button>

            <button type="button" onclick="openPeriodeModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 transition">
                <i data-feather="calendar" class="w-4 h-4"></i>
                Periode Update
            </button>

            <a href="{{ route('admin.siswa.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition">
                <i data-feather="plus" class="w-4 h-4"></i>
                Tambah Siswa
            </a>
        </div>
    </div>

    <div class="mb-6">
        <div class="relative max-w-md">
            <i data-feather="search"
                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>

            <input type="text" id="searchSiswa" oninput="cariSiswa(this.value)" autocomplete="off"
                placeholder="Cari nama atau NIS siswa..."
                class="w-full pl-10 pr-9 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

            <button type="button" id="clearSearchBtn"
                onclick="document.getElementById('searchSiswa').value=''; cariSiswa('');"
                class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <i data-feather="x" class="w-4 h-4"></i>
            </button>
        </div>

        <p id="searchEmptyState" class="hidden text-sm text-slate-400 mt-3">
            Tidak ada siswa yang cocok dengan pencarian.
        </p>
    </div>

    @if(session('error_nonaktifkan_siswa'))
        @php $errSiswaData = session('error_nonaktifkan_siswa'); @endphp

        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-red-700 mb-2">
                {{ $errSiswaData['nama'] }} (Kelas {{ $errSiswaData['kelas'] }}) tidak bisa dinonaktifkan
            </p>

            <p class="text-xs text-red-600 mb-3">Masih ada kasus yang sedang berjalan:</p>

            <ul class="space-y-1">
                @foreach($errSiswaData['kasus'] as $k)
                    <li class="text-xs text-red-600 bg-red-100 rounded-lg px-3 py-1.5">
                        {{ $k['judul'] }}
                        <span class="ml-1 bg-red-200 text-red-700 px-1.5 py-0.5 rounded-full text-[10px] capitalize">
                            {{ $k['status'] }}
                        </span>
                    </li>
                @endforeach
            </ul>

            <p class="text-xs text-red-500 mt-2">Selesaikan atau arsipkan kasus tersebut terlebih dahulu.</p>
        </div>
    @endif

    @if(session('info_nonaktifkan_kelas'))
        @php $infoKelasData = session('info_nonaktifkan_kelas'); @endphp

        <div class="mb-5 bg-amber-50 border border-amber-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-amber-700 mb-2">
                Beberapa siswa kelas {{ $infoKelasData['kelas'] }} dilewati
            </p>

            <p class="text-xs text-amber-600 mb-3">
                Siswa berikut masih punya kasus yang sedang berjalan, jadi tetap aktif:
            </p>

            <ul class="space-y-1">
                @foreach($infoKelasData['dilewati'] as $d)
                    <li class="text-xs text-amber-700 bg-amber-100 rounded-lg px-3 py-1.5">
                        <strong>{{ $d['nama'] }}</strong>
                        @foreach($d['kasus'] as $k)
                            <span class="ml-1 bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded-full text-[10px] capitalize">
                                {{ $k['judul'] }} — {{ $k['status'] }}
                            </span>
                        @endforeach
                    </li>
                @endforeach
            </ul>

            <p class="text-xs text-amber-600 mt-2">
                Siswa lain di kelas ini yang tidak punya kasus berjalan sudah berhasil dinonaktifkan.
            </p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($aktifSekarang)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

            {{-- PERIODE --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Periode Aktif</p>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $aktifSekarang->tahun_ajaran }}</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $aktifSekarang->tanggal_mulai->format('d M Y') }} -
                            {{ $aktifSekarang->tanggal_selesai->format('d M Y') }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center">
                        <i data-feather="calendar" class="w-5 h-5 text-amber-600"></i>
                    </div>
                </div>
            </div>

            {{-- TOTAL SISWA --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Total Siswa</p>
                        <h3 class="mt-2 text-3xl font-bold text-blue-700">{{ $totalSiswaAktif }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i data-feather="users" class="w-5 h-5 text-blue-700"></i>
                    </div>
                </div>
            </div>

            {{-- SUDAH UPDATE --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Sudah Update</p>
                        <h3 class="mt-2 text-3xl font-bold text-green-600">{{ $totalSudahUpdate }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center">
                        <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- BELUM UPDATE --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Belum Update</p>
                        <h3 class="mt-2 text-3xl font-bold text-red-600">{{ $totalBelumUpdate }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center">
                        <i data-feather="alert-circle" class="w-5 h-5 text-red-600"></i>
                    </div>
                </div>
            </div>

        </div>
    @endif

    @forelse($siswaPerKelas as $kelas => $dataSiswa)
        @php
            $kelasId = 'kelas-' . Str::slug($kelas);
            $sisaCount = $dataSiswa->count() - $batasTampil;
        @endphp

        <section class="mb-8 bg-white border border-slate-200 rounded-3xl p-5 lg:p-6 shadow-sm overflow-hidden"
            data-kelas-section>

            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Kelas {{ $kelas }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $dataSiswa->count() }} siswa aktif</p>
                </div>

                <button type="button" onclick="openNonaktifkanKelasModal('{{ $kelas }}', {{ $dataSiswa->count() }})"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 transition shrink-0">
                    <i data-feather="user-x" class="w-3.5 h-3.5"></i>
                    Nonaktifkan Kelas
                </button>
            </div>

            <div id="{{ $kelasId }}" class="flex flex-row flex-wrap gap-y-3" data-expanded="false" data-container>
                @foreach($dataSiswa as $index => $item)
                    <div @if($index >= $batasTampil) data-extra-card @endif data-card data-nama="{{ strtolower($item->nama_siswa) }}"
                        data-nis="{{ strtolower($item->nis) }}"
                        class="group relative shrink-0 h-[330px] border-r-4 border-white bg-center overflow-hidden transition-all duration-500 ease-out cursor-pointer"
                        style="width: 58px; {{ $index >= $batasTampil ? 'display: none;' : '' }}"
                        onmouseenter="this.style.width='284px'" onmouseleave="this.style.width='58px'">
                        @if($item->foto)
                            <img src="{{ route('foto.siswa', $item->nis) }}" alt="{{ $item->nama_siswa }}"
                                class="absolute inset-0 w-full h-full object-cover"
                                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">

                            <div
                                class="hidden absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                                <span class="text-white/60 text-8xl font-extrabold">
                                    {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                </span>
                            </div>
                        @else
                            <div
                                class="absolute inset-0 bg-gradient-to-b from-blue-500 via-blue-700 to-blue-950 flex items-center justify-center">
                                <span class="text-white/60 text-8xl font-extrabold">
                                    {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-black/20 to-black/90"></div>

                        <p class="absolute left-4 bottom-4 max-w-[250px] text-white text-xs font-extrabold tracking-[2px] uppercase whitespace-nowrap transition-all duration-300 group-hover:opacity-0"
                            style="transform: rotate(-90deg); transform-origin: left bottom;">
                            {{ $item->nama_siswa }}
                        </p>

                        <div
                            class="absolute inset-0 p-5 flex flex-col justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.siswa.edit', $item->nis) }}" title="Edit Data"
                                    class="w-10 h-10 rounded-full bg-white/90 text-blue-700 flex items-center justify-center hover:bg-blue-700 hover:text-white transition">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </a>

                                <form action="{{ route('admin.siswa.destroy', $item->nis) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Nonaktifkan"
                                        onclick="return confirm('Nonaktifkan akun siswa ini?')"
                                        class="w-10 h-10 rounded-full bg-white/90 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition">
                                        <i data-feather="archive" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>

                            <div>
                                <div
                                    class="mb-3 inline-flex items-center px-2.5 py-1 rounded-full bg-green-400/20 text-green-200 border border-green-300/30 text-xs font-bold">
                                    Akun Aktif
                                </div>

                                <h3 class="text-white text-xl font-extrabold leading-tight">{{ $item->nama_siswa }}</h3>

                                <div class="mt-3 space-y-1.5">
                                    <p class="text-blue-100 text-sm">Kelas {{ $item->kelas }}</p>
                                    <p class="text-blue-100 text-sm">NIS {{ $item->nis }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($sisaCount > 0)
                <div class="text-center mt-2" data-toggle-btn>
                    <button type="button" onclick="toggleKelas('{{ $kelasId }}', this)"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                        <span data-label-more="Lihat Semua" data-label-less="Sembunyikan">Lihat Semua</span>
                        <i data-feather="chevron-down" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

        </section>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-14 text-center">
            <i data-feather="users" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-500 font-medium">Belum ada data siswa aktif.</p>
        </div>
    @endforelse

    <div id="downloadModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/40" onclick="closeDownloadModal()"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg p-6">

                <div class="mb-5 pb-5 border-b border-slate-100 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-slate-800">Download Data Siswa</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Pilih filter data siswa yang ingin didownload.</p>
                    </div>
                    <button type="button" onclick="closeDownloadModal()" class="text-slate-400 hover:text-slate-700">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form method="GET" action="{{ route('admin.siswa.download') }}" class="space-y-4">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelas</label>
                        <select name="kelas"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach($siswaPerKelas as $kelas => $dataSiswa)
                                <option value="{{ $kelas }}">Kelas {{ $kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Akun</label>
                        <select name="status"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <input type="hidden" name="format" value="pdf">

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeDownloadModal()"
                            class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-50">
                            Batal
                        </button>

                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition">
                            <i data-feather="download" class="w-4 h-4"></i>
                            Download
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div id="periodeModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/40" onclick="closePeriodeModal()"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6">

                <div class="mb-5 pb-5 border-b border-slate-100 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-slate-800">Periode Update Data</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Tentukan rentang waktu wajib update data siswa.</p>
                    </div>
                    <button type="button" onclick="closePeriodeModal()" class="text-slate-400 hover:text-slate-700">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                @if($aktifSekarang)
                    {{-- SUDAH ADA PERIODE --}}
                    <form method="POST"
                        action="{{ route('admin.periode-update.update', ['tahun_ajaran' => $aktifSekarang->tahun_ajaran]) }}"
                        class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun Ajaran</label>
                            <input type="text" value="{{ $aktifSekarang->tahun_ajaran }}" readonly
                                class="w-full rounded-xl border-slate-300 bg-slate-50 text-sm text-slate-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', $aktifSekarang->tanggal_mulai->format('Y-m-d')) }}" readonly
                                class="w-full rounded-xl border-slate-300 bg-slate-50 text-sm text-slate-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                value="{{ old('tanggal_selesai', $aktifSekarang->tanggal_selesai->format('Y-m-d')) }}" required
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="closePeriodeModal()"
                                class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-50">
                                Batal
                            </button>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition">
                                <i data-feather="save" class="w-4 h-4"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                @else
                    {{-- BELUM ADA PERIODE --}}
                    <form method="POST" action="{{ route('admin.periode-update.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran') }}"
                                placeholder="Contoh: 2026/2027" required
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="closePeriodeModal()"
                                class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-50">
                                Batal
                            </button>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition">
                                <i data-feather="save" class="w-4 h-4"></i>
                                Simpan Periode
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>

    <div id="nonaktifkanKelasModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/40" onclick="closeNonaktifkanKelasModal()"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6">

                <div class="flex items-start gap-4 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                        <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
                    </div>

                    <div>
                        <h2 class="font-bold text-slate-800">Menonaktifkan Semua Akun Kelas</h2>
                        <p class="text-sm text-slate-500 mt-0.5">
                            Kelas <strong id="nonaktifkanKelasNama" class="text-slate-700"></strong> —
                            <span id="nonaktifkanKelasJumlah"></span> akun siswa akan dinonaktifkan sekaligus.
                        </p>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5">
                    <p class="text-xs text-amber-700">
                        Siswa yang masih punya kasus BK sedang berjalan (status baru, pemanggilan, atau monitoring)
                        akan otomatis <strong>dilewati dan tetap aktif</strong>. Siswa lain di kelas ini yang tidak
                        punya kasus berjalan akan langsung dinonaktifkan.
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.siswa.nonaktifkan-kelas') }}">
                    @csrf
                    <input type="hidden" name="kelas" id="nonaktifkanKelasInput">

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeNonaktifkanKelasModal()"
                            class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-50">
                            Batal
                        </button>

                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition">
                            <i data-feather="user-x" class="w-4 h-4"></i>
                            Ya, Menonaktifkan Semua
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        function openPeriodeModal() {
            document.getElementById('periodeModal').classList.remove('hidden');
        }

        function closePeriodeModal() {
            document.getElementById('periodeModal').classList.add('hidden');
        }

        function openDownloadModal() {
            document.getElementById('downloadModal').classList.remove('hidden');
        }

        function closeDownloadModal() {
            document.getElementById('downloadModal').classList.add('hidden');
        }

        function toggleKelas(id, btn) {
            const container = document.getElementById(id);
            const extraCards = container.querySelectorAll('[data-extra-card]');
            const icon = btn.querySelector('i');
            const label = btn.querySelector('span');
            const isExpanded = container.dataset.expanded === 'true';

            if (isExpanded) {
                extraCards.forEach(card => card.style.display = 'none');
                container.dataset.expanded = 'false';
                label.textContent = label.dataset.labelMore;
                icon.setAttribute('data-feather', 'chevron-down');
            } else {
                extraCards.forEach(card => card.style.display = '');
                container.dataset.expanded = 'true';
                label.textContent = label.dataset.labelLess;
                icon.setAttribute('data-feather', 'chevron-up');
            }

            feather.replace();
        }

        function openNonaktifkanKelasModal(kelas, jumlah) {
            document.getElementById('nonaktifkanKelasInput').value = kelas;
            document.getElementById('nonaktifkanKelasNama').textContent = kelas;
            document.getElementById('nonaktifkanKelasJumlah').textContent = jumlah + ' siswa';
            document.getElementById('nonaktifkanKelasModal').classList.remove('hidden');
        }

        function closeNonaktifkanKelasModal() {
            document.getElementById('nonaktifkanKelasModal').classList.add('hidden');
        }

        function cariSiswa(rawQuery) {
            const query = rawQuery.trim().toLowerCase();
            const sections = document.querySelectorAll('[data-kelas-section]');
            const clearBtn = document.getElementById('clearSearchBtn');
            const emptyState = document.getElementById('searchEmptyState');

            clearBtn.classList.toggle('hidden', query === '');

            let adaHasilTotal = false;

            sections.forEach(section => {
                const cards = section.querySelectorAll('[data-card]');
                const container = section.querySelector('[data-container]');
                const toggleBtnWrapper = section.querySelector('[data-toggle-btn]');
                const isExpanded = container.dataset.expanded === 'true';

                let adaYangCocokDiKelasIni = false;

                cards.forEach(card => {
                    if (query === '') {
                        if (card.hasAttribute('data-extra-card')) {
                            card.style.display = isExpanded ? '' : 'none';
                        } else {
                            card.style.display = '';
                        }
                        return;
                    }

                    const cocok = card.dataset.nama.includes(query) || card.dataset.nis.includes(query);
                    card.style.display = cocok ? '' : 'none';

                    if (cocok) {
                        adaYangCocokDiKelasIni = true;
                    }
                });

                if (query === '') {
                    section.style.display = '';

                    if (toggleBtnWrapper) {
                        toggleBtnWrapper.style.display = '';
                    }
                } else {
                    section.style.display = adaYangCocokDiKelasIni ? '' : 'none';

                    if (toggleBtnWrapper) {
                        toggleBtnWrapper.style.display = 'none';
                    }

                    if (adaYangCocokDiKelasIni) {
                        adaHasilTotal = true;
                    }
                }
            });

            emptyState.classList.toggle('hidden', query === '' || adaHasilTotal);
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            @if($errors->any())
                openPeriodeModal();
            @endif
            });
    </script>

@endsection