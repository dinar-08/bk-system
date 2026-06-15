<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;
use App\Models\GuruBK;

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

        return view('admin.dashboard', compact(
            'jumlahSiswa',
            'siswaTidakAktif',
            'totalPermasalahan',
            'totalGuruBK'
        ));
    }
}
