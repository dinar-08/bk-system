@extends('layouts.admin')

@section('title', 'Detail Arsip Siswa')
@section('page-title', 'Detail Arsip Siswa')
@section('page-subtitle', 'Riwayat lengkap penanganan BK')

@section('content')

    @php
        $statusClass = [
            'baru' => 'bg-amber-50 text-amber-700 border-amber-200',
            'pemanggilan' => 'bg-orange-50 text-orange-700 border-orange-200',
            'monitoring' => 'bg-blue-50 text-blue-700 border-blue-200',
            'selesai' => 'bg-green-50 text-green-700 border-green-200',
            'dirujuk' => 'bg-red-50 text-red-700 border-red-200',
        ];

        $kategoriClass = [
            'akademik' => 'bg-blue-50 text-blue-700 border-blue-200',
            'sosial' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'perilaku' => 'bg-amber-50 text-amber-700 border-amber-200',
            'emosional' => 'bg-rose-50 text-rose-700 border-rose-200',
            'lain-lain' => 'bg-slate-50 text-slate-600 border-slate-200',
        ];
    @endphp

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm mb-4">
        <a href="{{ route('admin.arsip.index') }}"
            class="inline-flex items-center gap-1 text-slate-400 hover:text-slate-600 font-medium">
            <i data-feather="chevron-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-slate-800 font-bold">Detail Arsip</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex justify-end">
            <form action="{{ route('admin.siswa.destroy', $siswa->nis) }}" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit" onclick="return confirm('Aktifkan kembali akun siswa ini?')"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    Aktifkan Kembali
                </button>
            </form>
        </div>

        {{-- Profil --}}
        <div class="p-5 sm:p-6">
            <div class="flex flex-col lg:flex-row gap-5 lg:items-center">
                <div class="w-24 h-24 rounded-3xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                    @if($siswa->foto)
                        <img src="{{ route('foto.siswa', $siswa->nis) }}" alt="{{ $siswa->nama_siswa }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-5xl font-extrabold text-slate-300">
                            {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <h1 class="text-2xl font-extrabold text-slate-900">
                            {{ $siswa->nama_siswa }}
                        </h1>

                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                            Arsip
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">
                        <div class="bg-slate-50 rounded-2xl px-4 py-3">
                            <p class="text-xs text-slate-400 font-semibold uppercase">NIS</p>
                            <p class="text-sm font-bold text-slate-800 mt-1">{{ $siswa->nis }}</p>
                        </div>

                        <div class="bg-slate-50 rounded-2xl px-4 py-3">
                            <p class="text-xs text-slate-400 font-semibold uppercase">Kelas Terakhir</p>
                            <p class="text-sm font-bold text-slate-800 mt-1">{{ $siswa->kelas }}</p>
                        </div>

                        <div class="bg-slate-50 rounded-2xl px-4 py-3">
                            <p class="text-xs text-slate-400 font-semibold uppercase">Tahun Ajaran</p>
                            <p class="text-sm font-bold text-slate-800 mt-1">{{ $siswa->tahun_ajaran ?? 'Data Lama' }}</p>
                        </div>

                        <div class="bg-slate-50 rounded-2xl px-4 py-3">
                            <p class="text-xs text-slate-400 font-semibold uppercase">Orang Tua / Wali</p>
                            <p class="text-sm font-bold text-slate-800 mt-1">{{ $siswa->nama_ortu ?? '-' }}</p>
                        </div>

                        <div class="bg-slate-50 rounded-2xl px-4 py-3">
                            <p class="text-xs text-slate-400 font-semibold uppercase">WhatsApp</p>
                            <p class="text-sm font-bold text-slate-800 mt-1">{{ $siswa->no_whatsapp ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Garis pemisah --}}
        <div class="border-t border-slate-100"></div>

        {{-- Riwayat --}}
        <div class="p-5 sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="w-1 h-6 bg-blue-700 rounded-full"></div>
                <h2 class="text-lg font-bold text-slate-900">Riwayat Penanganan BK</h2>
                <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">
                    {{ $siswa->laporan->count() }} laporan
                </span>
            </div>

            @forelse($siswa->laporan as $laporan)
                @php
                    $tanggalMulai = $laporan->created_at;
                    $tanggalSelesai = $laporan->evaluasi?->created_at ?? $laporan->evaluasi?->tanggal_evaluasi ?? null;
                    $detailId = 'detail-laporan-' . $laporan->laporan_id;
                    $status = $laporan->status ?? 'baru';
                    $kategori = $laporan->kategori ?? 'lain-lain';
                @endphp

                <div class="border-b border-red-100 last:border-b-0 py-6 first:pt-0 last:pb-0">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:items-center">

                        <div class="lg:col-span-2 flex lg:block items-center gap-3">
                            <p class="text-4xl font-extrabold text-red-600 leading-none">
                                {{ $tanggalMulai?->format('d') ?? '-' }}
                            </p>

                            <div>
                                <p class="text-sm font-semibold text-slate-700 uppercase">
                                    {{ $tanggalMulai?->format('M') ?? '-' }}
                                </p>
                                <p class="text-sm text-slate-500">
                                    {{ $tanggalMulai?->format('Y') ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="lg:col-span-7">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-bold {{ $kategoriClass[$kategori] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                    {{ ucfirst($kategori) }}
                                </span>

                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-bold {{ $statusClass[$status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>

                            <h3 class="text-lg font-extrabold text-slate-900 leading-snug">
                                {{ $laporan->judul_laporan }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ $tanggalMulai?->format('d M Y') ?? '-' }}
                                <span class="mx-1">-</span>
                                @if($tanggalSelesai)
                                    {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}
                                @else
                                    Belum selesai
                                @endif
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                Jenis masalah:
                                <span class="font-semibold text-slate-700">
                                    {{ $laporan->jenis_masalah ?? '-' }}
                                </span>
                            </p>
                        </div>

                        <div class="lg:col-span-3 lg:text-right">
                            <button type="button" onclick="toggleDetail('{{ $detailId }}', this)"
                                class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full border border-red-500 text-red-600 font-semibold text-sm hover:bg-red-600 hover:text-white transition">
                                <span>Lihat Detail</span>
                                <i data-feather="chevron-down" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div id="{{ $detailId }}" class="hidden mt-5 bg-slate-50 rounded-3xl border border-slate-100 p-5">
                        <div class="mb-5">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">
                                Deskripsi Permasalahan
                            </p>
                            <p class="text-sm text-slate-700 leading-relaxed bg-white border border-slate-100 rounded-2xl p-4">
                                {{ $laporan->deskripsi }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <i data-feather="calendar" class="w-4 h-4 text-blue-600"></i>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Pemanggilan</p>
                                </div>

                                @forelse($laporan->pemanggilan as $item)
                                    <div class="bg-white border border-slate-100 rounded-2xl p-4 mb-2">
                                        <p class="text-sm font-bold text-slate-700">
                                            {{ \Carbon\Carbon::parse($item->tanggal_pemanggilan)->format('d M Y') }}
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Kehadiran:
                                            <span class="font-semibold">{{ ucfirst($item->status_kehadiran) }}</span>
                                        </p>
                                    </div>
                                @empty
                                    <div class="bg-white border border-slate-100 rounded-2xl p-4">
                                        <p class="text-sm text-slate-400">Tidak ada data pemanggilan.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <i data-feather="activity" class="w-4 h-4 text-blue-600"></i>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Monitoring</p>
                                </div>

                                @forelse($laporan->monitoring as $item)
                                    <div class="bg-white border border-slate-100 rounded-2xl p-4 mb-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                                                Monitoring ke-{{ $item->monitoring_ke }}
                                            </span>

                                            <span class="text-xs text-slate-500">
                                                {{ ucfirst($item->status_perkembangan) }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-slate-700 leading-relaxed">
                                            {{ $item->catatan_perkembangan }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="bg-white border border-slate-100 rounded-2xl p-4">
                                        <p class="text-sm text-slate-400">Tidak ada data monitoring.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <i data-feather="check-circle" class="w-4 h-4 text-green-600"></i>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Evaluasi</p>
                                </div>

                                @if($laporan->evaluasi)
                                    <div class="bg-white border border-green-100 rounded-2xl p-4">
                                        <p class="text-xs font-bold text-green-700 mb-2">Hasil Evaluasi</p>
                                        <p class="text-sm text-slate-700 leading-relaxed">
                                            {{ $laporan->evaluasi->hasil_evaluasi }}
                                        </p>
                                    </div>
                                @else
                                    <div class="bg-white border border-slate-100 rounded-2xl p-4">
                                        <p class="text-sm text-slate-400">Belum ada evaluasi.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 sm:p-14 text-center">
                    <i data-feather="file-text" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                    <p class="text-slate-500 font-medium">
                        Tidak ada riwayat laporan pada siswa ini.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        function toggleDetail(id, button) {
            const detail = document.getElementById(id);
            const text = button.querySelector('span');

            detail.classList.toggle('hidden');

            if (detail.classList.contains('hidden')) {
                text.textContent = 'Lihat Detail';
                button.classList.remove('bg-red-600', 'text-white');
                button.classList.add('text-red-600');
            } else {
                text.textContent = 'Tutup Detail';
                button.classList.add('bg-red-600', 'text-white');
                button.classList.remove('text-red-600');
            }

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>

@endsection