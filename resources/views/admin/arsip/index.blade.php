@extends('layouts.admin')

@section('title', 'Arsip Siswa')
@section('page-title', 'Arsip Siswa')
@section('page-subtitle', 'Data siswa yang telah dinonaktifkan')

@section('content')

    @php
        $arsipPerTahun = $arsip
            ->sortBy('nama_siswa')
            ->groupBy(function ($item) {
                return $item->tahun_ajaran ?: 'Data Lama';
            });
    @endphp

    <div class="mb-7">
        <h1 class="text-2xl font-bold text-slate-900">Arsip Siswa</h1>
        <p class="text-slate-500 mt-1 text-sm">
            Data siswa yang telah dinonaktifkan.
        </p>
    </div>

    @forelse($arsipPerTahun as $tahunAjaran => $dataSiswa)
        <section class="mb-8 bg-white border border-slate-200 rounded-3xl p-4 sm:p-5 lg:p-6 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">
                        {{ $tahunAjaran }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ $dataSiswa->count() }} siswa diarsipkan
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center">
                    <i data-feather="archive" class="w-5 h-5 text-slate-500"></i>
                </div>
            </div>

            {{-- Mobile --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:hidden">
                @foreach($dataSiswa as $item)
                    <div class="relative rounded-2xl overflow-hidden min-h-[230px] bg-slate-800 shadow-sm">
                        @if($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_siswa }}"
                                class="absolute inset-0 w-full h-full object-cover opacity-70">
                        @else
                            <div
                                class="absolute inset-0 bg-gradient-to-b from-slate-500 via-slate-700 to-slate-950 flex items-center justify-center">
                                <span class="text-white/20 text-8xl font-extrabold">
                                    {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/35 to-transparent"></div>

                        <div class="relative z-10 p-4 min-h-[230px] flex flex-col justify-between">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.arsip.show', $item->id) }}" title="Lihat Arsip"
                                    class="w-9 h-9 rounded-full bg-white/90 text-slate-700 flex items-center justify-center hover:bg-slate-800 hover:text-white transition">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>

                                <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" title="Aktifkan Kembali"
                                        onclick="return confirm('Aktifkan kembali akun siswa ini?')"
                                        class="w-9 h-9 rounded-full bg-white/90 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition">
                                        <i data-feather="refresh-cw" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>

                            <div>
                                <span
                                    class="mb-2 inline-flex items-center px-2.5 py-1 rounded-full bg-red-400/20 text-red-200 border border-red-300/30 text-xs font-bold">
                                    Arsip
                                </span>

                                <h3 class="text-white text-lg font-extrabold leading-tight">
                                    {{ $item->nama_siswa }}
                                </h3>

                                <p class="text-slate-200 text-sm mt-1">Kelas {{ $item->kelas }}</p>
                                <p class="text-slate-200 text-sm">NIS {{ $item->nis }}</p>
                                <p class="text-slate-300 text-xs mt-1">
                                    Tahun Ajaran {{ $item->tahun_ajaran ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop --}}
            <div class="hidden lg:flex flex-row flex-nowrap overflow-x-auto pb-3">
                @foreach($dataSiswa as $item)
                    <div class="group relative shrink-0 h-[330px] border-r-4 border-white bg-center overflow-hidden transition-all duration-500 ease-out cursor-pointer"
                        style="width: 58px;" onmouseenter="this.style.width='284px'" onmouseleave="this.style.width='58px'">

                        @if($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_siswa }}"
                                class="absolute inset-0 w-full h-full object-cover opacity-70">
                        @else
                            <div
                                class="absolute inset-0 bg-gradient-to-b from-slate-500 via-slate-700 to-slate-950 flex items-center justify-center">
                                <span class="text-white/20 text-8xl font-extrabold">
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
                                <a href="{{ route('admin.arsip.show', $item->id) }}" title="Lihat Arsip"
                                    class="w-10 h-10 rounded-full bg-white/90 text-slate-700 flex items-center justify-center hover:bg-slate-800 hover:text-white transition">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>

                                <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" title="Aktifkan Kembali"
                                        onclick="return confirm('Aktifkan kembali akun siswa ini?')"
                                        class="w-10 h-10 rounded-full bg-white/90 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition">
                                        <i data-feather="refresh-cw" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>

                            <div>
                                <div
                                    class="mb-3 inline-flex items-center px-2.5 py-1 rounded-full bg-red-400/20 text-red-200 border border-red-300/30 text-xs font-bold">
                                    Arsip
                                </div>

                                <h3 class="text-white text-xl font-extrabold leading-tight">
                                    {{ $item->nama_siswa }}
                                </h3>

                                <div class="mt-3 space-y-1.5">
                                    <p class="text-slate-200 text-sm">Kelas {{ $item->kelas }}</p>
                                    <p class="text-slate-200 text-sm">NIS {{ $item->nis }}</p>
                                    <p class="text-slate-300 text-xs">
                                        Tahun Ajaran {{ $item->tahun_ajaran ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-10 sm:p-14 text-center">
            <i data-feather="archive" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-500 font-medium">Belum ada data siswa yang diarsipkan.</p>
        </div>
    @endforelse

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>

@endsection