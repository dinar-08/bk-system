<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;

class PerkembanganController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('user_id', auth()->id())
            ->firstOrFail();

        // Mencakup monitoring yang masih aktif, dan yang sudah
        // selesai/dirujuk sebagai riwayat perkembangan anak.
        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'monitoring',
            'evaluasi',
        ])
            ->where('siswa_id', $siswa->id)
            ->whereIn('status', ['monitoring', 'selesai', 'dirujuk'])
            ->latest()
            ->get();

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
            ->where('siswa_id', $siswa->id)
            ->whereIn('status', ['monitoring', 'selesai', 'dirujuk'])
            ->findOrFail($id);

        return view('orang-tua.perkembangan.show', compact('siswa', 'laporan'));
    }
}