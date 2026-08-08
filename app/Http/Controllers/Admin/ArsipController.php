<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;

class ArsipController extends Controller
{
    public function index()
    {
        $arsip = Siswa::with(['user', 'periodeUpdate'])
            ->whereHas('user', fn($q) => $q->where('status_akun', 'nonaktif'))
            ->orderBy('kelas')
            ->orderBy('nis')
            ->get();

        $grouped = $arsip->groupBy(fn($item) => $item->tahun_ajaran ?? '_data_lama');

        $periodeList = PeriodeUpdate::orderByDesc('tanggal_mulai')->get();

        $arsipPerPeriode = collect();

        foreach ($periodeList as $periode) {
            if ($grouped->has($periode->tahun_ajaran)) {
                $arsipPerPeriode->put($periode->tahun_ajaran, [
                    'periode' => $periode,
                    'kelas' => $grouped[$periode->tahun_ajaran]->groupBy('kelas')->sortKeys(),
                ]);
            }
        }

        if ($grouped->has('_data_lama')) {
            $arsipPerPeriode->put('Data Lama', [
                'periode' => null,
                'kelas' => $grouped['_data_lama']->groupBy('kelas')->sortKeys(),
            ]);
        }

        // Data flat untuk fitur search (dipakai JS, tidak lewat query lagi)
        $arsipJson = $arsip->map(function ($item) {
            return [
                'nis' => $item->nis,
                'nama' => $item->nama_siswa,
                'kelas' => $item->kelas,
                'periode' => $item->periodeUpdate->tahun_ajaran ?? 'Data Lama',
                'foto_url' => $item->foto ? route('foto.siswa', $item->nis) : null,
                'nonaktif_at' => optional($item->user->nonaktif_at)->format('d-m-Y'),
                'show_url' => route('admin.arsip.show', $item->nis),
                'destroy_url' => route('admin.siswa.destroy', $item->nis),
            ];
        })->values();

        return view('admin.arsip.index', compact('arsipPerPeriode', 'arsipJson'));
    }

    public function show($id)
    {
        $siswa = Siswa::with([
            'user',
            'periodeUpdate',
            'laporan.pemanggilan',
            'laporan.monitoring',
            'laporan.evaluasi'
        ])->findOrFail($id);

        return view('admin.arsip.show', compact('siswa'));
    }
}