<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Evaluasi;
use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\PeriodeUpdate;
use App\Notifications\EvaluasiBaruNotification;
use Illuminate\Http\Request;

class EvaluasiController extends Controller
{
    public function show(string $id)
    {
        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'monitoring.guruBk',
            'evaluasi',
        ])
            ->where('status', 'monitoring')
            ->findOrFail($id);

        return view('bk.evaluasi.show', compact('laporan'));
    }

    public function store(Request $request)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'laporan_id' => ['required', 'exists:laporan,laporan_id'],
            'tanggal_evaluasi' => ['required', 'date'],
            'hasil_evaluasi' => ['required', 'string'],
            'status_akhir' => ['required', 'in:selesai,dirujuk'],
        ]);

        Evaluasi::create([
            'laporan_id' => $validated['laporan_id'],
            'nip' => $guruBk->nip,
            'tanggal_evaluasi' => $validated['tanggal_evaluasi'],
            'hasil_evaluasi' => $validated['hasil_evaluasi'],
            'status_akhir' => $validated['status_akhir'],
        ]);

        $periodeAktif = PeriodeUpdate::terkini();

        $laporanUntukUpdate = Laporan::find($validated['laporan_id']);

        Laporan::where('laporan_id', $validated['laporan_id'])
            ->update([
                'status' => $validated['status_akhir'],
                // Isi tahun_ajaran hanya jika sebelumnya masih kosong,
                // supaya tidak menimpa nilai yang sudah benar.
                'tahun_ajaran' => $laporanUntukUpdate->tahun_ajaran ?? $periodeAktif?->tahun_ajaran,
            ]);

        $laporan = Laporan::with('siswa.user')->find($validated['laporan_id']);
        if ($laporan->siswa && $laporan->siswa->user) {
            $laporan->siswa->user->notify(new EvaluasiBaruNotification($laporan));
        }

        return redirect()
            ->route('bk.riwayat.index')
            ->with('success', 'Evaluasi berhasil disimpan dan kasus telah dipindahkan ke riwayat.');
    }

    public function update(Request $request, string $id)
    {
        $evaluasi = Evaluasi::findOrFail($id);

        $validated = $request->validate([
            'tanggal_evaluasi' => ['required', 'date'],
            'hasil_evaluasi' => ['required', 'string'],
            'status_akhir' => ['required', 'in:selesai,dirujuk'],
        ]);

        $evaluasi->update([
            'tanggal_evaluasi' => $validated['tanggal_evaluasi'],
            'hasil_evaluasi' => $validated['hasil_evaluasi'],
            'status_akhir' => $validated['status_akhir'],
        ]);

        $periodeAktif = PeriodeUpdate::terkini();

        $evaluasi->laporan->update([
            'status' => $validated['status_akhir'],
            'tahun_ajaran' => $evaluasi->laporan->tahun_ajaran ?? $periodeAktif?->tahun_ajaran,
        ]);

        $laporan = $evaluasi->laporan()->with('siswa.user')->first();
        if ($laporan->siswa && $laporan->siswa->user) {
            $laporan->siswa->user->notify(new EvaluasiBaruNotification($laporan));
        }

        return redirect()
            ->route('bk.riwayat.index')
            ->with('success', 'Evaluasi berhasil diperbarui dan kasus telah dipindahkan ke riwayat.');
    }

    public function destroy(string $id)
    {
        Evaluasi::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Evaluasi berhasil dihapus.'
        );
    }
}