<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\Laporan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->first();

        $totalLaporan = Laporan::count();

        $monitoringAktif = Laporan::where('status', 'monitoring')->count();

        $permasalahanSelesai = Laporan::where('status', 'selesai')->count();

        $permasalahanDirujuk = Laporan::where('status', 'dirujuk')->count();

        $laporanTerbaru = Laporan::with(['siswa', 'guruBk'])
            ->latest()
            ->take(5)
            ->get();

        $siswaPerKelas = Laporan::join('siswa', 'laporan.siswa_id', '=', 'siswa.id')
            ->select('siswa.kelas', DB::raw('COUNT(DISTINCT laporan.siswa_id) as jumlah'))
            ->groupBy('siswa.kelas')
            ->orderBy('siswa.kelas')
            ->pluck('jumlah', 'siswa.kelas')
            ->toArray();

        $genderChart = Laporan::join('siswa', 'laporan.siswa_id', '=', 'siswa.id')
            ->selectRaw('siswa.jenis_kelamin, COUNT(*) as total')
            ->groupBy('siswa.jenis_kelamin')
            ->get();

        $kelasChart = Laporan::join('siswa', 'laporan.siswa_id', '=', 'siswa.id')
            ->selectRaw('siswa.kelas, COUNT(*) as total')
            ->groupBy('siswa.kelas')
            ->orderByDesc('total')
            ->get();

        $kategoriChart = Laporan::selectRaw('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        return view('bk.dashboard', compact(
            'guruBk',
            'totalLaporan',
            'monitoringAktif',
            'permasalahanSelesai',
            'permasalahanDirujuk',
            'laporanTerbaru',
            'siswaPerKelas',
            'genderChart',
            'kelasChart',
            'kategoriChart'
        ));
    }
}