<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\Pemanggilan;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;
use App\Notifications\LaporanBaruNotification;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    const KATEGORI = ['akademik', 'sosial', 'perilaku', 'emosional', 'lain-lain'];

    public function index()
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        // Laporan langsung terkunci ke guru BK yang membuatnya sejak awal
        // (nip diisi otomatis di store()), jadi cukup filter berdasarkan nip.
        $laporan = Laporan::with(['siswa', 'guruBk'])
            ->where('nip', $guruBk->nip)
            ->latest()
            ->get();

        return view('bk.laporan.index', compact('laporan'));
    }

    public function create()
    {

        $siswa = Siswa::select('nis', 'nama_siswa', 'kelas', 'user_id')
            ->whereHas('user', function ($query) {
                $query->where('status_akun', 'aktif');
            })
            ->orderBy('nama_siswa')
            ->get();

        $kategori = self::KATEGORI;

        return view('bk.laporan.create', compact('siswa', 'kategori'));
    }

    public function store(Request $request)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'nis' => [
                'required',
                'exists:siswa,nis',
                function ($attribute, $value, $fail) {
                    $siswa = Siswa::with('user')->where('nis', $value)->first();

                    if (!$siswa || !$siswa->user || $siswa->user->status_akun !== 'aktif') {
                        $fail('Siswa ini tidak aktif dan tidak bisa dilaporkan.');
                    }
                },
            ],
            'judul_laporan' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'in:' . implode(',', self::KATEGORI)],
            'jenis_masalah' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,mp3,mp4,mov,wav,m4a,ogg', 'max:51200'],
        ]);

        $siswa = Siswa::where('nis', $validated['nis'])->firstOrFail();

        $periodeAktif = PeriodeUpdate::where('aktif', true)->first();

        if (!$periodeAktif) {
            return back()
                ->withInput()
                ->with('error', 'Tidak ada periode/tahun ajaran yang aktif. Hubungi admin untuk mengaktifkan periode terlebih dahulu.');
        }

        $buktiPath = null;

        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti-laporan', 'local');
        }

        $laporan = Laporan::create([
            'nis' => $validated['nis'],
            'nip' => $guruBk->nip,
            'tahun_ajaran' => $periodeAktif->tahun_ajaran,
            'kelas' => $siswa->kelas,
            'judul_laporan' => $validated['judul_laporan'],
            'kategori' => $validated['kategori'],
            'jenis_masalah' => $validated['jenis_masalah'],
            'deskripsi' => $validated['deskripsi'],
            'bukti' => $buktiPath,
            'status' => 'baru',
        ]);

        $laporan->load('siswa.user');
        if ($laporan->siswa && $laporan->siswa->user) {
            $laporan->siswa->user->notify(new LaporanBaruNotification($laporan));
        }

        return redirect()
            ->route('bk.laporan.index')
            ->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(string $id)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'pemanggilan',
        ])->findOrFail($id);

        // Laporan sudah pasti milik satu guru BK sejak dibuat, jadi cukup
        // cek kecocokan nip - tidak ada lagi pengecualian status "baru".
        if ($laporan->nip !== $guruBk->nip) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        return view('bk.laporan.show', compact('laporan'));
    }

    public function proses(Request $request, string $id)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $laporan = Laporan::findOrFail($id);

        // Hanya guru BK pembuat laporan yang boleh memprosesnya.
        if ($laporan->nip !== $guruBk->nip) {
            abort(403, 'Anda tidak memiliki akses untuk memproses laporan ini.');
        }

        if ($laporan->status !== 'baru') {
            abort(403, 'Laporan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'kategori' => ['required', 'in:' . implode(',', self::KATEGORI)],
            'jenis_masalah' => ['required', 'string', 'max:150'],
            'tanggal_pemanggilan' => ['required', 'date'],
            'waktu_pemanggilan' => ['required'],
            'pihak_dipanggil' => ['required', 'in:siswa,orang_tua,siswa_orang_tua'],
            'tujuan' => ['required', 'string'],
        ]);

        $laporan->update([
            'kategori' => $validated['kategori'],
            'jenis_masalah' => $validated['jenis_masalah'],
            'status' => 'pemanggilan',
        ]);

        Pemanggilan::create([
            'laporan_id' => $laporan->laporan_id,
            'nip' => $guruBk->nip,
            'tanggal_pemanggilan' => $validated['tanggal_pemanggilan'],
            'waktu_pemanggilan' => $validated['waktu_pemanggilan'],
            'pihak_dipanggil' => $validated['pihak_dipanggil'],
            'tujuan' => $validated['tujuan'],
            'status_kehadiran' => 'belum',
            'tindak_lanjut' => 'belum',
            'tanggal_monitoring' => null,
        ]);

        return redirect()
            ->route('bk.laporan.show', $laporan->laporan_id)
            ->with('success', 'Detail laporan dan jadwal pemanggilan berhasil disimpan.');
    }

    public function edit(string $id)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $laporan = Laporan::with('siswa')->findOrFail($id);

        if ($laporan->nip !== $guruBk->nip) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $kategori = self::KATEGORI;

        return view('bk.laporan.edit', compact('laporan', 'kategori'));
    }

    public function update(Request $request, string $id)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $laporan = Laporan::findOrFail($id);

        if ($laporan->nip !== $guruBk->nip) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah laporan ini.');
        }

        $validated = $request->validate([
            'kategori' => ['required', 'in:' . implode(',', self::KATEGORI)],
            'jenis_masalah' => ['required', 'string', 'max:150'],
            'status' => ['required', 'in:baru,pemanggilan,monitoring,selesai,dirujuk'],
        ]);

        $laporan->update([
            'kategori' => $validated['kategori'],
            'jenis_masalah' => $validated['jenis_masalah'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('bk.laporan.show', $laporan->laporan_id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $laporan = Laporan::findOrFail($id);

        if ($laporan->nip !== $guruBk->nip) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus laporan ini.');
        }

        $laporan->delete();

        return redirect()
            ->route('bk.laporan.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}