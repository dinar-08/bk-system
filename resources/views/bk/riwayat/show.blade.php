@extends('layouts.bk')

@section('title', 'Riwayat Siswa')
@section('page-title', 'Riwayat Permasalahan Siswa')
@section('page-subtitle', 'Rekam jejak permasalahan dari kelas 1 hingga saat ini')

@section('content')

    {{-- BREADCRUMB --}}
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('bk.riwayat.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300 hidden sm:inline">/</span>
        <span class="text-sm text-slate-800 font-semibold hidden sm:inline">Riwayat {{ $siswa->nama_siswa }}</span>

        @if($daftarLaporan->count() > 0)
            <a href="{{ route('bk.riwayat.downloadSiswa', $siswa->nis) }}"
                class="ml-auto inline-flex items-center justify-center w-10 h-10 bg-blue-700 text-white rounded-xl hover:bg-blue-800 transition-colors"
                title="Download Semua Permasalahan">
                <i data-feather="download" class="w-4 h-4"></i>
            </a>
        @endif
    </div>

    {{-- SEMUANYA JADI 1 CARD BESAR: Data Siswa + daftar permasalahan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">

        {{-- IDENTITAS SISWA — SELALU KELIATAN, GAK DI-HIDE --}}
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between gap-2 mb-4">
                <div class="flex items-center gap-2">
                    <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">Data Siswa</h2>
                </div>
                <span class="text-xs text-slate-400">{{ $daftarLaporan->count() }} laporan tercatat</span>
            </div>

            {{-- FOTO (pojok kiri) + SEMUA INFO (di samping foto) --}}
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-5">
                <div class="w-32 h-32 sm:w-36 sm:h-36 shrink-0 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                    @if($siswa->foto)
                        <img src="{{ route('foto.siswa', $siswa->nis) }}" class="w-full h-full object-cover"
                            alt="{{ $siswa->nama_siswa }}">
                    @else
                        <div class="text-center">
                            <i data-feather="user" class="w-8 h-8 text-slate-300 mx-auto mb-1"></i>
                            <p class="text-xs text-slate-400">Foto Siswa</p>
                        </div>
                    @endif
                </div>

                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3 content-start">
                    @foreach([
                        ['Nama',        $siswa->nama_siswa],
                        ['NIS',         $siswa->nis],
                        ['Kelas',       $siswa->kelas],
                        ['Orang Tua',   $siswa->nama_ortu],
                        ['No WhatsApp', $siswa->no_whatsapp],
                        ['Alamat',      $siswa->alamat],
                    ] as [$label, $val])
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                            <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                            <p class="text-sm font-semibold text-slate-700 break-words">{{ $val ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- DAFTAR PERMASALAHAN (TERBARU DI ATAS), KLIK BUAT LIHAT DETAIL --}}
        @forelse($daftarLaporan as $item)
            @php
                $statusAkhir = optional($item->evaluasi)->status_akhir ?? $item->status;
                $statusClass = $statusAkhir === 'selesai'
                    ? 'bg-green-100 text-green-700 border-green-200'
                    : 'bg-red-100 text-red-700 border-red-200';

                $tahunAjaran = $item->tahun_ajaran ?? 'Tahun ajaran tidak diketahui';
                $kelasSaatItu = $item->kelas ?? optional($siswa)->kelas ?? '-';
            @endphp

            <details class="group">
                <summary
                    class="list-none cursor-pointer p-4 sm:p-5 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-blue-700 mb-1">
                            {{ $tahunAjaran }} &bull; Kelas {{ $kelasSaatItu }}
                        </p>
                        <p class="text-sm font-bold text-slate-800 truncate">
                            {{ $item->judul_laporan ?? $item->jenis_masalah ?? 'Permasalahan' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $statusAkhir)) }}
                        </span>
                        <a href="{{ route('bk.download.kasus', $item->laporan_id) }}"
                            class="text-slate-400 hover:text-blue-600" title="Download PDF laporan ini"
                            onclick="event.stopPropagation()">
                            <i data-feather="download" class="w-4 h-4"></i>
                        </a>
                        <i data-feather="chevron-down"
                            class="w-4 h-4 text-slate-400 transition-transform duration-200 group-open:rotate-180"></i>
                    </div>
                </summary>


                <div class="border-t border-slate-100 divide-y divide-slate-100">

                    {{-- DETAIL LAPORAN --}}
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <i data-feather="file-text" class="w-4 h-4 text-blue-600"></i>
                            <h3 class="text-sm font-bold text-slate-700">Detail Laporan</h3>
                        </div>

                        <div class="space-y-2.5">
                            @foreach([
                                ['Judul Laporan', $item->judul_laporan],
                                ['Kategori',      ucfirst($item->kategori ?? '-')],
                                ['Jenis Masalah', $item->jenis_masalah],
                                ['Guru BK',       optional($item->guruBk)->nama ?? 'Belum ditangani'],
                            ] as [$label, $val])
                                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                                    <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                                    <p class="text-sm font-semibold text-slate-700 break-words">{{ $val ?? '-' }}</p>
                                </div>
                            @endforeach

                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                                <p class="text-xs text-slate-400 mb-1">Status Akhir</p>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $statusAkhir)) }}
                                </span>
                            </div>

                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 min-h-[100px]">
                                <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                                <p class="text-sm text-slate-700 leading-relaxed break-words">{{ $item->deskripsi ?? '-' }}</p>

                                @if($item->bukti)
                                    <a href="{{ route('bukti.show', $item->laporan_id) }}" target="_blank"
                                        class="inline-flex items-center justify-center gap-2 mt-4 px-4 py-2 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 w-full sm:w-auto">
                                        <i data-feather="paperclip" class="w-4 h-4"></i>
                                        Lihat Bukti
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RIWAYAT PEMANGGILAN --}}
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center gap-2 mb-5">
                            <i data-feather="phone" class="w-4 h-4 text-blue-600"></i>
                            <h3 class="text-sm font-bold text-slate-700">Riwayat Pemanggilan</h3>
                        </div>

                        @forelse($item->pemanggilan as $p)
                            <div class="relative flex gap-3 sm:gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
                                <div class="flex flex-col items-center shrink-0">
                                    <div class="w-3 h-3 rounded-full bg-blue-500 border-2 border-white ring-2 ring-blue-200 mt-1 shrink-0"></div>
                                    @if(!$loop->last)
                                        <div class="w-px flex-1 bg-slate-200 mt-1"></div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0 border border-slate-200 bg-slate-50 rounded-xl p-3 sm:p-4">
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                                        <div>
                                            <p class="text-xs text-slate-400 mb-0.5">Tanggal</p>
                                            <p class="text-sm font-semibold text-slate-700 break-words">{{ $p->tanggal_pemanggilan }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400 mb-0.5">Waktu</p>
                                            <p class="text-sm font-semibold text-slate-700 break-words">{{ $p->waktu_pemanggilan }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400 mb-0.5">Pihak</p>
                                            <p class="text-sm font-semibold text-slate-700 break-words">
                                                {{ str_replace('_', ' ', ucfirst($p->pihak_dipanggil)) }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400 mb-0.5">Kehadiran</p>
                                            <p class="text-sm font-semibold text-slate-700 break-words">
                                                {{ str_replace('_', ' ', ucfirst($p->status_kehadiran)) }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($p->tujuan)
                                        <p class="text-sm text-slate-600 leading-relaxed break-words">{{ $p->tujuan }}</p>
                                    @endif

                                    @if($p->catatan)
                                        <p class="text-sm text-slate-600 leading-relaxed mt-2 break-words">
                                            <span class="font-semibold">Catatan:</span> {{ $p->catatan }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 text-center">
                                <i data-feather="calendar" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-400">Belum ada riwayat pemanggilan.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- RIWAYAT MONITORING --}}
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center gap-2 mb-5">
                            <i data-feather="activity" class="w-4 h-4 text-blue-600"></i>
                            <h3 class="text-sm font-bold text-slate-700">Riwayat Monitoring</h3>
                        </div>

                        @forelse($item->monitoring->sortBy('monitoring_ke') as $m)
                            <div class="relative flex gap-3 sm:gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
                                <div class="flex flex-col items-center shrink-0">
                                    <div class="w-3 h-3 rounded-full mt-1 shrink-0 border-2 border-white ring-2
                                        @if($m->status_perkembangan === 'membaik') bg-green-500 ring-green-200
                                        @elseif($m->status_perkembangan === 'menurun') bg-red-500 ring-red-200
                                        @else bg-blue-500 ring-blue-200
                                        @endif">
                                    </div>
                                    @if(!$loop->last)
                                        <div class="w-px flex-1 bg-slate-200 mt-1"></div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0 border border-slate-200 bg-slate-50 rounded-xl p-3 sm:p-4">
                                    <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                        <div>
                                            <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                                                Monitoring ke-{{ $m->monitoring_ke }}
                                            </span>
                                            @if($m->tanggal_monitoring)
                                                <p class="text-xs text-slate-400 mt-1.5">
                                                    {{ \Carbon\Carbon::parse($m->tanggal_monitoring)->translatedFormat('d F Y') }}
                                                </p>
                                            @endif
                                        </div>

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($m->status_perkembangan === 'membaik') bg-green-100 text-green-700
                                            @elseif($m->status_perkembangan === 'menurun') bg-red-100 text-red-700
                                            @else bg-blue-100 text-blue-700
                                            @endif">
                                            {{ ucfirst($m->status_perkembangan ?? '-') }}
                                        </span>
                                    </div>

                                    <p class="text-sm text-slate-600 leading-relaxed break-words">
                                        {{ $m->catatan_perkembangan ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 text-center">
                                <i data-feather="clock" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-400">Belum ada riwayat monitoring.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- EVALUASI AKHIR --}}
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center gap-2 mb-5">
                            <i data-feather="check-square" class="w-4 h-4 text-blue-600"></i>
                            <h3 class="text-sm font-bold text-slate-700">Evaluasi Akhir</h3>
                        </div>

                        @if($item->evaluasi)
                            <div class="space-y-3">
                                @if($item->evaluasi->tanggal_evaluasi)
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                                        <p class="text-xs text-slate-400 mb-0.5">Tanggal Evaluasi</p>
                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($item->evaluasi->tanggal_evaluasi)->translatedFormat('d F Y') }}
                                        </p>
                                    </div>
                                @endif

                                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 min-h-[100px]">
                                    <p class="text-xs text-slate-400 mb-1">Hasil Evaluasi</p>
                                    <p class="text-sm text-slate-700 leading-relaxed break-words">
                                        {{ $item->evaluasi->hasil_evaluasi ?? '-' }}
                                    </p>
                                </div>

                                <div class="bg-slate-50 border border-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3">
                                    <p class="text-xs text-slate-400 mb-1">Status Akhir</p>
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->evaluasi->status_akhir)) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 text-center">
                                <i data-feather="clipboard" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-400">Belum ada data evaluasi.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </details>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-10 sm:p-14 text-center">
                <i data-feather="archive" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-500 font-medium">Belum ada riwayat permasalahan untuk siswa ini.</p>
            </div>
        @endforelse
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>

@endsection
