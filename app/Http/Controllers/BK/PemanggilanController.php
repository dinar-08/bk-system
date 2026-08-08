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
            'laporan_id' => ['required', 'exists:laporan,laporan_id'],
            'tanggal_pemanggilan' => ['required', 'date'],
            'waktu_pemanggilan' => ['required'],
            'pihak_dipanggil' => ['required', 'in:siswa,orang_tua,siswa_orang_tua'],
            'tujuan' => ['required'],
        ]);

        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $pemanggilan = Pemanggilan::create([
            'laporan_id' => $validated['laporan_id'],
            'nip' => $guruBk->nip, // Fix B6: simpan guru BK
            'tanggal_pemanggilan' => $validated['tanggal_pemanggilan'],
            'waktu_pemanggilan' => $validated['waktu_pemanggilan'],
            'pihak_dipanggil' => $validated['pihak_dipanggil'],
            'tujuan' => $validated['tujuan'],
            'status_kehadiran' => 'belum',
            'tindak_lanjut' => 'belum',
            'tanggal_monitoring' => null,
        ]);

        Laporan::where('laporan_id', $validated['laporan_id'])->update(['status' => 'pemanggilan']);

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
            'tanggal_pemanggilan' => ['nullable', 'date'],
            'waktu_pemanggilan' => ['nullable'],
            'status_kehadiran' => ['required', 'in:belum,hadir,tidak_hadir'],
            'tindak_lanjut' => ['required', 'in:belum,monitoring,selesai'],
            'tanggal_monitoring' => ['nullable', 'date'],
            'waktu_monitoring' => ['nullable', 'date_format:H:i'],
            'catatan' => ['nullable'],
        ]);

        // Cek apakah ini reschedule: pemanggilan masih berstatus "belum"
        // dan tanggal/waktu yang dikirim beda dari yang tersimpan.
        $jadwalBerubah = false;
        if ($pemanggilan->status_kehadiran === 'belum' && $validated['status_kehadiran'] === 'belum') {
            $tanggalBaru = $validated['tanggal_pemanggilan'] ?? $pemanggilan->tanggal_pemanggilan;
            $waktuBaru = $validated['waktu_pemanggilan'] ?? $pemanggilan->waktu_pemanggilan;

            $jadwalBerubah = (string) $tanggalBaru !== (string) $pemanggilan->tanggal_pemanggilan
                || (string) $waktuBaru !== (string) $pemanggilan->waktu_pemanggilan;
        }

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
            'tanggal_pemanggilan' => $jadwalBerubah ? $validated['tanggal_pemanggilan'] : $pemanggilan->tanggal_pemanggilan,
            'waktu_pemanggilan' => $jadwalBerubah ? $validated['waktu_pemanggilan'] : $pemanggilan->waktu_pemanggilan,
            'status_kehadiran' => $validated['status_kehadiran'],
            'tindak_lanjut' => $validated['tindak_lanjut'],
            'tanggal_monitoring' => $validated['tanggal_monitoring'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Kalau jadwal berubah (reschedule), kirim notifikasi baru dan stop di sini
        if ($jadwalBerubah) {
            $pemanggilan->load('laporan.siswa.user');
            if ($pemanggilan->laporan?->siswa?->user) {
                $pemanggilan->laporan->siswa->user->notify(new JadwalPemanggilanNotification($pemanggilan));
            }

            return back()->with('success', 'Jadwal pemanggilan berhasil diubah dan notifikasi baru telah dikirim.');
        }

        if ($validated['status_kehadiran'] === 'hadir') {
            if ($validated['tindak_lanjut'] === 'monitoring') {
                $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();
                $laporan = $pemanggilan->laporan;
                $laporan->update(['status' => 'monitoring']);

                if (!Monitoring::where('laporan_id', $laporan->laporan_id)->where('monitoring_ke', 1)->exists()) {
                    Monitoring::create([
                        'laporan_id' => $laporan->laporan_id,
                        'nip' => $guruBk->nip,
                        'tanggal_monitoring' => $validated['tanggal_monitoring'],
                        'waktu_monitoring' => $validated['waktu_monitoring'] ?? null,
                        'monitoring_ke' => 1,
                        'status_monitoring' => 'terjadwal',
                        'status_perkembangan' => 'stabil',
                        'catatan_perkembangan' => '-',
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