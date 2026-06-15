<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PeriodeUpdateController extends Controller
{
    public function index()
    {
        $periode = PeriodeUpdate::with('createdBy')->latest()->get();
        $aktifSekarang = PeriodeUpdate::aktifSekarang();

        // Rekap siswa yg sudah/belum update
        $rekapSiswa = [];
        if ($aktifSekarang) {
            $semuaSiswa = Siswa::with('user')->get();
            foreach ($semuaSiswa as $s) {
                $rekapSiswa[] = [
                    'nama' => $s->nama_siswa,
                    'kelas' => $s->kelas,
                    'sudah_update' => $s->sudahUpdateDiPeriode($aktifSekarang),
                    'update_at' => $s->last_data_updated_at?->format('d/m/Y H:i'),
                ];
            }
        }

        return view('admin.periode-update.index', compact('periode', 'aktifSekarang', 'rekapSiswa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        PeriodeUpdate::create([
            ...$validated,
            'aktif' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.periode-update.index')
            ->with('success', 'Periode update berhasil dibuat.');
    }

    public function update(Request $request, string $id)
    {
        $periode = PeriodeUpdate::findOrFail($id);
        $validated = $request->validate([
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'aktif' => ['boolean'],
        ]);

        $periode->update($validated);

        return redirect()->route('admin.periode-update.index')
            ->with('success', 'Periode update berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        PeriodeUpdate::findOrFail($id)->delete();
        return redirect()->route('admin.periode-update.index')
            ->with('success', 'Periode update berhasil dihapus.');
    }
}


