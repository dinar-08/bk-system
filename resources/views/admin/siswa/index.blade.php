@extends('layouts.admin')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Kelola data siswa dan akun orang tua berdasarkan kelas')

@section('content')

    @php
        $siswaAktif = $siswa->filter(fn($item) => ($item->user->status_akun ?? 'aktif') == 'aktif');
        $siswaPerKelas = $siswaAktif->groupBy('kelas');
    @endphp

    <div class="mb-7 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Siswa</h1>
            <p class="text-slate-500 mt-1 text-sm">Kelola data siswa dan akun orang tua berdasarkan kelas.</p>
        </div>
        <a href="{{ route('admin.siswa.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Siswa
        </a>
    </div>

    @forelse($siswaPerKelas as $kelas => $dataSiswa)

        <section class="mb-10">

            {{-- Header kelas --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-7 bg-blue-700 rounded-full"></div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Kelas {{ $kelas }}</h2>
                        <p class="text-xs text-slate-500">{{ $dataSiswa->count() }} siswa aktif</p>
                    </div>
                </div>
                <form action="{{ route('admin.siswa.nonaktifkan-kelas') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="kelas" value="{{ $kelas }}">
                    <button onclick="return confirm('Nonaktifkan semua akun kelas {{ $kelas }}?')"
                        class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-xl text-sm font-semibold hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Nonaktifkan Kelas
                    </button>
                </form>
            </div>

            {{-- Rak Buku Siswa --}}
            <div class="flex overflow-x-auto gap-1 pb-2" style="min-height: 340px;">
                @foreach($dataSiswa as $item)
                    <div class="group relative flex-shrink-0 rounded-xl overflow-hidden transition-all duration-500 shadow-sm cursor-pointer"
                        style="width: 72px; height: 320px;" onmouseenter="this.style.width='300px'"
                        onmouseleave="this.style.width='72px'">

                        {{-- Background --}}
                        <div class="absolute inset-0 bg-gradient-to-b from-blue-700 to-blue-900"></div>

                        @if($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}"
                                class="absolute inset-0 w-full h-full object-cover opacity-40">
                        @endif

                        {{-- Overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/90 via-blue-800/50 to-transparent"></div>

                        {{-- Nama spine (collapsed) --}}
                        <div class="absolute inset-0 flex items-center justify-center group-hover:opacity-0 transition-opacity duration-300 pointer-events-none"
                            style="writing-mode: vertical-rl; transform: rotate(180deg);">
                            <span
                                class="text-white text-[11px] font-bold tracking-[3px] uppercase whitespace-nowrap overflow-hidden"
                                style="max-height: 260px;">
                                {{ $item->nama_siswa }}
                            </span>
                        </div>

                        {{-- Konten (expanded) --}}
                        <div
                            class="absolute inset-0 p-5 flex flex-col justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none group-hover:pointer-events-auto">
                            <div>
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center text-white text-2xl font-bold mb-3">
                                    {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                </div>
                                <h3 class="text-white text-base font-bold leading-tight">{{ $item->nama_siswa }}</h3>
                                <div class="mt-2 space-y-0.5">
                                    <p class="text-blue-100 text-xs">NIS: {{ $item->nis }}</p>
                                    <p class="text-blue-100 text-xs">Kelas: {{ $item->kelas }}</p>
                                    <p class="text-blue-100 text-xs">Orang Tua: {{ $item->nama_ortu }}</p>
                                </div>
                                <span
                                    class="mt-3 inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-green-400/20 text-green-300 border border-green-400/30">
                                    Akun Aktif
                                </span>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('admin.siswa.edit', $item->id) }}"
                                    class="block w-full bg-white text-blue-900 rounded-xl py-2.5 text-center text-sm font-bold hover:bg-blue-50 transition-colors">
                                    Edit Data
                                </a>
                                <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Nonaktifkan akun siswa ini?')"
                                        class="w-full bg-red-500/80 hover:bg-red-500 text-white rounded-xl py-2.5 text-sm font-bold transition-colors">
                                        Nonaktifkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Garis rak --}}
            <div class="h-2 rounded-full mt-1"
                style="background: linear-gradient(to right, #bfdbfe, #3b82f6, #bfdbfe); box-shadow: 0 2px 6px rgba(59,130,246,0.2);">
            </div>

        </section>

    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-14 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p class="text-slate-500 font-medium">Belum ada data siswa aktif.</p>
        </div>
    @endforelse

@endsection