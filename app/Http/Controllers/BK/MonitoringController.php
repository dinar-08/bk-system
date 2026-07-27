<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\Monitoring;
use App\Notifications\MonitoringBaruNotification;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $laporan = Laporan::with(['siswa', 'guruBk', 'monitoring', 'pemanggilan'])
            ->where('status', 'monitoring')
            ->latest()
            ->get();

        return view('bk.monitoring.index', compact('laporan'));
    }

    public function show(string $id)
    {
        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'pemanggilan',
            'monitoring',
            'evaluasi',
        ])
            ->where('status', 'monitoring')
            ->findOrFail($id);

        return view('bk.monitoring.show', compact('laporan'));
    }

    public function store(Request $request)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'laporan_id' => ['required', 'exists:laporan,laporan_id'],
            'monitoring_id' => ['nullable', 'exists:monitoring,monitoring_id'],
            'tanggal_monitoring' => ['required', 'date'],
            'waktu_monitoring' => ['nullable', 'date_format:H:i'],
            'tanggal_monitoring_berikutnya' => ['nullable', 'date'],
            'waktu_monitoring_berikutnya' => ['nullable', 'date_format:H:i'],
            'status_perkembangan' => ['required', 'in:membaik,stabil,menurun'],
            'catatan_perkembangan' => ['required', 'string'],
        ]);

        if (!empty($validated['monitoring_id'])) {
            $monitoring = Monitoring::where('monitoring_id', $validated['monitoring_id'])
                ->where('laporan_id', $validated['laporan_id'])
                ->firstOrFail();

            $monitoring->update([
                'nip' => $guruBk->nip,
                'tanggal_monitoring' => $validated['tanggal_monitoring'],
                'waktu_monitoring' => $validated['waktu_monitoring'] ?? null,
                'status_monitoring' => 'selesai',
                'status_perkembangan' => $validated['status_perkembangan'],
                'catatan_perkembangan' => $validated['catatan_perkembangan'],
                'tanggal_monitoring_berikutnya' => $validated['tanggal_monitoring_berikutnya'] ?? null,
                'waktu_monitoring_berikutnya' => $validated['waktu_monitoring_berikutnya'] ?? null,
            ]);

            if ($request->filled('tanggal_monitoring_berikutnya')) {
                $this->buatJadwalMonitoringBerikutnya($validated, $guruBk->nip, $monitoring->monitoring_ke);

                return back()->with('success', 'Hasil monitoring berhasil disimpan dan jadwal berikutnya sudah dibuat.');
            }

            return redirect()
                ->route('bk.evaluasi.show', $validated['laporan_id'])
                ->with('success', 'Monitoring selesai. Silakan lanjutkan evaluasi.');
        }

        $monitoringKe = Monitoring::where('laporan_id', $validated['laporan_id'])->count() + 1;

        $monitoring = Monitoring::create([
            'laporan_id' => $validated['laporan_id'],
            'nip' => $guruBk->nip,
            'tanggal_monitoring' => $validated['tanggal_monitoring'],
            'waktu_monitoring' => $validated['waktu_monitoring'] ?? null,
            'monitoring_ke' => $monitoringKe,
            'status_monitoring' => 'selesai',
            'status_perkembangan' => $validated['status_perkembangan'],
            'catatan_perkembangan' => $validated['catatan_perkembangan'],
            'tanggal_monitoring_berikutnya' => $validated['tanggal_monitoring_berikutnya'] ?? null,
            'waktu_monitoring_berikutnya' => $validated['waktu_monitoring_berikutnya'] ?? null,
        ]);

        Laporan::where('laporan_id', $validated['laporan_id'])
            ->update([
                'status' => 'monitoring',
                'nip' => $guruBk->nip,
            ]);

        $laporanUntukNotif = Laporan::with('siswa.user')->find($validated['laporan_id']);
        if ($laporanUntukNotif->siswa && $laporanUntukNotif->siswa->user) {
            $laporanUntukNotif->siswa->user->notify(new MonitoringBaruNotification($laporanUntukNotif));
        }

        if ($request->filled('tanggal_monitoring_berikutnya')) {
            $this->buatJadwalMonitoringBerikutnya($validated, $guruBk->nip, $monitoring->monitoring_ke);

            return back()->with('success', 'Catatan monitoring berhasil ditambahkan dan jadwal berikutnya sudah dibuat.');
        }

        return redirect()
            ->route('bk.evaluasi.show', $validated['laporan_id'])
            ->with('success', 'Monitoring selesai. Silakan lanjutkan evaluasi.');
    }

    public function update(Request $request, string $id)
    {
        $monitoring = Monitoring::findOrFail($id);

        $validated = $request->validate([
            'tanggal_monitoring' => ['required', 'date'],
            'waktu_monitoring' => ['nullable', 'date_format:H:i'],
            'tanggal_monitoring_berikutnya' => ['nullable', 'date'],
            'waktu_monitoring_berikutnya' => ['nullable', 'date_format:H:i'],
            'status_perkembangan' => ['required', 'in:membaik,stabil,menurun'],
            'catatan_perkembangan' => ['required', 'string'],
        ]);

        $monitoring->update([
            'tanggal_monitoring' => $validated['tanggal_monitoring'],
            'waktu_monitoring' => $validated['waktu_monitoring'] ?? null,
            'tanggal_monitoring_berikutnya' => $validated['tanggal_monitoring_berikutnya'] ?? null,
            'waktu_monitoring_berikutnya' => $validated['waktu_monitoring_berikutnya'] ?? null,
            'status_monitoring' => 'selesai',
            'status_perkembangan' => $validated['status_perkembangan'],
            'catatan_perkembangan' => $validated['catatan_perkembangan'],
        ]);

        if ($request->filled('tanggal_monitoring_berikutnya')) {
            $this->buatJadwalMonitoringBerikutnya($validated, $monitoring->nip, $monitoring->monitoring_ke, $monitoring->laporan_id);

            return back()->with('success', 'Catatan monitoring berhasil diperbarui dan jadwal berikutnya sudah dibuat.');
        }

        return redirect()
            ->route('bk.evaluasi.show', $monitoring->laporan_id)
            ->with('success', 'Monitoring selesai. Silakan lanjutkan evaluasi.');
    }

    public function destroy(string $id)
    {
        Monitoring::findOrFail($id)->delete();

        return back()->with('success', 'Catatan monitoring berhasil dihapus.');
    }

    private function buatJadwalMonitoringBerikutnya(
        array $validated,
        string $guruBkId,
        int $monitoringKeSaatIni,
        ?int $laporanId = null
    ): Monitoring {
        return Monitoring::create([
            'laporan_id' => $laporanId ?? $validated['laporan_id'],
            'nip' => $guruBkId,
            'tanggal_monitoring' => $validated['tanggal_monitoring_berikutnya'],
            'waktu_monitoring' => $validated['waktu_monitoring_berikutnya'] ?? null,
            'monitoring_ke' => $monitoringKeSaatIni + 1,
            'status_monitoring' => 'terjadwal',
            'status_perkembangan' => 'stabil',
            'catatan_perkembangan' => '-',
        ]);
    }
}