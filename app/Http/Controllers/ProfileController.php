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

        return view('profile.edit', [
            'user' => $user,
            'dataProfil' => $dataProfil,
        ]);
    }
    public function editSiswa(Request $request)
    {
        $user = $request->user();

        $siswa = Siswa::where('user_id', $user->id)
            ->firstOrFail();

        $periode = PeriodeUpdate::aktifSekarang();

        return view('orang-tua.update-data', compact(
            'user',
            'siswa',
            'periode'
        ));
    }
    public function updateSiswa(Request $request)
    {
        $user = $request->user();

        $siswa = Siswa::where('user_id', $user->id)
            ->firstOrFail();

        $request->validate([
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
            'nama_siswa' => $request->nama_siswa,
            'kelas' => $request->kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'nama_ortu' => $request->nama_ortu,
            'no_whatsapp' => $request->no_whatsapp,
            'last_data_updated_at' => now(),
        ];

        if ($request->hasFile('foto')) {

            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }

            $updateData['foto'] = $request
                ->file('foto')
                ->store('foto-profil', 'public');
        }

        $siswa->update($updateData);

        return redirect()
            ->route('orang_tua.dashboard')
            ->with('success', 'Data berhasil diperbarui.');
    }
    public function showChangePassword()
    {
        if (!auth()->user()->must_change_password) {
            return redirect()->route('profile.edit');
        }

        return view('auth.change-password-first');
    }
    public function processChangePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()
            ->to($this->redirectAfterRole($user->role))
            ->with(
                'success',
                'Password berhasil diganti. Selamat datang!'
            );
    }


    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::min(8)],

            'nama_siswa' => ['nullable', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'nama_ortu' => ['nullable', 'string', 'max:150'],
            'no_whatsapp' => ['nullable', 'string', 'max:20'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $updateUser = [
            'name' => $request->name,
        ];

        if ($request->filled('password')) {
            $updateUser['password'] = Hash::make($request->password);
        }

        if ($user->role === 'admin' && $request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            $updateUser['foto'] = $request->file('foto')->store('foto-profil', 'public');
        }

        $user->update($updateUser);

        if ($user->role === 'bk') {
            $profil = GuruBK::where('user_id', $user->id)->first();

            if ($profil) {
                $updateProfil = [
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat,
                ];

                if ($request->hasFile('foto')) {
                    if ($profil->foto) {
                        Storage::disk('public')->delete($profil->foto);
                    }

                    $updateProfil['foto'] = $request->file('foto')->store('foto-profil', 'public');
                }

                $profil->update($updateProfil);
            }
        }

        if ($user->role === 'orang_tua') {
            $profil = Siswa::where('user_id', $user->id)->first();

            if ($profil) {
                $updateProfil = [
                    'nama_siswa' => $request->nama_siswa,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat' => $request->alamat,
                    'kelas' => $request->kelas,
                    'nama_ortu' => $request->nama_ortu,
                    'no_whatsapp' => $request->no_whatsapp,
                ];

                if ($request->hasFile('foto')) {
                    if ($profil->foto) {
                        Storage::disk('public')->delete($profil->foto);
                    }

                    $updateProfil['foto'] = $request->file('foto')->store('foto-profil', 'public');
                }

                $profil->update($updateProfil);

                $user->update([
                    'name' => $request->nama_siswa,
                ]);
            }
        }

        return redirect()
            ->route('profile.edit')
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