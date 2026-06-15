<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('user_id', auth()->id())
            ->firstOrFail();

            //dd($siswa)
        $laporan = Laporan::with([
            'siswa',
            'pemanggilan',
            'monitoring',
            'evaluasi',
        ])
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        $totalLaporan = $laporan->count();

        $laporanAktif = $laporan
            ->whereIn('status', ['baru', 'pemanggilan', 'monitoring'])
            ->count();

        return view(
            'orang-tua.dashboard',
            compact(
                'siswa',
                'laporan',
                'totalLaporan',
                'laporanAktif'
            )
        );
    }
}