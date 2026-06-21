@extends('layouts.bk')

@section('title', 'Dashboard BK')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di panel Guru BK')

@section('content')

    @php
        $daftarLaporan = ($laporanTerbaru ?? collect())->where('status', 'baru');

        $genderLabels = ($genderChart ?? collect())->map(function ($item) {
            return $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        })->values();

        $genderData = ($genderChart ?? collect())->pluck('total')->values();

        $kelasLabels = ($kelasChart ?? collect())->pluck('kelas')->values();
        $kelasData = ($kelasChart ?? collect())->pluck('total')->values();

        $kategoriLabels = ($kategoriChart ?? collect())->pluck('kategori')->map(fn($item) => ucfirst($item))->values();
        $kategoriData = ($kategoriChart ?? collect())->pluck('total')->values();
    @endphp

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-7">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i data-feather="file-text" class="w-5 h-5 text-blue-700"></i>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Total</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $totalLaporan ?? 0 }}</p>
            <p class="text-xs text-slate-400 mt-1">Total Permasalahan Tercatat</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i data-feather="activity" class="w-5 h-5 text-amber-600"></i>
                </div>
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Aktif</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $monitoringAktif ?? 0 }}</p>
            <p class="text-xs text-slate-400 mt-1">Sedang Dalam Monitoring</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">Selesai</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $permasalahanSelesai ?? 0 }}</p>
            <p class="text-xs text-slate-400 mt-1">Permasalahan Selesai</p>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-7">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Jenis Kelamin Siswa</h3>
            <p class="text-xs text-slate-400 mb-4">Jumlah laporan berdasarkan jenis kelamin siswa.</p>
            <div class="h-64">
                <canvas id="genderChartCanvas"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Permasalahan Berdasarkan Kelas</h3>
            <p class="text-xs text-slate-400 mb-4">Kelas dengan jumlah laporan permasalahan terbanyak.</p>
            <div class="h-64">
                <canvas id="kelasChartCanvas"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 lg:col-span-2">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Kategori Permasalahan</h3>
            <p class="text-xs text-slate-400 mb-4">Jumlah laporan berdasarkan kategori permasalahan.</p>
            <div class="h-72">
                <canvas id="kategoriChartCanvas"></canvas>
            </div>
        </div>
    </div>

    {{-- Laporan Permasalahan Baru --}}
    @if($daftarLaporan->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                    <h3 class="font-semibold text-slate-800 text-sm">Laporan Permasalahan Baru</h3>
                </div>

                <a href="{{ route('bk.laporan.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                    Lihat semua →
                </a>
            </div>

            <div class="divide-y divide-slate-50">
                @foreach($daftarLaporan as $laporan)
                    <a href="{{ route('bk.laporan.show', $laporan->id) }}"
                        class="px-5 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">

                        <div class="w-20 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-blue-50 border border-slate-100">
                            @if(optional($laporan->siswa)->foto)
                                <img src="{{ asset('storage/' . $laporan->siswa->foto) }}" class="w-full h-full object-cover"
                                    alt="Foto Siswa">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50">
                                    <i data-feather="user" class="w-6 h-6 text-blue-300"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-slate-400">{{ optional($laporan->siswa)->kelas ?? '-' }}</p>
                            <p class="font-semibold text-slate-800 text-sm truncate">
                                {{ optional($laporan->siswa)->nama_siswa ?? '—' }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5 truncate">
                                {{ $laporan->judul_laporan ?? '-' }}
                            </p>
                        </div>

                        <span
                            class="text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full flex-shrink-0">
                            {{ ucfirst($laporan->kategori ?? '-') }}
                        </span>

                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 border border-yellow-200 text-yellow-700 flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                            Baru
                        </span>

                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                            <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <i data-feather="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-400 text-sm font-medium">Tidak ada laporan permasalahan baru masuk.</p>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const genderLabels = @json($genderLabels);
        const genderData = @json($genderData);

        const kelasLabels = @json($kelasLabels);
        const kelasData = @json($kelasData);

        const kategoriLabels = @json($kategoriLabels);
        const kategoriData = @json($kategoriData);

        new Chart(document.getElementById('genderChartCanvas'), {
            type: 'pie',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderData
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(document.getElementById('kelasChartCanvas'), {
            type: 'bar',
            data: {
                labels: kelasLabels,
                datasets: [{
                    label: 'Jumlah Permasalahan',
                    data: kelasData
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(document.getElementById('kategoriChartCanvas'), {
            type: 'bar',
            data: {
                labels: kategoriLabels,
                datasets: [{
                    label: 'Jumlah Permasalahan',
                    data: kategoriData
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>

@endsection