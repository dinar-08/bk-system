<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\Siswa;
use App\Notifications\EvaluasiBaruNotification;
use App\Notifications\JadwalPemanggilanNotification;
use App\Notifications\LaporanBaruBkNotification;
use App\Notifications\LaporanBaruNotification;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('user_id', auth()->id())->firstOrFail();

        $laporanAktif = Laporan::with(['siswa', 'guruBk'])
            ->where('siswa_id', $siswa->id)
            ->whereIn('status', ['baru', 'pemanggilan', 'monitoring'])
            ->latest()
            ->get();

        $laporanRiwayat = Laporan::with(['siswa', 'guruBk'])
            ->where('siswa_id', $siswa->id)
            ->whereIn('status', ['selesai', 'dirujuk'])
            ->latest()
            ->get();

        $adaLaporanBerjalan = Laporan::where('siswa_id', $siswa->id)
            ->whereIn('status', ['baru', 'pemanggilan', 'monitoring'])
            ->exists();

        $this->tandaiNotifikasiLaporanDibaca();

        return view('orang-tua.laporan.index', compact(
            'laporanAktif',
            'laporanRiwayat',
            'adaLaporanBerjalan',
            'siswa'
        ));
    }

    public function create()
    {
        $siswa = Siswa::where('user_id', auth()->id())->firstOrFail();

        $kasusAktif = Laporan::where('siswa_id', $siswa->id)
            ->whereIn('status', ['baru', 'pemanggilan', 'monitoring'])
            ->exists();

        if ($kasusAktif) {
            return redirect()->route('orang_tua.laporan.index')
                ->with('warning', 'Anda tidak dapat membuat laporan baru karena masih ada kasus yang sedang ditangani oleh BK.');
        }

        return view('orang-tua.laporan.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $siswa = Siswa::where('user_id', auth()->id())->firstOrFail();

        $kasusAktif = Laporan::where('siswa_id', $siswa->id)
            ->whereIn('status', ['baru', 'pemanggilan', 'monitoring'])
            ->exists();

        if ($kasusAktif) {
            return redirect()->route('orang_tua.laporan.index')
                ->with('warning', 'Masih ada kasus yang sedang berjalan.');
        }

        $validated = $request->validate([
            'judul_laporan' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,mp3,mp4,mov,wav,m4a,ogg', 'max:51200'],
        ], [
            'judul_laporan.required' => 'Judul laporan wajib diisi.',
            'judul_laporan.max' => 'Judul laporan maksimal 150 karakter.',
            'deskripsi.required' => 'Deskripsi laporan wajib diisi.',
            'bukti.file' => 'File yang diunggah tidak valid.',
            'bukti.mimes' => 'Format file tidak didukung. Gunakan JPG, PNG, PDF, MP3, MP4, MOV, atau WAV.',
            'bukti.max' => 'Ukuran file maksimal 50MB.',
        ]);

        $buktiPath = null;

        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti-laporan', 'local');
        }

        $laporan = Laporan::create([
            'siswa_id' => $siswa->id,
            'guru_bk_id' => null,
            'judul_laporan' => $validated['judul_laporan'],
            'kategori' => null,
            'jenis_masalah' => null,
            'deskripsi' => $validated['deskripsi'],
            'bukti' => $buktiPath,
            'status' => 'baru',
        ]);

        $laporan->load('siswa');

        GuruBK::with('user')->get()->each(function ($guruBk) use ($laporan) {
            if ($guruBk->user) {
                $guruBk->user->notify(new LaporanBaruBkNotification($laporan));
            }
        });

        return redirect()->route('orang_tua.laporan.index')
            ->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(string $id)
    {
        $siswa = Siswa::where('user_id', auth()->id())->firstOrFail();

        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'pemanggilan',
            'monitoring',
            'evaluasi',
        ])
            ->where('siswa_id', $siswa->id)
            ->findOrFail($id);

        $this->tandaiNotifikasiLaporanDibaca();

        return view('orang-tua.laporan.show', compact('laporan', 'siswa'));
    }

    protected function tandaiNotifikasiLaporanDibaca(): void
    {
        auth()->user()->unreadNotifications()
            ->whereIn('type', [
                LaporanBaruNotification::class,
                EvaluasiBaruNotification::class,
                JadwalPemanggilanNotification::class,
            ])
            ->update(['read_at' => now()]);
    }
}