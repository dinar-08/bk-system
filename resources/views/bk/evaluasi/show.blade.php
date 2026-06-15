@extends('layouts.bk')

@section('title', 'Evaluasi')

@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>

    <div class="max-w-6xl mx-auto">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-5 px-6 py-4 flex items-center gap-5">
            <a href="{{ route('bk.monitoring.show', $laporan->id) }}" class="text-slate-400 hover:text-slate-600">
                <i data-feather="arrow-left" class="w-6 h-6"></i>
            </a>

            <div class="h-7 w-px bg-slate-200"></div>

            <h1 class="text-base font-bold text-slate-800">
                Evaluasi
            </h1>
        </div>

        @if(session('success'))
            <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- CARD UTAMA --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">

                {{-- DATA SISWA --}}
                <div>
                    <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                        <h2 class="text-sm font-bold text-slate-700">Data Siswa</h2>
                    </div>

                    <div class="flex justify-center mb-6">
                        <div
                            class="w-44 h-36 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                            @if(optional($laporan->siswa)->foto)
                                <img src="{{ asset('storage/' . $laporan->siswa->foto) }}" class="w-full h-full object-cover"
                                    alt="Foto Siswa">
                            @else
                                <span class="text-xs font-bold text-slate-400">FOTO SISWA</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Nama</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->nama_siswa ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Kelas</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->kelas ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">NIS</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->nis ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Orang Tua</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ optional($laporan->siswa)->nama_ortu ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- DETAIL LAPORAN --}}
                <div>
                    <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                        <h2 class="text-sm font-bold text-slate-700">Detail Laporan</h2>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Judul</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ $laporan->judul_laporan ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Kategori</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ ucfirst($laporan->kategori ?? '-') }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <p class="text-sm text-slate-400 mb-1">Jenis Masalah</p>
                            <p class="text-base font-semibold text-slate-800">
                                {{ $laporan->jenis_masalah ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4 min-h-48">
                            <p class="text-sm text-slate-400 mb-2">Deskripsi</p>
                            <p class="text-base text-slate-800 leading-7">
                                {{ $laporan->deskripsi ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIWAYAT MONITORING --}}
            <div class="mt-8">
                <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                    <h2 class="text-sm font-bold text-slate-700">Riwayat Monitoring</h2>
                </div>

                <div class="space-y-4">
                    @forelse($laporan->monitoring->where('status_monitoring', 'selesai') as $item)
                        <div class="border border-slate-200 bg-slate-50 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">
                                        Monitoring ke-{{ $item->monitoring_ke }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $item->tanggal_monitoring }}
                                    </p>
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($item->status_perkembangan == 'membaik') bg-green-100 text-green-700
                                        @elseif($item->status_perkembangan == 'menurun') bg-red-100 text-red-700
                                        @else bg-blue-100 text-blue-700
                                        @endif">
                                    {{ ucfirst($item->status_perkembangan) }}
                                </span>
                            </div>

                            <p class="text-sm text-slate-600 leading-7">
                                {{ $item->catatan_perkembangan }}
                            </p>
                        </div>
                    @empty
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 text-center">
                            <p class="text-sm text-slate-400">
                                Belum ada riwayat monitoring.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- FORM EVALUASI --}}
            <div class="mt-8">
                <div class="bg-blue-50 rounded-xl py-3 text-center mb-5 border border-blue-100">
                    <h2 class="text-sm font-bold text-slate-700">Form Evaluasi</h2>
                </div>

                <form action="{{ route('bk.evaluasi.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <label class="block text-sm text-slate-400 mb-2">
                                Tanggal Evaluasi
                            </label>
                            <input type="date" name="tanggal_evaluasi"
                                class="w-full bg-transparent border-0 p-0 text-base font-semibold text-slate-800 focus:outline-none focus:ring-0">
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <label class="block text-sm text-slate-400 mb-2">
                                Status Akhir
                            </label>
                            <select name="status_akhir"
                                class="w-full bg-transparent border-0 p-0 text-base font-semibold text-slate-800 focus:outline-none focus:ring-0">
                                <option value="">Pilih status akhir</option>
                                <option value="selesai">Selesai</option>
                                <option value="dirujuk">Dirujuk</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
                            <label class="block text-sm text-slate-400 mb-2">
                                Hasil Evaluasi
                            </label>
                            <textarea name="hasil_evaluasi" rows="6"
                                placeholder="Tuliskan ringkasan hasil evaluasi berdasarkan monitoring..."
                                class="w-full bg-transparent border-0 p-0 text-base text-slate-800 placeholder-slate-400 resize-none focus:outline-none focus:ring-0"></textarea>
                        </div>

                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                            class="px-7 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800">
                            Simpan Evaluasi
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        feather.replace();
    </script>

@endsection