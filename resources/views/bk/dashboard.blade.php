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
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5 mb-6 sm:mb-7">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i data-feather="file-text" class="w-4 h-4 sm:w-5 sm:h-5 text-blue-700"></i>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Total</span>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ $totalLaporan ?? 0 }}</p>
            <p class="text-xs text-slate-400 mt-1">Total Permasalahan Tercatat</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i data-feather="activity" class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600"></i>
                </div>
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Aktif</span>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ $monitoringAktif ?? 0 }}</p>
            <p class="text-xs text-slate-400 mt-1">Sedang Dalam Monitoring</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 sm:col-span-2 md:col-span-1">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <i data-feather="check-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600"></i>
                </div>
                <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">Selesai</span>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ $permasalahanSelesai ?? 0 }}</p>
            <p class="text-xs text-slate-400 mt-1">Permasalahan Selesai</p>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 mb-6 sm:mb-7">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Jenis Kelamin Siswa</h3>
            <p class="text-xs text-slate-400 mb-4">Jumlah laporan berdasarkan jenis kelamin siswa.</p>
            <div class="h-52 sm:h-64">
                <canvas id="genderChartCanvas"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Permasalahan Berdasarkan Kelas</h3>
            <p class="text-xs text-slate-400 mb-4">Kelas dengan jumlah laporan permasalahan terbanyak.</p>
            {{-- Wrapper luar yang boleh di-scroll horizontal di HP --}}
            <div class="h-52 sm:h-64 overflow-x-auto">
                {{-- Wrapper dalam: lebarnya diatur oleh JS sesuai jumlah kelas, supaya batang tidak gepeng --}}
                <div id="kelasChartWrapper" class="h-full">
                    <canvas id="kelasChartCanvas"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 lg:col-span-2">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Kategori Permasalahan</h3>
            <p class="text-xs text-slate-400 mb-4">Jumlah laporan berdasarkan kategori permasalahan.</p>
            <div class="h-56 sm:h-72 overflow-x-auto">
                <div id="kategoriChartWrapper" class="h-full">
                    <canvas id="kategoriChartCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Laporan Permasalahan Baru --}}
    @if($daftarLaporan->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                    <h3 class="font-semibold text-slate-800 text-sm">Laporan Permasalahan Baru</h3>
                </div>

                <a href="{{ route('bk.laporan.index') }}"
                    class="text-xs text-blue-600 hover:text-blue-700 font-medium whitespace-nowrap">
                    Lihat semua →
                </a>
            </div>

            <div class="divide-y divide-slate-50">
                @foreach($daftarLaporan as $laporan)
                    <a href="{{ route('bk.laporan.show', $laporan->id) }}"
                        class="px-4 sm:px-5 py-4 flex flex-wrap sm:flex-nowrap items-center gap-3 sm:gap-4 hover:bg-slate-50 transition-colors">

                        {{-- Foto + Nama: selalu satu baris penuh di mobile --}}
                        <div class="flex items-center gap-3 w-full sm:w-auto sm:flex-1 min-w-0">
                            <div
                                class="w-16 h-12 sm:w-20 sm:h-14 rounded-xl overflow-hidden flex-shrink-0 bg-blue-50 border border-slate-100">
                                @if(optional($laporan->siswa)->foto)
                                    <img src="{{ route('foto.siswa', $laporan->siswa->id) }}" class="w-full h-full object-cover"
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

                            {{-- Panah: sembunyikan di mobile supaya tidak sempit, tampil lagi di sm ke atas --}}
                            <div
                                class="hidden sm:flex flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 items-center justify-center text-blue-600">
                                <i data-feather="arrow-right" class="w-4 h-4"></i>
                            </div>
                        </div>

                        {{-- Badge kategori & status: baris sendiri di mobile, full width & rata kanan-kiri --}}
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                            <span
                                class="text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full flex-shrink-0 truncate max-w-[55%] sm:max-w-none">
                                {{ ucfirst($laporan->kategori ?? '-') }}
                            </span>

                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 border border-yellow-200 text-yellow-700 flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                                Baru
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 text-center">
            <i data-feather="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-400 text-sm font-medium">Tidak ada laporan permasalahan baru masuk.</p>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        const genderLabels = @json($genderLabels);
        const genderData = @json($genderData);

        const kelasLabels = @json($kelasLabels);
        const kelasData = @json($kelasData);

        const kategoriLabels = @json($kategoriLabels);
        const kategoriData = @json($kategoriData);

        // Menghitung step sumbu Y yang "rapi" (1, 2, 5, 10, 20, 50, 100, dst)
        // menyesuaikan otomatis dengan nilai data terbesar, supaya tidak muncul 0.2 / 0.4.
        function hitungStepRapi(nilaiMax) {
            if (!nilaiMax || nilaiMax <= 0) return 1;

            const kasar = nilaiMax / 5;
            const magnitude = Math.pow(10, Math.floor(Math.log10(kasar)));
            const sisa = kasar / magnitude;

            let stepRapi;
            if (sisa > 5) stepRapi = 10;
            else if (sisa > 2) stepRapi = 5;
            else if (sisa > 1) stepRapi = 2;
            else stepRapi = 1;

            return stepRapi * magnitude;
        }

        // Deteksi layar kecil supaya ukuran font chart ikut menyesuaikan.
        const isMobile = window.innerWidth < 640;

        // Atur lebar minimum wrapper chart batang berdasarkan jumlah label,
        // supaya di HP batangnya tidak gepeng dan bisa di-scroll horizontal
        // kalau labelnya banyak. Di layar lebar, tetap mengikuti 100% container.
        function aturLebarWrapper(wrapperEl, jumlahLabel, lebarPerLabelMobile) {
            if (!wrapperEl) return;
            const lebarDibutuhkan = jumlahLabel * lebarPerLabelMobile;
            const lebarContainer = wrapperEl.parentElement.clientWidth;
            wrapperEl.style.width = Math.max(lebarDibutuhkan, lebarContainer) + 'px';
        }

        const kelasWrapper = document.getElementById('kelasChartWrapper');
        const kategoriWrapper = document.getElementById('kategoriChartWrapper');

        if (isMobile) {
            aturLebarWrapper(kelasWrapper, kelasLabels.length, 56);
            aturLebarWrapper(kategoriWrapper, kategoriLabels.length, 70);
        }

        // Opsi bawaan untuk semua chart: legend & tooltip dimatikan.
        // datalabels dimatikan di sini (default), lalu diaktifkan khusus untuk pie chart saja.
        const opsiUmum = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
                datalabels: { display: false }
            }
        };

        new Chart(document.getElementById('genderChartCanvas'), {
            type: 'pie',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderData
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                ...opsiUmum,
                plugins: {
                    ...opsiUmum.plugins,
                    datalabels: {
                        color: '#000',
                        font: { weight: 'bold', size: isMobile ? 11 : 13 },
                        formatter: function (value, context) {
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            if (!total) return '0%';
                            const persen = (value / total) * 100;
                            return persen.toFixed(1) + '%';
                        }
                    }
                }
            }
        });

        const kelasStep = hitungStepRapi(Math.max(...kelasData, 0));
        new Chart(document.getElementById('kelasChartCanvas'), {
            type: 'bar',
            data: {
                labels: kelasLabels,
                datasets: [{
                    label: 'Jumlah Permasalahan',
                    data: kelasData,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8
                }]
            },
            options: {
                ...opsiUmum,
                scales: {
                    x: {
                        ticks: { font: { size: isMobile ? 10 : 11 }, autoSkip: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: kelasStep,
                            precision: 0,
                            font: { size: isMobile ? 9 : 11 }
                        }
                    }
                }
            }
        });

        const kategoriStep = hitungStepRapi(Math.max(...kategoriData, 0));
        new Chart(document.getElementById('kategoriChartCanvas'), {
            type: 'bar',
            data: {
                labels: kategoriLabels,
                datasets: [{
                    label: 'Jumlah Permasalahan',
                    data: kategoriData,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8
                }]
            },
            options: {
                ...opsiUmum,
                scales: {
                    x: {
                        ticks: { font: { size: isMobile ? 10 : 11 }, autoSkip: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: kategoriStep,
                            precision: 0,
                            font: { size: isMobile ? 9 : 11 }
                        }
                    }
                }
            }
        });

        // Kalau layar diputar/di-resize, sesuaikan ulang lebar wrapper & kelas mobile.
        window.addEventListener('resize', function () {
            const mobileSekarang = window.innerWidth < 640;
            if (mobileSekarang) {
                aturLebarWrapper(kelasWrapper, kelasLabels.length, 56);
                aturLebarWrapper(kategoriWrapper, kategoriLabels.length, 70);
            } else {
                kelasWrapper.style.width = '100%';
                kategoriWrapper.style.width = '100%';
            }
        });
    </script>

@endsection