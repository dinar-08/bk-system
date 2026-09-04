<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeUpdate;
use Illuminate\Http\Request;

class PeriodeUpdateController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => [
                'required',
                'string',
                'max:20',
                'unique:periode_update,tahun_ajaran',
            ],
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ], [
            'tahun_ajaran.unique' => 'Tahun ajaran tersebut sudah ada.',
            'tanggal_selesai.after_or_equal' =>
                'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        PeriodeUpdate::create([
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'aktif' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Periode update berhasil dibuat.');
    }


    public function update(Request $request, string $tahun_ajaran)
    {
        $periode = PeriodeUpdate::where(
            'tahun_ajaran',
            $tahun_ajaran
        )->firstOrFail();

        $validated = $request->validate([
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ], [
            'tanggal_selesai.after_or_equal' =>
                'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $periode->update([
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
        ]);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Periode update berhasil diperbarui.');
    }


    public function destroy(string $tahun_ajaran)
    {
        $periode = PeriodeUpdate::where(
            'tahun_ajaran',
            $tahun_ajaran
        )->firstOrFail();

        $periode->delete();

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Periode update berhasil dihapus.');
    }
}