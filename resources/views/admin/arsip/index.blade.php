@extends('layouts.admin')
@section('title', 'Arsip Siswa')
@section('page-title', 'Arsip Siswa')
@section('page-subtitle', 'Data siswa yang telah dinonaktifkan')

@section('content')

    @php
        $arsipPerKelas = $arsip->groupBy('kelas');
    @endphp

    <div class="mb-7">
        <h1 class="text-2xl font-bold text-slate-900">Arsip Siswa</h1>
        <p class="text-slate-500 mt-1 text-sm">Data siswa yang telah dinonaktifkan namun tetap tersimpan sebagai arsip
            sekolah.</p>
    </div>

    @forelse($arsipPerKelas as $kelas => $dataSiswa)

        <section class="mb-10">

            {{-- Header kelas --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1 h-7 bg-slate-400 rounded-full"></div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Kelas {{ $kelas }}</h2>
                    <p class="text-xs text-slate-500">{{ $dataSiswa->count() }} siswa diarsipkan</p>
                </div>
            </div>

            {{-- Rak Buku Arsip --}}
            <div class="flex overflow-x-auto gap-1 pb-2" style="min-height: 340px;">
                @foreach($dataSiswa as $item)
                    <div class="group relative flex-shrink-0 rounded-xl overflow-hidden transition-all duration-500 shadow-sm cursor-pointer"
                        style="width: 72px; height: 320px;" onmouseenter="this.style.width='300px'"
                        onmouseleave="this.style.width='72px'">

                        {{-- Background gelap untuk arsip --}}
                        <div class="absolute inset-0 bg-gradient-to-b from-slate-600 to-slate-900"></div>

                        @if($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}"
                                class="absolute inset-0 w-full h-full object-cover opacity-30">
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-800/50 to-transparent"></div>

                        {{-- Nama spine --}}
                        <div class="absolute inset-0 flex items-center justify-center group-hover:opacity-0 transition-opacity duration-300 pointer-events-none"
                            style="writing-mode: vertical-rl; transform: rotate(180deg);">
                            <span
                                class="text-white text-[11px] font-bold tracking-[3px] uppercase whitespace-nowrap overflow-hidden"
                                style="max-height: 260px;">
                                {{ $item->nama_siswa }}
                            </span>
                        </div>

                        {{-- Konten expanded --}}
                        <div
                            class="absolute inset-0 p-5 flex flex-col justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none group-hover:pointer-events-auto">
                            <div>
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center text-white text-2xl font-bold mb-3">
                                    {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                </div>
                                <h3 class="text-white text-base font-bold leading-tight">{{ $item->nama_siswa }}</h3>
                                <div class="mt-2 space-y-0.5">
                                    <p class="text-slate-300 text-xs">NIS: {{ $item->nis }}</p>
                                    <p class="text-slate-300 text-xs">Kelas: {{ $item->kelas }}</p>
                                    <p class="text-slate-300 text-xs">Orang Tua: {{ $item->nama_ortu }}</p>
                                </div>
                                <span
                                    class="mt-3 inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-red-400/20 text-red-300 border border-red-400/30">
                                    Arsip
                                </span>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('admin.arsip.show', $item->id) }}"
                                    class="block w-full bg-white text-slate-900 rounded-xl py-2.5 text-center text-sm font-bold hover:bg-slate-100 transition-colors">
                                    Lihat Arsip
                                </a>
                                <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Aktifkan kembali akun siswa ini?')"
                                        class="w-full bg-green-500/80 hover:bg-green-500 text-white rounded-xl py-2.5 text-sm font-bold transition-colors">
                                        Aktifkan Kembali
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Garis rak --}}
            <div class="h-2 rounded-full mt-1"
                style="background: linear-gradient(to right, #cbd5e1, #94a3b8, #cbd5e1); box-shadow: 0 2px 6px rgba(148,163,184,0.3);">
            </div>

        </section>

    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-14 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            <p class="text-slate-500 font-medium">Belum ada data siswa yang diarsipkan.</p>
        </div>
    @endforelse

@endsection