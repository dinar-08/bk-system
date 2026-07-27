<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruBK;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GuruBKController extends Controller
{
    public function index()
    {
        $guruBk = GuruBK::with('user')->latest()->get();

        return view('admin.guru-bk.index', compact('guruBk'));
    }

    public function create()
    {
        return view('admin.guru-bk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nip' => ['required', 'string', 'max:50', 'unique:guru_bk,nip', 'unique:users,username'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
            'foto' => ['nullable', 'image', 'max:2048'],
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['nama'],
                'username' => $validated['nip'],
                'role' => 'bk',
                'password' => Hash::make($validated['password']),
            ]);

            $fotoPath = $request->hasFile('foto')
                ? $request->file('foto')->store('guru-bk', 'local')
                : null;

            GuruBK::create([
                'user_id' => $user->id,
                'nama' => $validated['nama'],
                'nip' => $validated['nip'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'] ?? null,
                'foto' => $fotoPath,
            ]);
        });

        return redirect()
            ->route('admin.guru-bk.index')
            ->with('success', 'Data Guru BK berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $guruBk = GuruBK::with('user')->findOrFail($id);

        return view('admin.guru-bk.show', compact('guruBk'));
    }

    public function edit(string $id)
    {
        $guruBk = GuruBK::with('user')->findOrFail($id);

        return view('admin.guru-bk.edit', compact('guruBk'));
    }

    public function update(Request $request, string $id)
    {
        $guruBk = GuruBK::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nip' => ['required', 'string', 'max:50', 'unique:guru_bk,nip,' . $guruBk->nip . ',nip', 'unique:users,username,' . $guruBk->user_id],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
            'foto' => ['nullable', 'image', 'max:2048'],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        DB::transaction(function () use ($guruBk, $validated, $request) {
            $dataUpdate = [
                'nama' => $validated['nama'],
                'nip' => $validated['nip'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'] ?? null,
            ];

            if ($request->hasFile('foto')) {
                if ($guruBk->foto && Storage::disk('local')->exists($guruBk->foto)) {
                    Storage::disk('local')->delete($guruBk->foto);
                }
                $dataUpdate['foto'] = $request->file('foto')->store('guru-bk', 'local');
            }

            $guruBk->update($dataUpdate);

            $userData = [
                'name' => $validated['nama'],
                'username' => $validated['nip'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $guruBk->user->update($userData);
        });

        return redirect()
            ->route('admin.guru-bk.index')
            ->with('success', 'Data Guru BK berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $guruBk = GuruBK::with('user')->findOrFail($id);

        if ($guruBk->foto && Storage::disk('local')->exists($guruBk->foto)) {
            Storage::disk('local')->delete($guruBk->foto);
        }

        if ($guruBk->user) {
            $guruBk->user->delete();
        }

        return redirect()
            ->route('admin.guru-bk.index')
            ->with('success', 'Data Guru BK berhasil dihapus.');
    }
}