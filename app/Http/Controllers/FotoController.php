<?php

namespace App\Http\Controllers;

use App\Models\GuruBK;
use App\Models\Laporan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class FotoController extends Controller
{
    public function bukti(Laporan $laporan)
    {
        $user = auth()->user();
        $siswa = $laporan->siswa;

        if ($user->role === 'bk') {
            $guruBk = GuruBK::where('user_id', $user->id)->first();

            // Laporan masih "baru" (belum ada yang menangani) → semua BK boleh lihat
            // Laporan sudah diverifikasi/ditangani → hanya BK yang menangani (nip cocok)
            $boleh = $laporan->status === 'baru'
                || ($guruBk && $laporan->nip === $guruBk->nip);
        } else {
            $boleh = $user->role === 'orang_tua' && $siswa && $siswa->user_id === $user->id;
        }

        abort_unless($boleh, 403, 'Anda tidak punya akses ke file ini.');
        abort_unless($laporan->bukti && Storage::disk('local')->exists($laporan->bukti), 404);

        return Storage::disk('local')->response($laporan->bukti);
    }

    public function siswa(Siswa $siswa)
    {
        $user = auth()->user();

        $boleh = in_array($user->role, ['admin', 'bk'])
            || ($user->role === 'orang_tua' && $siswa->user_id === $user->id);

        abort_unless($boleh, 403, 'Anda tidak punya akses ke foto ini.');
        abort_unless($siswa->foto && Storage::disk('local')->exists($siswa->foto), 404);

        return Storage::disk('local')->response($siswa->foto);
    }

    public function guruBk(GuruBK $guruBk)
    {
        $user = auth()->user();

        abort_unless(in_array($user->role, ['admin', 'bk', 'orang_tua']), 403);
        abort_unless($guruBk->foto && Storage::disk('local')->exists($guruBk->foto), 404);

        return Storage::disk('local')->response($guruBk->foto);
    }

    public function user(User $user)
    {
        $auth = auth()->user();

        $boleh = $auth->id === $user->id || $auth->role === 'admin';

        abort_unless($boleh, 403);
        abort_unless($user->foto && Storage::disk('local')->exists($user->foto), 404);

        return Storage::disk('local')->response($user->foto);
    }
}