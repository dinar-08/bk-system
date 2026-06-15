<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;

class CekPeriodeUpdate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login, lanjutkan
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Middleware hanya berlaku untuk orang tua
        if ($user->role !== 'orang_tua') {
            return $next($request);
        }

        // Cari periode update yang sedang aktif
        $periode = PeriodeUpdate::where('aktif', true)
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->first();

        // Jika tidak ada periode aktif, lanjutkan
        if (!$periode) {
            return $next($request);
        }

        // Cari data siswa berdasarkan user login
        $siswa = Siswa::where('user_id', $user->id)->first();

        // Jika siswa tidak ditemukan, lanjutkan
        if (!$siswa) {
            return $next($request);
        }

        // Cek apakah siswa sudah update data pada periode ini
        $sudahUpdate =
            $siswa->last_data_updated_at &&
            $siswa->last_data_updated_at >= $periode->tanggal_mulai;

        // Jika belum update
        if (!$sudahUpdate) {

            // Batasi hanya fitur tertentu
            if (
                $request->routeIs('orang_tua.laporan.*') ||
                $request->routeIs('orang_tua.perkembangan.*')
            ) {
                return redirect()
                    ->route('orang_tua.update-data')
                    ->with(
                        'warning',
                        'Anda wajib memperbarui data diri terlebih dahulu sebelum menggunakan fitur ini. '
                        . 'Periode update: '
                        . $periode->tanggal_mulai
                        . ' s/d '
                        . $periode->tanggal_selesai
                    );
            }
        }

        return $next($request);
    }
}