<?php
namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::with(['siswa', 'guruBk'])
            ->whereIn('status', ['selesai', 'dirujuk']);

        // B3: search kombinasi nama + kelas + kategori
        if ($request->filled('nama')) {
            $query->whereHas('siswa', fn($q) => $q->where('nama_siswa', 'like', '%' . $request->nama . '%'));
        }
        if ($request->filled('kelas')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas', $request->kelas));
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', 'like', $request->kategori . '%');
        }

        $laporan = $query->latest()->get();
        $daftarKelas = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $kategori = ['akademik', 'sosial', 'perilaku', 'emosional', 'lain-lain'];

        return view('bk.riwayat.index', compact('laporan', 'daftarKelas', 'kategori'));
    }

    public function show(string $id)
    {
        // B4: rekap lengkap per siswa dalam 1 halaman
        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'pemanggilan',
            'monitoring.guruBk',
            'evaluasi',
        ])->findOrFail($id);

        return view('bk.riwayat.show', compact('laporan'));
    }
}
