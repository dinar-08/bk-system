<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with('user')
            ->orderBy('kelas')
            ->orderBy('nama_siswa')
            ->get();

        $daftarKelas = Siswa::select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $aktifSekarang = PeriodeUpdate::aktifSekarang();

        $siswaAktif = $siswa->filter(function ($item) {
            return ($item->user->status_akun ?? 'aktif') === 'aktif';
        });

        $totalSiswaAktif = $siswaAktif->count();
        $totalSudahUpdate = 0;
        $totalBelumUpdate = 0;

        if ($aktifSekarang) {
            $totalSudahUpdate = $siswaAktif
                ->filter(fn($item) => $item->sudahUpdateDiPeriode($aktifSekarang))
                ->count();

            $totalBelumUpdate = $totalSiswaAktif - $totalSudahUpdate;
        }

        return view('admin.siswa.index', compact(
            'siswa',
            'daftarKelas',
            'aktifSekarang',
            'totalSiswaAktif',
            'totalSudahUpdate',
            'totalBelumUpdate'
        ));
    }

    public function create()
    {
        return view('admin.siswa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:50', 'unique:siswa,nis', 'unique:users,username'],
            'nama_siswa' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($validated) {
            $password = $this->generatePassword();

            $user = User::create([
                'name' => $validated['nama_siswa'],
                'username' => $validated['nis'],
                'role' => 'orang_tua',
                'status_akun' => 'aktif',
                'password' => Hash::make($password),
                'default_password' => $password,
                'must_change_password' => true,
            ]);

            $periodeAktif = PeriodeUpdate::aktifSekarang() ?? PeriodeUpdate::terkini();

            Siswa::create([
                'user_id' => $user->id,
                'nis' => $validated['nis'],
                'nama_siswa' => $validated['nama_siswa'],
                'kelas' => $validated['kelas'],
                'tahun_ajaran' => optional($periodeAktif)->tahun_ajaran,
            ]);

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
            'nis' => [
                'required',
                'string',
                'max:50',
                'unique:siswa,nis,' . $siswa->nis . ',nis',
                'unique:users,username,' . $siswa->user_id,
            ],
            'nama_siswa' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_whatsapp' => ['nullable', 'string', 'max:20'],
            'nama_ortu' => ['nullable', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($siswa, $validated) {
            $siswa->update([
                'nis' => $validated['nis'],
                'nama_siswa' => $validated['nama_siswa'],
                'kelas' => $validated['kelas'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'no_whatsapp' => $validated['no_whatsapp'] ?? null,
                'nama_ortu' => $validated['nama_ortu'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);

            if ($siswa->user) {
                $dataUser = [
                    'name' => $validated['nama_siswa'],
                    'username' => $validated['nis'],
                ];

                if (!empty($validated['password'])) {
                    $dataUser['password'] = Hash::make($validated['password']);
                    $dataUser['default_password'] = $validated['password'];
                    $dataUser['must_change_password'] = true;
                }

                $siswa->user->update($dataUser);
            }
        });

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        if ($siswa->user) {
            $sedangAktif = ($siswa->user->status_akun ?? 'aktif') === 'aktif';
            $statusBaru = $sedangAktif ? 'nonaktif' : 'aktif';

            // Hanya cek kasus aktif kalau ini proses NONAKTIFKAN (bukan aktifkan lagi)
            if ($statusBaru === 'nonaktif') {
                $kasusAktif = Laporan::where('nis', $siswa->nis)
                    ->whereIn('status', ['baru', 'pemanggilan', 'monitoring'])
                    ->get();

                if ($kasusAktif->isNotEmpty()) {
                    return redirect()->route('admin.siswa.index')
                        ->with('error_nonaktifkan_siswa', [
                            'nama' => $siswa->nama_siswa,
                            'kelas' => $siswa->kelas,
                            'kasus' => $kasusAktif->map(fn($laporan) => [
                                'judul' => $laporan->judul_laporan,
                                'status' => $laporan->status,
                            ])->toArray(),
                        ]);
                }
            }

            $siswa->user->update([
                'status_akun' => $statusBaru,
                'nonaktif_at' => $statusBaru === 'nonaktif' ? now() : null,
            ]);

            $siswa->update([
                'tahun_ajaran' => $statusBaru === 'nonaktif'
                    ? optional(PeriodeUpdate::terkini())->tahun_ajaran
                    : null,
            ]);
        }

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Status siswa berhasil diperbarui.');
    }

    public function nonaktifkanKelas(Request $request)
    {
        $validated = $request->validate([
            'kelas' => ['required', 'string'],
        ]);

        $siswaDikelas = Siswa::with('user')
            ->where('kelas', $validated['kelas'])
            ->get();

        $periodeTerkini = PeriodeUpdate::terkini();
        $dilewati = [];
        $jumlahBerhasil = 0;

        foreach ($siswaDikelas as $item) {
            // Lewati siswa yang memang sudah nonaktif duluan
            if (!$item->user || ($item->user->status_akun ?? 'aktif') !== 'aktif') {
                continue;
            }

            $kasusAktif = Laporan::where('nis', $item->nis)
                ->whereIn('status', ['baru', 'pemanggilan', 'monitoring'])
                ->get();

            if ($kasusAktif->isNotEmpty()) {
                $dilewati[] = [
                    'nama' => $item->nama_siswa,
                    'kasus' => $kasusAktif->map(fn($laporan) => [
                        'judul' => $laporan->judul_laporan,
                        'status' => $laporan->status,
                    ])->toArray(),
                ];
                continue;
            }

            $item->user->update([
                'status_akun' => 'nonaktif',
                'nonaktif_at' => now(),
            ]);

            $item->update([
                'tahun_ajaran' => optional($periodeTerkini)->tahun_ajaran,
            ]);

            $jumlahBerhasil++;
        }

        $redirect = redirect()->route('admin.siswa.index')
            ->with('success', $jumlahBerhasil . ' akun siswa kelas ' . $validated['kelas'] . ' berhasil dinonaktifkan.');

        if (!empty($dilewati)) {
            $redirect->with('info_nonaktifkan_kelas', [
                'kelas' => $validated['kelas'],
                'dilewati' => $dilewati,
            ]);
        }

        return $redirect;
    }

    public function updateKelasMassal(Request $request)
    {
        $validated = $request->validate([
            'kelas_lama' => ['required', 'string'],
            'kelas_baru' => ['required', 'string'],
        ]);

        $jumlah = Siswa::where('kelas', $validated['kelas_lama'])
            ->update([
                'kelas' => $validated['kelas_baru'],
            ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', $jumlah . ' siswa berhasil dipindahkan dari kelas ' . $validated['kelas_lama'] . ' ke ' . $validated['kelas_baru'] . '.');
    }

    public function download(Request $request)
    {
        $siswa = Siswa::with('user')
            ->when($request->input('kelas'), fn($q) => $q->where('kelas', $request->input('kelas')))
            ->when($request->input('status'), function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('status_akun', $request->input('status')));
            })
            ->orderBy('kelas')
            ->orderBy('nama_siswa')
            ->get();

        $pdf = Pdf::loadView('admin.siswa.download.download-pdf', [
            'siswa' => $siswa,
            'tanggal' => now('Asia/Jakarta')->format('d-m-Y H:i'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('data-siswa-' . now()->format('Ymd-His') . '.pdf');
    }

    private function generatePassword(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return substr(str_shuffle(str_repeat($chars, 4)), 0, 8);
    }
}