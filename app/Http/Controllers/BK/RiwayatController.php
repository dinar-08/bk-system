<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatController extends Controller
{
    private function queryRiwayat(Request $request)
    {
        $query = Laporan::with(['siswa', 'guruBk', 'evaluasi'])
            ->whereIn('status', ['selesai', 'dirujuk']);

        if ($request->filled('nama')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->nama . '%');
            });
        }

        if ($request->filled('kelas')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $laporan = $this->queryRiwayat($request)
            ->join('siswa', 'laporan.siswa_id', '=', 'siswa.id')
            ->orderBy('siswa.kelas')
            ->orderBy('siswa.nis')
            ->select('laporan.*')
            ->get();

        $daftarKelas = Siswa::select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $kategori = ['akademik', 'sosial', 'perilaku', 'emosional', 'lain-lain'];

        return view('bk.riwayat.index', compact('laporan', 'daftarKelas', 'kategori'));
    }

    public function show(string $id)
    {
        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'pemanggilan',
            'monitoring.guruBk',
            'evaluasi',
        ])->findOrFail($id);

        return view('bk.riwayat.show', compact('laporan'));
    }

    public function exportPdf(Request $request)
    {
        $laporan = $this->queryRiwayat($request)
            ->latest()
            ->get();

        $pdf = Pdf::loadView('bk.download.semua-pdf', compact('laporan'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('riwayat-kasus.pdf');
    }
<<<<<<< HEAD
=======

    public function downloadKasus(string $id)
    {
        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'pemanggilan',
            'monitoring.guruBk',
            'evaluasi',
        ])->findOrFail($id);

        $namaSiswa = optional($laporan->siswa)->nama_siswa ?? 'siswa';

        $pdf = Pdf::loadView('bk.download.kasus-pdf', compact('laporan'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('kasus-' . str_replace(' ', '-', strtolower($namaSiswa)) . '.pdf');
    }
>>>>>>> 4226421 (backup)
}