@extends('layouts.admin')

@section('title', 'Periode Update Data')
@section('page-title', 'Periode Update Data')
@section('page-subtitle', 'Atur periode wajib update data siswa')

@section('content')

    @php
        $totalSudah = collect($rekapSiswa ?? [])->where('sudah_update', true)->count();
        $totalBelum = collect($rekapSiswa ?? [])->where('sudah_update', false)->count();
        $totalRekap = collect($rekapSiswa ?? [])->count();
    @endphp

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.siswa.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Periode Update Data</span>
    </div>

    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
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

    <div class="mb-5 flex justify-end">
        <button type="button" onclick="openPeriodeModal()"
            class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition">
            <i data-feather="calendar-plus" class="w-4 h-4"></i>
            Buat Periode
        </button>
    </div>

    @if($aktifSekarang)
        <div class="mb-5 bg-amber-50 border border-amber-200 rounded-2xl p-5">
            <p class="text-xs font-bold text-amber-700 uppercase tracking-wide">Periode Aktif Sekarang</p>
            <h3 class="text-lg font-bold text-amber-900 mt-1">{{ $aktifSekarang->tahun_ajaran }}</h3>
            <p class="text-sm text-amber-700 mt-1">
                {{ \Carbon\Carbon::parse($aktifSekarang->tanggal_mulai)->format('d M Y') }}
                –
                {{ \Carbon\Carbon::parse($aktifSekarang->tanggal_selesai)->format('d M Y') }}
            </p>
            <p class="text-xs text-amber-600 mt-3">
                Orang tua wajib memperbarui data selama periode ini aktif.
            </p>
        </div>
    @endif

    <div class="space-y-5">

        @if($aktifSekarang && $totalRekap > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Siswa</p>
                    <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ $totalRekap }}</h3>
                </div>

                <div class="bg-white rounded-2xl border border-green-200 p-5">
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Sudah Update</p>
                    <h3 class="text-3xl font-bold text-green-700 mt-2">{{ $totalSudah }}</h3>
                </div>

                <div class="bg-white rounded-2xl border border-red-200 p-5">
                    <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Belum Update</p>
                    <h3 class="text-3xl font-bold text-red-700 mt-2">{{ $totalBelum }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="mb-5 pb-5 border-b border-slate-100">
                    <h2 class="font-bold text-slate-800">Status Update Siswa</h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Rekap siswa yang sudah dan belum memperbarui data.
                    </p>
                </div>

                <div class="overflow-x-auto max-h-72 overflow-y-auto rounded-xl border border-slate-100">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kelas</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Update Terakhir</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach($rekapSiswa as $s)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $s['nama'] }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $s['kelas'] ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($s['sudah_update'])
                                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                <i data-feather="check-circle" class="w-3 h-3"></i>
                                                Sudah
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 border border-red-100 px-2.5 py-1 rounded-full text-xs font-semibold">
                                                <i data-feather="x-circle" class="w-3 h-3"></i>
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-400">{{ $s['update_at'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="mb-5 pb-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-slate-800">Riwayat Periode</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Daftar periode update data yang pernah dibuat.</p>
                </div>
            </div>

            @forelse($periode as $p)
                @php
                    $isAktif = $p->aktif && $p->tanggal_mulai <= now()->toDateString() && $p->tanggal_selesai >= now()->toDateString();
                    $isSelesai = $p->tanggal_selesai < now()->toDateString();
                @endphp

                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 py-4 border-b border-slate-100 last:border-0">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-bold text-slate-800">{{ $p->tahun_ajaran }}</p>

                            @if($isAktif)
                                <span class="bg-green-50 text-green-700 border border-green-100 text-xs px-2 py-0.5 rounded-full font-semibold">
                                    Aktif
                                </span>
                            @elseif($isSelesai)
                                <span class="bg-slate-100 text-slate-500 text-xs px-2 py-0.5 rounded-full font-semibold">
                                    Selesai
                                </span>
                            @else
                                <span class="bg-blue-50 text-blue-700 border border-blue-100 text-xs px-2 py-0.5 rounded-full font-semibold">
                                    Mendatang
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-500 mt-1">
                            {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}
                            –
                            {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        <form method="POST" action="{{ route('admin.periode-update.update', $p->id) }}" class="flex gap-2">
                            @csrf
                            @method('PATCH')

                            <input type="date" name="tanggal_selesai" value="{{ $p->tanggal_selesai }}"
                                class="border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-400 focus:outline-none">

                            <button type="submit"
                                class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-100 text-xs px-3 py-2 rounded-xl font-semibold transition">
                                <i data-feather="edit-3" class="w-3 h-3"></i>
                                Perpanjang
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.periode-update.destroy', $p->id) }}">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                onclick="return confirm('Hapus periode ini?')"
                                class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 text-xs px-3 py-2 rounded-xl font-semibold transition">
                                <i data-feather="trash-2" class="w-3 h-3"></i>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center">
                    <i data-feather="calendar" class="w-9 h-9 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-400">Belum ada periode yang dibuat.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Buat Periode --}}
    <div id="periodeModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/40" onclick="closePeriodeModal()"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6">
                <div class="mb-5 pb-5 border-b border-slate-100 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-slate-800">Buat Periode Baru</h2>
                        <p class="text-sm text-slate-500 mt-0.5">
                            Tentukan rentang waktu wajib update data siswa.
                        </p>
                    </div>

                    <button type="button" onclick="closePeriodeModal()" class="text-slate-400 hover:text-slate-700">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

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