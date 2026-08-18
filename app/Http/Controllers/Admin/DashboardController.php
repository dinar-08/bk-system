<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;
use App\Models\GuruBK;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        $jumlahSiswa = Siswa::whereHas('user', function ($query) {
            $query->where('status_akun', 'aktif');
        })->count();

        $siswaTidakAktif = Siswa::whereHas('user', function ($query) {
            $query->where('status_akun', 'nonaktif');
        })->count();

        $totalPermasalahan = Laporan::count();

        $totalGuruBK = GuruBK::count();

        $statusChart = DB::table('laporan')
            ->whereNotNull('status')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $guruChart = DB::table('laporan')
            ->join('guru_bk', 'guru_bk.nip', '=', 'laporan.nip')
            ->whereNotNull('laporan.nip')
            ->select('guru_bk.nama', DB::raw('count(*) as total'))
            ->groupBy('guru_bk.nama')
            ->orderByDesc('total')
            ->get();

        $genderChart = DB::table('siswa')
            ->whereIn('jenis_kelamin', ['L', 'P'])
            ->select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->get();

        $kelasChart = DB::table('laporan')
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->select('kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        $kategoriChart = DB::table('laporan')
            ->whereNotNull('kategori')
            ->select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', compact(
            'jumlahSiswa',
            'siswaTidakAktif',
            'totalPermasalahan',
            'totalGuruBK',
            'genderChart',
            'kelasChart',
            'kategoriChart',
            'statusChart',
            'guruChart'
        ));
    }
}