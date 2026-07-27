<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;
use App\Notifications\MonitoringBaruNotification;

class PerkembanganController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('user_id', auth()->id())
            ->firstOrFail();

        // Laporan yang sudah 'selesai' atau 'dirujuk' otomatis pindah ke riwayat.
        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'monitoring',
            'evaluasi',
        ])
            ->where('nis', $siswa->nis)
            ->where('status', 'monitoring')
            ->latest()
            ->get();

        $this->tandaiNotifikasiMonitoringDibaca();

        return view('orang-tua.perkembangan.index', compact('siswa', 'laporan'));
    }

    public function show(string $id)
    {
        $siswa = Siswa::where('user_id', auth()->id())
            ->firstOrFail();

        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'pemanggilan',
            'monitoring' => function ($query) {
                $query->orderByDesc('tanggal_monitoring');
            },
            'evaluasi',
        ])
            ->where('nis', $siswa->nis)
            ->where('status', 'monitoring')
            ->findOrFail($id);

        // Tandai juga saat user langsung buka detail
        $this->tandaiNotifikasiMonitoringDibaca();

        return view('orang-tua.perkembangan.show', compact('siswa', 'laporan'));
    }

    protected function tandaiNotifikasiMonitoringDibaca(): void
    {
        auth()->user()->unreadNotifications()
            ->where('type', MonitoringBaruNotification::class)
            ->update(['read_at' => now()]);
    }
}