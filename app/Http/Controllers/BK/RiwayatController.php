<?php
namespace App\Http\Controllers\BK;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;
use App\Models\PeriodeUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
class RiwayatController extends Controller
{
    private function queryRiwayat(Request $request)
    {
        $query = Laporan::periodeAktif()
            ->with(['siswa', 'guruBk', 'evaluasi'])
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
            ->join('siswa', 'laporan.nis', '=', 'siswa.nis')
            ->orderBy('siswa.kelas')
            ->orderBy('siswa.nis')
            ->select('laporan.*')
            ->get();

        $daftarKelas = Siswa::select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $kategori = ['akademik', 'sosial', 'perilaku', 'emosional', 'lain-lain'];

        $semuaLaporanUntukSearch = Laporan::with('siswa')
            ->whereIn('status', ['selesai', 'dirujuk'])
            ->latest()
            ->get()
            ->groupBy('nis')
            ->map(fn($grup) => $grup->first()) 
            ->values()
            ->map(function ($item) {
                $siswa = $item->siswa;
                return [
                    'laporan_id' => $item->laporan_id,
                    'nama' => optional($siswa)->nama_siswa ?? '-',
                    'kelas' => optional($siswa)->kelas ?? '-',
                    'foto_url' => optional($siswa)->foto
                        ? route('foto.siswa', $siswa->nis)
                        : null,
                    'kategori' => $item->kategori ?: 'lain-lain',
                    'judul' => $item->judul_laporan ?? $item->jenis_masalah ?? '-',
                    'status' => $item->status,
                    'show_url' => route('bk.riwayat.siswa', $siswa->nis),
                ];
            })
            ->values();

        return view('bk.riwayat.index', compact(
            'laporan',
            'daftarKelas',
            'kategori',
            'semuaLaporanUntukSearch'
        ));
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

    public function riwayatSiswa(string $nis)
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        $daftarLaporan = Laporan::with([
            'periodeUpdate',
            'guruBk',
            'pemanggilan',
            'monitoring.guruBk',
            'evaluasi',
        ])
            ->where('nis', $nis)
            ->latest()
            ->get();

        return view('bk.riwayat.show', compact('siswa', 'daftarLaporan'));
    }

    public function downloadRiwayatSiswa(string $nis)
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        $laporan = Laporan::with(['siswa', 'guruBk', 'evaluasi', 'periodeUpdate'])
            ->where('nis', $nis)
            ->latest()
            ->get();

        $namaFile = Str::slug($siswa->nama_siswa, '_') . '_riwayat_permasalahan.pdf';

        $pdf = Pdf::loadView('bk.download.semua-pdf', compact('laporan'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($namaFile);
    }

    public function exportPdf(Request $request)
    {
        $laporan = $this->queryRiwayat($request)
            ->latest()
            ->get();
        $pdf = Pdf::loadView('bk.download.semua-pdf', compact('laporan'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('riwayat permasalahan.pdf');
    }

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
        $kelas = optional($laporan->siswa)->kelas ?? 'kelas';
        $kategori = $laporan->kategori ?? 'umum';

        $namaFile = collect([$namaSiswa, $kelas, $kategori])
            ->map(fn($val) => Str::slug($val, '_'))
            ->implode('_') . '.pdf';

        $pdf = Pdf::loadView('bk.download.kasus-pdf', compact('laporan'))
            ->setPaper('a4', 'portrait');
        return $pdf->download($namaFile);
    }
}