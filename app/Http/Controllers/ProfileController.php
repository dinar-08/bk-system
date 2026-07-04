<?php

namespace App\Http\Controllers;

use App\Models\GuruBK;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        $dataProfil = null;
        $periode = null;
        $wajibUpdate = false;

        if ($user->role === 'bk') {
            $dataProfil = GuruBK::where('user_id', $user->id)->first();
        }

        if ($user->role === 'orang_tua') {
            $dataProfil = Siswa::where('user_id', $user->id)->first();
            $periode = PeriodeUpdate::aktifSekarang();

            if ($periode && $dataProfil) {
                $wajibUpdate = !$dataProfil->sudahUpdateDiPeriode($periode);
            }
        }

        return view('profile.edit', compact(
            'user',
            'dataProfil',
            'periode',
            'wajibUpdate'
        ));
    }

    // Halaman update data siswa (dipanggil saat periode wajib update)
    public function editSiswa(Request $request)
    {
        $user = $request->user();
        $siswa = Siswa::where('user_id', $user->id)->firstOrFail();
        $periode = PeriodeUpdate::aktifSekarang();

        return view('orang-tua.update-data', compact('user', 'siswa', 'periode'));
    }

    public function updateSiswa(Request $request)
    {
        $user = $request->user();
        $siswa = Siswa::where('user_id', $user->id)->firstOrFail();
        $periode = PeriodeUpdate::aktifSekarang();

        $validated = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'nama_ortu' => ['required', 'string', 'max:150'],
            'no_whatsapp' => ['required', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $updateData = [
            'nama_siswa' => $validated['nama_siswa'],
            'kelas' => $validated['kelas'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'nama_ortu' => $validated['nama_ortu'],
            'no_whatsapp' => $validated['no_whatsapp'],
            // Catat tahun ajaran dari periode update yang sedang aktif,
            // supaya kolom ini ikut terisi otomatis saat orang tua update data.
            'tahun_ajaran' => $periode->tahun_ajaran ?? $siswa->tahun_ajaran,
            'last_data_updated_at' => now(),
        ];

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto)
                Storage::disk('public')->delete($siswa->foto);
            $fotoPath = $request->file('foto')->store('foto-profil', 'public');
            $updateData['foto'] = $fotoPath;
            // Sinkron ke tabel users juga
            $user->update(['foto' => $fotoPath]);
        }

        $siswa->update($updateData);

        if ($periode) {
            $siswa->tandaiSudahUpdate($periode);
        }

        // Sinkron nama ke users
        $user->update(['name' => $validated['nama_siswa']]);

        return redirect()->route('orang_tua.dashboard')
            ->with('success', 'Data berhasil diperbarui.');
    }

    // Halaman ganti password pertama kali (must_change_password = true)
    public function showChangePassword()
    {
        if (!auth()->check())
            return redirect()->route('login');

        // Jika sudah tidak wajib ganti, redirect ke dashboard sesuai role
        if (!auth()->user()->must_change_password) {
            return redirect()->to($this->redirectAfterRole(auth()->user()->role));
        }

        return view('auth.change-password-first');
    }

    public function processChangePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password harus mengandung angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->to($this->redirectAfterRole($user->role))
            ->with('success', 'Password berhasil diganti. Selamat datang!');
    }

    // Update profil umum (admin, BK, orang tua via halaman /profile)
    public function update(Request $request)
    {
        $user = $request->user();

        // Kosongkan password jika tidak diisi
        if (trim((string) $request->input('password')) === '') {
            $request->merge([
                'password' => null,
                'password_confirmation' => null,
            ]);
        }

        // Tentukan apakah kondisi wajib isi semua field
        $periode = null;
        $wajibUpdate = false;
        $siswa = null;

        if ($user->role === 'orang_tua') {
            $siswa = Siswa::where('user_id', $user->id)->first();
            $periode = PeriodeUpdate::aktifSekarang();

            if ($periode && $siswa) {
                $wajibUpdate = !$siswa->sudahUpdateDiPeriode($periode);
            }
        }

        // Kondisi wajib isi semua field:
        // 1. Login pertama (must_change_password = true)
        // 2. Periode update aktif dan belum update
        $wajibIsiLengkap = $user->role === 'orang_tua' &&
            ($user->must_change_password || $wajibUpdate);

        // ── Validasi ──────────────────────────────────────────────────
        $rules = [
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];

        if ($wajibIsiLengkap) {
            // Foto wajib diisi jika belum ada foto
            $rules['foto'] = [$siswa && $siswa->foto ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];
            $rules['nama_siswa'] = ['required', 'string', 'max:150'];
            $rules['jenis_kelamin'] = ['required', 'in:L,P'];
            $rules['tanggal_lahir'] = ['required', 'date'];
            $rules['alamat'] = ['required', 'string'];
            $rules['kelas'] = ['required', 'string', 'max:50'];
            $rules['nama_ortu'] = ['required', 'string', 'max:150'];
            $rules['no_whatsapp'] = ['required', 'string', 'max:20'];
        } else {
            $rules['nama_siswa'] = ['nullable', 'string', 'max:150'];
            $rules['jenis_kelamin'] = ['nullable', 'in:L,P'];
            $rules['tanggal_lahir'] = ['nullable', 'date'];
            $rules['alamat'] = ['nullable', 'string'];
            $rules['kelas'] = ['nullable', 'string', 'max:50'];
            $rules['nama_ortu'] = ['nullable', 'string', 'max:150'];
            $rules['no_whatsapp'] = ['nullable', 'string', 'max:20'];
        }

        if ($user->role !== 'orang_tua') {
            $rules['name'] = ['required', 'string', 'max:150'];
            $rules['no_hp'] = ['nullable', 'string', 'max:20'];
            $rules['alamat'] = ['nullable', 'string'];
        }

        if ($user->must_change_password) {
            $rules['password'] = ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()];
        } elseif ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        }

        $request->validate($rules, [
            'foto.required' => 'Foto profil wajib diunggah.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'nama_ortu.required' => 'Nama orang tua wajib diisi.',
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'password.required' => 'Anda wajib mengganti password sebelum melanjutkan.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password harus mengandung angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // ── Update tabel users ────────────────────────────────────────
        $updateUser = [];

        if ($user->role === 'orang_tua') {
            $updateUser['name'] = $request->nama_siswa ?? $user->name;
        } else {
            $updateUser['name'] = $request->name;
        }

        if ($request->filled('password')) {
            $updateUser['password'] = Hash::make($request->password);
            $updateUser['default_password'] = null;
            $updateUser['must_change_password'] = false;
        }

        // Foto untuk admin/bk disimpan di users
        if ($user->role !== 'orang_tua' && $request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $updateUser['foto'] = $request->file('foto')->store('foto-profil', 'public');
        }

        $user->update($updateUser);

        // ── Update tabel siswa (khusus orang_tua) ────────────────────
        if ($user->role === 'orang_tua' && $siswa) {
            $updateSiswa = [
                'nama_siswa' => $request->nama_siswa ?? $siswa->nama_siswa,
                'jenis_kelamin' => $request->jenis_kelamin ?? $siswa->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir ?? $siswa->tanggal_lahir,
                'alamat' => $request->alamat ?? $siswa->alamat,
                'kelas' => $request->kelas ?? $siswa->kelas,
                'nama_ortu' => $request->nama_ortu ?? $siswa->nama_ortu,
                'no_whatsapp' => $request->no_whatsapp ?? $siswa->no_whatsapp,
                // Catat tahun ajaran dari periode update yang sedang aktif,
                // supaya kolom ini ikut terisi otomatis saat orang tua update data.
                'tahun_ajaran' => $periode->tahun_ajaran ?? $siswa->tahun_ajaran,
            ];

            // Foto untuk orang_tua disimpan di siswa
            if ($request->hasFile('foto')) {
                if ($siswa->foto) {
                    Storage::disk('public')->delete($siswa->foto);
                }
                $updateSiswa['foto'] = $request->file('foto')->store('foto-profil', 'public');
            }

            $siswa->update($updateSiswa);

            if ($periode) {
                $siswa->tandaiSudahUpdate($periode);
            }
        }

        // ── Update tabel guru_bk (khusus bk) ─────────────────────────
        if ($user->role === 'bk') {
            $gurubk = GuruBK::where('user_id', $user->id)->first();
            if ($gurubk) {
                $updateGurubk = [
                    'nama' => $request->name ?? $gurubk->nama,
                    'no_hp' => $request->no_hp ?? $gurubk->no_hp,
                    'alamat' => $request->alamat ?? $gurubk->alamat,
                ];

                // Foto BK disimpan di guru_bk jika ada kolomnya,
                // kalau tidak ada hapus baris ini
                if ($request->hasFile('foto')) {
                    if ($gurubk->foto) {
                        Storage::disk('public')->delete($gurubk->foto);
                    }
                    $updateGurubk['foto'] = $request->file('foto')->store('foto-profil', 'public');
                }

                $gurubk->update($updateGurubk);
            }
        }

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    private function redirectAfterRole(string $role): string
    {
        return match ($role) {
            'admin' => route('admin.dashboard'),
            'bk' => route('bk.dashboard'),
            'orang_tua' => route('orang_tua.dashboard'),
            default => '/',
        };
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }
}
