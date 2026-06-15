<?php
namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\Siswa;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    const KATEGORI = ['akademik', 'sosial', 'perilaku', 'emosional', 'lain-lain'];

    public function index()
    {
        $laporan = Laporan::with(['siswa', 'guruBk'])
            ->whereIn('status', ['baru', 'pemanggilan'])
            ->latest()->get();

        return view('bk.laporan.index', compact('laporan'));
    }

    public function create()
    {
        $siswa = Siswa::orderBy('nama_siswa')->get();
        $kategori = self::KATEGORI;
        return view('bk.laporan.create', compact('siswa', 'kategori'));
    }

    public function store(Request $request)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'judul_laporan' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'in:' . implode(',', self::KATEGORI)],
            'kategori_lain' => ['nullable', 'string', 'max:100', 'required_if:kategori,lain-lain'],
            'jenis_masalah' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            // Fix L3: tambah rekaman audio/video
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,mp3,mp4,mov,wav,m4a,ogg', 'max:51200'],
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti-laporan', 'public');
        }

        Laporan::create([
            'siswa_id' => $validated['siswa_id'],
            'guru_bk_id' => $guruBk->id,
            'judul_laporan' => $validated['judul_laporan'],
            'kategori' => $validated['kategori'] === 'lain-lain'
                ? 'lain-lain: ' . ($validated['kategori_lain'] ?? '')
                : $validated['kategori'],
            'jenis_masalah' => $validated['jenis_masalah'],
            'deskripsi' => $validated['deskripsi'],
            'bukti' => $buktiPath,
            'status' => 'baru',
        ]);

        return redirect()->route('bk.laporan.index')
            ->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(string $id)
    {
        $laporan = Laporan::with(['siswa', 'guruBk', 'pemanggilan'])->findOrFail($id);
        return view('bk.laporan.show', compact('laporan'));
    }

    public function edit(string $id)
    {
        $laporan = Laporan::with('siswa')->findOrFail($id);
        $kategori = self::KATEGORI;
        return view('bk.laporan.edit', compact('laporan', 'kategori'));
    }

    public function update(Request $request, string $id)
    {
        $guruBk = GuruBK::where('user_id', auth()->id())->firstOrFail();
        $laporan = Laporan::findOrFail($id);

        $validated = $request->validate([
            'kategori' => ['required', 'in:' . implode(',', self::KATEGORI)],
            'kategori_lain' => ['nullable', 'string', 'max:100', 'required_if:kategori,lain-lain'],
            'jenis_masalah' => ['required', 'string', 'max:150'],
            'status' => ['required', 'in:baru,pemanggilan,monitoring,selesai'],
        ]);

        $laporan->update([
            'guru_bk_id' => $guruBk->id,
            'kategori' => $validated['kategori'] === 'lain-lain'
                ? 'lain-lain: ' . ($validated['kategori_lain'] ?? '')
                : $validated['kategori'],
            'jenis_masalah' => $validated['jenis_masalah'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('bk.laporan.show', $laporan->id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Laporan::findOrFail($id)->delete();
        return redirect()->route('bk.laporan.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}

