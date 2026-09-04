<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;
use App\Models\GuruBK;
use App\Models\PeriodeUpdate; // <-- sesuaikan kalau nama model periode lo beda
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // 1. TENTUKAN PERIODE YANG SEDANG DIPILIH
        // ==========================================

        // Ambil semua tahun ajaran buat isi dropdown, urut dari yang terbaru
        $daftarPeriode = PeriodeUpdate::orderByDesc('tahun_ajaran')->pluck('tahun_ajaran');

        // "semua" = opsi lihat semua data tanpa filter (termasuk data lama yang
        // tahun_ajaran-nya masih NULL). Ini dipisah biar data lama gak "hilang"
        // begitu fitur filter ini aktif.
        $tahunAjaran = $request->query('tahun_ajaran', 'semua');

        // Kalau user belum pernah pilih apa-apa sama sekali (pertama kali buka
        // dashboard, gak ada query string), baru default ke periode yang aktif.
        if (!$request->has('tahun_ajaran')) {
            $periodeAktif = PeriodeUpdate::where('aktif', true)->orderByDesc('tahun_ajaran')->first();
            $tahunAjaran = $periodeAktif->tahun_ajaran ?? 'semua';
        }

        // ==========================================
        // 2. QUERY YANG DI-FILTER SESUAI PERIODE
        // ==========================================

        $jumlahSiswa = Siswa::when($tahunAjaran !== 'semua', function ($query) use ($tahunAjaran) {
            $query->where('tahun_ajaran', $tahunAjaran);
        })
            ->whereHas('user', function ($query) {
                $query->where('status_akun', 'aktif');
            })->count();

        $siswaTidakAktif = Siswa::when($tahunAjaran !== 'semua', function ($query) use ($tahunAjaran) {
            $query->where('tahun_ajaran', $tahunAjaran);
        })
            ->whereHas('user', function ($query) {
                $query->where('status_akun', 'nonaktif');
            })->count();

        $totalPermasalahan = Laporan::when($tahunAjaran !== 'semua', function ($query) use ($tahunAjaran) {
            $query->where('tahun_ajaran', $tahunAjaran);
        })->count();

        // Guru BK sengaja TIDAK difilter periode — ini data staff, bukan data per-tahun ajaran
        $totalGuruBK = GuruBK::count();

        $statusChart = DB::table('laporan')
            ->when($tahunAjaran !== 'semua', function ($query) use ($tahunAjaran) {
                $query->where('tahun_ajaran', $tahunAjaran);
            })
            ->whereNotNull('status')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $guruChart = DB::table('laporan')
            ->join('guru_bk', 'guru_bk.nip', '=', 'laporan.nip')
            ->when($tahunAjaran !== 'semua', function ($query) use ($tahunAjaran) {
                $query->where('laporan.tahun_ajaran', $tahunAjaran);
            })
            ->whereNotNull('laporan.nip')
            ->select('guru_bk.nama', DB::raw('count(*) as total'))
            ->groupBy('guru_bk.nama')
            ->orderByDesc('total')
            ->get();

        $genderChart = DB::table('siswa')
            ->when($tahunAjaran !== 'semua', function ($query) use ($tahunAjaran) {
                $query->where('tahun_ajaran', $tahunAjaran);
            })
            ->whereIn('jenis_kelamin', ['L', 'P'])
            ->select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->get();

        $kelasChart = DB::table('laporan')
            ->when($tahunAjaran !== 'semua', function ($query) use ($tahunAjaran) {
                $query->where('tahun_ajaran', $tahunAjaran);
            })
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->select('kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        $kategoriChart = DB::table('laporan')
            ->when($tahunAjaran !== 'semua', function ($query) use ($tahunAjaran) {
                $query->where('tahun_ajaran', $tahunAjaran);
            })
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
            'guruChart',
            'daftarPeriode',
            'tahunAjaran'
        ));
    }
}