<?php

namespace App\Http\Controllers;

use App\Models\GuruBK;
use App\Models\Siswa;
use App\Models\PeriodeUpdate;
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

        if ($user->role === 'bk') {
            $dataProfil = GuruBK::where('user_id', $user->id)->first();
        } elseif ($user->role === 'orang_tua') {
            $dataProfil = Siswa::where('user_id', $user->id)->first();
        }

        return view('profile.edit', compact('user', 'dataProfil'));
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

        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            // Orang tua
            'nama_siswa' => ['nullable', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'nama_ortu' => ['nullable', 'string', 'max:150'],
            'no_whatsapp' => ['nullable', 'string', 'max:20'],
            // BK
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $updateUser = ['name' => $request->name];

        if ($request->filled('password')) {
            $updateUser['password'] = Hash::make($request->password);
        }

        // Handle foto — simpan ke disk, update di users
        if ($request->hasFile('foto')) {
            if ($user->foto)
                Storage::disk('public')->delete($user->foto);
            $fotoPath = $request->file('foto')->store('foto-profil', 'public');
            $updateUser['foto'] = $fotoPath;
        }

        $user->update($updateUser);

        // Update tabel guru_bk
        if ($user->role === 'bk') {
            $profil = GuruBK::where('user_id', $user->id)->first();
            if ($profil) {
                $updateProfil = [
                    'nama' => $request->name,
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat,
                ];
                // Foto BK disimpan ke guru_bk juga (sama path)
                if (isset($fotoPath))
                    $updateProfil['foto'] = $fotoPath;
                $profil->update($updateProfil);
            }
        }

        // Update tabel siswa (orang tua)
        if ($user->role === 'orang_tua') {
            $profil = Siswa::where('user_id', $user->id)->first();
            if ($profil) {
                $updateProfil = [
                    'nama_siswa' => $request->nama_siswa ?? $request->name,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat' => $request->alamat,
                    'kelas' => $request->kelas,
                    'nama_ortu' => $request->nama_ortu,
                    'no_whatsapp' => $request->no_whatsapp,
                ];
                if (isset($fotoPath))
                    $updateProfil['foto'] = $fotoPath;
                $profil->update($updateProfil);
                // Sinkron nama siswa ke users
                $user->update(['name' => $request->nama_siswa ?? $request->name]);
            }
        }

        return redirect()->route('profile.edit')
            ->with('status', 'Profil berhasil diperbarui.');
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
