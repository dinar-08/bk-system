<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\Monitoring;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $laporan = Laporan::with(['siswa', 'guruBk', 'monitoring'])
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
            'laporan_id' => ['required', 'exists:laporan,id'],
            'monitoring_id' => ['nullable', 'exists:monitoring,id'],
            'tanggal_monitoring' => ['required', 'date'],
            'tanggal_monitoring_berikutnya' => ['nullable', 'date'],
            'status_perkembangan' => ['required', 'in:membaik,stabil,menurun'],
            'catatan_perkembangan' => ['required', 'string'],
        ]);

        if (!empty($validated['monitoring_id'])) {
            $monitoring = Monitoring::where('id', $validated['monitoring_id'])
                ->where('laporan_id', $validated['laporan_id'])
                ->firstOrFail();

            $monitoring->update([
                'guru_bk_id' => $guruBk->id,
                'tanggal_monitoring' => $validated['tanggal_monitoring'],
                'status_monitoring' => 'selesai',
                'status_perkembangan' => $validated['status_perkembangan'],
                'catatan_perkembangan' => $validated['catatan_perkembangan'],
                'tindak_lanjut' => '-',
                'tanggal_monitoring_berikutnya' => $validated['tanggal_monitoring_berikutnya'] ?? null,
            ]);

            if (!empty($validated['tanggal_monitoring_berikutnya'])) {
                $sudahAdaJadwal = Monitoring::where('laporan_id', $validated['laporan_id'])
                    ->where('tanggal_monitoring', $validated['tanggal_monitoring_berikutnya'])
                    ->where('status_monitoring', 'terjadwal')
                    ->exists();

                if (! $sudahAdaJadwal) {
                    Monitoring::create([
                        'laporan_id' => $validated['laporan_id'],
                        'guru_bk_id' => $guruBk->id,
                        'tanggal_monitoring' => $validated['tanggal_monitoring_berikutnya'],
                        'monitoring_ke' => $monitoring->monitoring_ke + 1,
                        'status_monitoring' => 'terjadwal',
                        'status_perkembangan' => 'stabil',
                        'catatan_perkembangan' => '-',
                        'tindak_lanjut' => '-',
                        'tanggal_monitoring_berikutnya' => null,
                    ]);
                }
            }

            return back()->with('success', 'Hasil monitoring berhasil disimpan.');
        }

        $monitoringKe = Monitoring::where('laporan_id', $validated['laporan_id'])->count() + 1;

        Monitoring::create([
            'laporan_id' => $validated['laporan_id'],
            'guru_bk_id' => $guruBk->id,
            'tanggal_monitoring' => $validated['tanggal_monitoring'],
            'monitoring_ke' => $monitoringKe,
            'status_monitoring' => 'selesai',
            'status_perkembangan' => $validated['status_perkembangan'],
            'catatan_perkembangan' => $validated['catatan_perkembangan'],
            'tindak_lanjut' => '-',
            'tanggal_monitoring_berikutnya' => $validated['tanggal_monitoring_berikutnya'] ?? null,
        ]);

        if (!empty($validated['tanggal_monitoring_berikutnya'])) {
            Monitoring::create([
                'laporan_id' => $validated['laporan_id'],
                'guru_bk_id' => $guruBk->id,
                'tanggal_monitoring' => $validated['tanggal_monitoring_berikutnya'],
                'monitoring_ke' => $monitoringKe + 1,
                'status_monitoring' => 'terjadwal',
                'status_perkembangan' => 'stabil',
                'catatan_perkembangan' => '-',
                'tindak_lanjut' => '-',
                'tanggal_monitoring_berikutnya' => null,
            ]);
        }

        Laporan::where('id', $validated['laporan_id'])
            ->update(['status' => 'monitoring']);

        return back()->with('success', 'Catatan monitoring berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $monitoring = Monitoring::findOrFail($id);

        $validated = $request->validate([
            'tanggal_monitoring' => ['required', 'date'],
            'tanggal_monitoring_berikutnya' => ['nullable', 'date'],
            'status_perkembangan' => ['required', 'in:membaik,stabil,menurun'],
            'catatan_perkembangan' => ['required', 'string'],
        ]);

        $monitoring->update([
            'tanggal_monitoring' => $validated['tanggal_monitoring'],
            'tanggal_monitoring_berikutnya' => $validated['tanggal_monitoring_berikutnya'] ?? null,
            'status_monitoring' => 'selesai',
            'status_perkembangan' => $validated['status_perkembangan'],
            'catatan_perkembangan' => $validated['catatan_perkembangan'],
        ]);

        return back()->with('success', 'Catatan monitoring berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Monitoring::findOrFail($id)->delete();

        return back()->with('success', 'Catatan monitoring berhasil dihapus.');
    }
}