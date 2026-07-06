<?php
namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\Monitoring;
use App\Models\Pemanggilan;
use App\Notifications\JadwalPemanggilanNotification;
use Illuminate\Http\Request;

class PemanggilanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'laporan_id' => ['required', 'exists:laporan,id'],
            'tanggal_pemanggilan' => ['required', 'date'],
            'waktu_pemanggilan' => ['required'],
            'pihak_dipanggil' => ['required', 'in:siswa,orang_tua,siswa_orang_tua'],
            'tujuan' => ['required'],
        ]);

        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $pemanggilan = Pemanggilan::create([
            'laporan_id' => $validated['laporan_id'],
            'guru_bk_id' => $guruBk->id, // Fix B6: simpan guru BK
            'tanggal_pemanggilan' => $validated['tanggal_pemanggilan'],
            'waktu_pemanggilan' => $validated['waktu_pemanggilan'],
            'pihak_dipanggil' => $validated['pihak_dipanggil'],
            'tujuan' => $validated['tujuan'],
            'status_kehadiran' => 'belum',
            'tindak_lanjut' => 'belum',
            'tanggal_monitoring' => null,
        ]);

        Laporan::where('id', $validated['laporan_id'])->update(['status' => 'pemanggilan']);

        $pemanggilan->load('laporan.siswa.user');
        if ($pemanggilan->laporan?->siswa?->user) {
            $pemanggilan->laporan->siswa->user->notify(new JadwalPemanggilanNotification($pemanggilan));
        }

        return back()->with('success', 'Jadwal pemanggilan berhasil dibuat.');
    }

    public function update(Request $request, string $id)
    {
        $pemanggilan = Pemanggilan::with('laporan')->findOrFail($id);

        $validated = $request->validate([
            'status_kehadiran' => ['required', 'in:belum,hadir,tidak_hadir'],
            'tindak_lanjut' => ['required', 'in:belum,monitoring,selesai'],
            'tanggal_monitoring' => ['nullable', 'date'],
            'catatan' => ['nullable'],
        ]);

        if (
            $validated['status_kehadiran'] === 'hadir' &&
            $validated['tindak_lanjut'] === 'monitoring' &&
            empty($validated['tanggal_monitoring'])
        ) {
            return back()->withInput()->withErrors([
                'tanggal_monitoring' => 'Tanggal monitoring wajib diisi jika tindak lanjut adalah monitoring.',
            ]);
        }

        $pemanggilan->update([
            'status_kehadiran' => $validated['status_kehadiran'],
            'tindak_lanjut' => $validated['tindak_lanjut'],
            'tanggal_monitoring' => $validated['tanggal_monitoring'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        if ($validated['status_kehadiran'] === 'hadir') {
            if ($validated['tindak_lanjut'] === 'monitoring') {
                $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();
                $laporan = $pemanggilan->laporan;
                $laporan->update(['status' => 'monitoring']);

                if (!Monitoring::where('laporan_id', $laporan->id)->where('monitoring_ke', 1)->exists()) {
                    Monitoring::create([
                        'laporan_id' => $laporan->id,
                        'guru_bk_id' => $guruBk->id,
                        'tanggal_monitoring' => $validated['tanggal_monitoring'],
                        'monitoring_ke' => 1,
                        'status_monitoring' => 'terjadwal',
                        'status_perkembangan' => 'stabil',
                        'catatan_perkembangan' => '-',
                        'tindak_lanjut' => '-',
                        'tanggal_monitoring_berikutnya' => null,
                    ]);
                }

                return redirect()->route('bk.monitoring.index')
                    ->with('success', 'Laporan berhasil dipindahkan ke monitoring.');
            }

            if ($validated['tindak_lanjut'] === 'selesai') {
                $pemanggilan->laporan->update(['status' => 'selesai']);
                return redirect()->route('bk.laporan.index')
                    ->with('success', 'Laporan berhasil diselesaikan.');
            }
        }

        if ($validated['status_kehadiran'] === 'tidak_hadir') {
            $pemanggilan->laporan->update(['status' => 'pemanggilan']);
        }

        return back()->with('success', 'Data pemanggilan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Pemanggilan::findOrFail($id)->delete();
        return back()->with('success', 'Data pemanggilan berhasil dihapus.');
    }
}