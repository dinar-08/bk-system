@extends('layouts.bk')

@section('title', 'Dashboard BK')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di panel Guru BK')

@section('content')

    @php
        $genderLabels = ($genderChart ?? collect())->map(function ($item) {
            return $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        })->values();

        $genderData = ($genderChart ?? collect())->pluck('total')->values();

        $kelasLabels = ($kelasChart ?? collect())->pluck('kelas')->values();
        $kelasData = ($kelasChart ?? collect())->pluck('total')->values();

        $kategoriLabels = ($kategoriChart ?? collect())->pluck('kategori')->map(fn($item) => ucfirst($item))->values();
        $kategoriData = ($kategoriChart ?? collect())->pluck('total')->values();
    @endphp

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
            <div class="h-52 sm:h-64 overflow-x-auto">
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        const genderLabels = @json($genderLabels);
        const genderData = @json($genderData);

        const kelasLabels = @json($kelasLabels);
        const kelasData = @json($kelasData);

        const kategoriLabels = @json($kategoriLabels);
        const kategoriData = @json($kategoriData);

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

        const isMobile = window.innerWidth < 640;

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