@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    @php
        $genderLabels = ($genderChart ?? collect())->map(function ($item) {
            return match ($item->jenis_kelamin) {
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
                default => 'Tidak Diketahui',
            };
        })->values();
        $genderData = ($genderChart ?? collect())->pluck('total')->values();

        $kelasLabels = ($kelasChart ?? collect())->pluck('kelas')->values();
        $kelasData = ($kelasChart ?? collect())->pluck('total')->values();

        $kategoriLabels = ($kategoriChart ?? collect())->pluck('kategori')->map(fn($item) => ucfirst($item))->values();
        $kategoriData = ($kategoriChart ?? collect())->pluck('total')->values();

        $statusLabels = ($statusChart ?? collect())->pluck('status')->map(fn($s) => ucfirst($s))->values();
        $statusData = ($statusChart ?? collect())->pluck('total')->values();

        $statusColorMap = [
            'baru' => '#eda100',
            'pemanggilan' => '#2a78d6',
            'monitoring' => '#8b5cf6',
            'selesai' => '#1baf7a',
            'dirujuk' => '#ef4444',
        ];
        $statusColors = ($statusChart ?? collect())->pluck('status')->map(function ($s) use ($statusColorMap) {
            return $statusColorMap[$s] ?? '#94a3b8';
        })->values();

        // Guru BK
        $guruLabels = ($guruChart ?? collect())->pluck('nama')->values();
        $guruData = ($guruChart ?? collect())->pluck('total')->values();

        $guruColorPalette = ['#02f80a', '#00e6fb', '#0065f3a7', '#cff300a7'];
        $guruColors = $guruLabels->map(function ($item, $index) use ($guruColorPalette) {
            return $guruColorPalette[$index % count($guruColorPalette)];
        })->values();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-blue-500"></div>
            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jumlah Siswa</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $jumlahSiswa ?? 0 }}</h2>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <i data-feather="users" class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-emerald-500"></div>
            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Guru BK</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $totalGuruBK ?? 0 }}</h2>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <i data-feather="user-check" class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-orange-500"></div>
            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Siswa Tidak Aktif</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $siswaTidakAktif ?? 0 }}</h2>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <i data-feather="archive" class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-red-500"></div>
            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Permasalahan Siswa</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $totalPermasalahan ?? 0 }}</h2>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <i data-feather="alert-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-red-600"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 mt-6 sm:mt-7">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Jenis Kelamin Pelapor</h3>
            <p class="text-xs text-slate-400 mb-4">Jumlah laporan berdasarkan jenis kelamin siswa yang melapor.</p>
            @if(($genderChart ?? collect())->isEmpty())
                <p class="text-sm text-slate-400 italic">Belum ada data.</p>
            @else
                <div class="h-52 sm:h-64">
                    <canvas id="genderChartCanvas"></canvas>
                </div>
                <div id="genderLegend" class="mt-4 flex flex-wrap gap-x-4 gap-y-2"></div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Laporan Berdasarkan Kelas</h3>
            <p class="text-xs text-slate-400 mb-4">Kelas dengan jumlah laporan permasalahan terbanyak.</p>
            @if(($kelasChart ?? collect())->isEmpty())
                <p class="text-sm text-slate-400 italic">Belum ada data.</p>
            @else
                <div class="h-52 sm:h-64 overflow-x-auto">
                    <div id="kelasChartWrapper" class="h-full">
                        <canvas id="kelasChartCanvas"></canvas>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 lg:col-span-2">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Kategori Permasalahan</h3>
            <p class="text-xs text-slate-400 mb-4">Jumlah laporan berdasarkan kategori permasalahan.</p>
            @if(($kategoriChart ?? collect())->isEmpty())
                <p class="text-sm text-slate-400 italic">Belum ada data.</p>
            @else
                <div class="h-56 sm:h-72 overflow-x-auto">
                    <div id="kategoriChartWrapper" class="h-full">
                        <canvas id="kategoriChartCanvas"></canvas>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 mt-6 sm:mt-7">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Status Penanganan Laporan</h3>
            <p class="text-xs text-slate-400 mb-4">Perbandingan laporan baru, monitoring, dan selesai.</p>
            @if(($statusChart ?? collect())->isEmpty())
                <p class="text-sm text-slate-400 italic">Belum ada data.</p>
            @else
                <div class="h-52 sm:h-64">
                    <canvas id="statusChartCanvas"></canvas>
                </div>
                <div id="statusLegend" class="mt-4 flex flex-wrap gap-x-4 gap-y-2"></div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
            <h3 class="font-semibold text-slate-800 text-sm mb-1">Beban Kerja per Guru BK</h3>
            <p class="text-xs text-slate-400 mb-4">Proporsi laporan yang ditangani tiap guru BK.</p>
            @if(($guruChart ?? collect())->isEmpty())
                <p class="text-sm text-slate-400 italic">Belum ada data.</p>
            @else
                <div class="h-52 sm:h-64">
                    <canvas id="guruChartCanvas"></canvas>
                </div>
                <div id="guruLegend" class="mt-4 flex flex-wrap gap-x-4 gap-y-2"></div>
            @endif
        </div>

    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        const genderLabels = @json($genderLabels);
        const genderData = @json($genderData);
        const genderColors = ['#0065f3', '#f80279', '#94a3b8'];

        const kelasLabels = @json($kelasLabels);
        const kelasData = @json($kelasData);

        const kategoriLabels = @json($kategoriLabels);
        const kategoriData = @json($kategoriData);

        const statusLabels = @json($statusLabels);
        const statusData = @json($statusData);
        const statusColors = @json($statusColors);

        const guruLabels = @json($guruLabels);
        const guruData = @json($guruData);
        const guruColors = @json($guruColors);

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

        function renderLegend(containerId, labels, colors) {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.innerHTML = labels.map(function (label, i) {
                const warna = colors[i] || '#94a3b8';
                return `
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-sm shrink-0" style="background-color: ${warna}"></span>
                                <span class="text-xs text-slate-600">${label}</span>
                            </div>
                        `;
            }).join('');
        }

        function datalabelPersen() {
            return {
                color: '#000',
                font: { weight: 'bold', size: isMobile ? 11 : 13 },
                formatter: function (value, context) {
                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    if (!total) return '0%';
                    return ((value / total) * 100).toFixed(1) + '%';
                }
            };
        }

        if (genderData.length > 0) {
            new Chart(document.getElementById('genderChartCanvas'), {
                type: 'pie',
                data: {
                    labels: genderLabels,
                    datasets: [{
                        data: genderData,
                        backgroundColor: genderColors
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    ...opsiUmum,
                    plugins: {
                        ...opsiUmum.plugins,
                        datalabels: datalabelPersen()
                    }
                }
            });
            renderLegend('genderLegend', genderLabels, genderColors);
        }

        if (kelasData.length > 0) {
            const kelasStep = hitungStepRapi(Math.max(...kelasData, 0));
            new Chart(document.getElementById('kelasChartCanvas'), {
                type: 'bar',
                data: {
                    labels: kelasLabels,
                    datasets: [{ label: 'Jumlah Laporan', data: kelasData, categoryPercentage: 0.6, barPercentage: 0.8 }]
                },
                options: {
                    ...opsiUmum,
                    scales: {
                        x: { ticks: { font: { size: isMobile ? 10 : 11 }, autoSkip: false } },
                        y: { beginAtZero: true, ticks: { stepSize: kelasStep, precision: 0, font: { size: isMobile ? 9 : 11 } } }
                    }
                }
            });
        }

        if (kategoriData.length > 0) {
            const kategoriStep = hitungStepRapi(Math.max(...kategoriData, 0));
            new Chart(document.getElementById('kategoriChartCanvas'), {
                type: 'bar',
                data: {
                    labels: kategoriLabels,
                    datasets: [{ label: 'Jumlah Laporan', data: kategoriData, categoryPercentage: 0.6, barPercentage: 0.8 }]
                },
                options: {
                    ...opsiUmum,
                    scales: {
                        x: { ticks: { font: { size: isMobile ? 10 : 11 }, autoSkip: false } },
                        y: { beginAtZero: true, ticks: { stepSize: kategoriStep, precision: 0, font: { size: isMobile ? 9 : 11 } } }
                    }
                }
            });
        }

        if (statusData.length > 0) {
            new Chart(document.getElementById('statusChartCanvas'), {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusColors
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    ...opsiUmum,
                    plugins: {
                        ...opsiUmum.plugins,
                        datalabels: datalabelPersen()
                    }
                }
            });
            renderLegend('statusLegend', statusLabels, statusColors);
        }

        if (guruData.length > 0) {
            new Chart(document.getElementById('guruChartCanvas'), {
                type: 'pie',
                data: {
                    labels: guruLabels,
                    datasets: [{
                        data: guruData,
                        backgroundColor: guruColors
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    ...opsiUmum,
                    plugins: {
                        ...opsiUmum.plugins,
                        datalabels: datalabelPersen()
                    }
                }
            });
            renderLegend('guruLegend', guruLabels, guruColors);
        }

        window.addEventListener('resize', function () {
            const mobileSekarang = window.innerWidth < 640;
            if (kelasWrapper) {
                if (mobileSekarang) {
                    aturLebarWrapper(kelasWrapper, kelasLabels.length, 56);
                } else {
                    kelasWrapper.style.width = '100%';
                }
            }
            if (kategoriWrapper) {
                if (mobileSekarang) {
                    aturLebarWrapper(kategoriWrapper, kategoriLabels.length, 70);
                } else {
                    kategoriWrapper.style.width = '100%';
                }
            }
        });
    </script>

@endsection