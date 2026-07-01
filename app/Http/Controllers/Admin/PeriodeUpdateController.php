<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;
use Illuminate\Http\Request;
class PeriodeUpdateController extends Controller
{
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
        return redirect()->route('admin.siswa.index')
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
        return redirect()->route('admin.siswa.index')
            ->with('success', 'Periode update berhasil diperbarui.');
    }
    public function destroy(string $id)
    {
        PeriodeUpdate::findOrFail($id)->delete();
        return redirect()->route('admin.siswa.index')
            ->with('success', 'Periode update berhasil dihapus.');
    }
}