<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Exports\SiswaExport;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with('user')->latest()->get();
        $daftarKelas = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        return view('admin.siswa.index', compact('siswa', 'daftarKelas'));
    }

    public function create()
    {
        return view('admin.siswa.create');
    }

    // A2: form disederhanakan — hanya NIS, nama, kelas
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:50', 'unique:siswa,nis', 'unique:users,username'],
            'nama_siswa' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($validated) {
            // Generate password random huruf+angka 8 karakter
            $password = $this->generatePassword();

            $user = User::create([
                'name' => $validated['nama_siswa'],
                'username' => $validated['nis'],
                'role' => 'orang_tua',
                'password' => Hash::make($password),
                'must_change_password' => true,
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'nis' => $validated['nis'],
                'nama_siswa' => $validated['nama_siswa'],
                'kelas' => $validated['kelas'],
            ]);

            // Simpan password plain ke session untuk ditampilkan sekali
            session()->flash('password_baru', $password);
            session()->flash('nama_siswa_baru', $validated['nama_siswa']);
        });

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Akun siswa berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, string $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:50', 'unique:siswa,nis,' . $siswa->id, 'unique:users,username,' . $siswa->user_id],
            'nama_siswa' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($siswa, $validated) {
            $siswa->update([
                'nis' => $validated['nis'],
                'nama_siswa' => $validated['nama_siswa'],
                'kelas' => $validated['kelas'],
            ]);
            $siswa->user->update([
                'name' => $validated['nama_siswa'],
                'username' => $validated['nis'],
            ]);
        });

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        if ($siswa->user) {
            $statusBaru = ($siswa->user->status_akun ?? 'aktif') === 'aktif' ? 'nonaktif' : 'aktif';
            $siswa->user->update(['status_akun' => $statusBaru]);
        }

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Status akun siswa berhasil diperbarui.');
    }

    // Fix X2 + bug X11: cek monitoring aktif sebelum nonaktifkan
    public function nonaktifkanKelas(Request $request)
    {
        $validated = $request->validate(['kelas' => ['required', 'string']]);

        $siswaDikelas = Siswa::with('user')->where('kelas', $validated['kelas'])->get();

        // Cek kasus monitoring aktif
        $siswaIds = $siswaDikelas->pluck('id');
        $kasusAktif = Laporan::whereIn('siswa_id', $siswaIds)
            ->whereIn('status', ['baru', 'pemanggilan', 'monitoring'])
            ->with('siswa')
            ->get();

        if ($kasusAktif->isNotEmpty()) {
            return redirect()->route('admin.siswa.index')
                ->with('error_nonaktifkan', [
                    'kelas' => $validated['kelas'],
                    'kasus' => $kasusAktif->map(fn($l) => [
                        'nama' => $l->siswa->nama_siswa ?? '-',
                        'judul' => $l->judul_laporan,
                        'status' => $l->status,
                    ])->toArray(),
                ]);
        }

        foreach ($siswaDikelas as $item) {
            if ($item->user) {
                $item->user->update(['status_akun' => 'nonaktif']);
            }
        }

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Semua akun kelas ' . $validated['kelas'] . ' berhasil dinonaktifkan.');
    }

    // A3: update kelas massal
    public function updateKelasMassal(Request $request)
    {
        $validated = $request->validate([
            'kelas_lama' => ['required', 'string'],
            'kelas_baru' => ['required', 'string'],
        ]);

        $jumlah = Siswa::where('kelas', $validated['kelas_lama'])
            ->update(['kelas' => $validated['kelas_baru']]);

        return redirect()->route('admin.siswa.index')
            ->with('success', $jumlah . ' siswa berhasil dipindahkan dari kelas ' . $validated['kelas_lama'] . ' ke ' . $validated['kelas_baru'] . '.');
    }

    // A6: download data siswa
    public function download(Request $request)
    {
        $tipe = $request->query('tipe', 'siswa');
        return (new SiswaExport($tipe))->download();
    }

    private function generatePassword(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle(str_repeat($chars, 4)), 0, 8);
    }
}