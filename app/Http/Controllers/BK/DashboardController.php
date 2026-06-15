<?php
namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->first();

        $totalLaporan = Laporan::count();
        $monitoringAktif = Laporan::where('status', 'monitoring')->count();
        $kasusSelesai = Laporan::where('status', 'selesai')->count();

        $laporanTerbaru = Laporan::with(['siswa', 'guruBk'])
            ->latest()->take(5)->get();

        // B1: siswa masuk BK per kelas
        $siswaPerKelas = Laporan::join('siswa', 'laporan.siswa_id', '=', 'siswa.id')
            ->select('siswa.kelas', DB::raw('COUNT(DISTINCT laporan.siswa_id) as jumlah'))
            ->groupBy('siswa.kelas')
            ->orderBy('siswa.kelas')
            ->pluck('jumlah', 'siswa.kelas')
            ->toArray();

        // B2: pie chart jenis kelamin siswa yang pernah masuk BK
        $jenisKelamin = Laporan::join('siswa', 'laporan.siswa_id', '=', 'siswa.id')
            ->select('siswa.jenis_kelamin', DB::raw('COUNT(DISTINCT laporan.siswa_id) as jumlah'))
            ->groupBy('siswa.jenis_kelamin')
            ->pluck('jumlah', 'siswa.jenis_kelamin')
            ->toArray();

        return view('bk.dashboard', compact(
            'totalLaporan',
            'monitoringAktif',
            'kasusSelesai',
            'laporanTerbaru',
            'siswaPerKelas',
            'jenisKelamin',
            'guruBk'
        ));
    }
}
