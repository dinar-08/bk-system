<?php

namespace App\Http\Middleware;

use App\Models\PeriodeUpdate;
use App\Models\Siswa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPeriodeUpdate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Untuk sementara role kamu masih orang_tua.
        // Kalau nanti role final sudah siswa, ganti orang_tua menjadi siswa.
        if ($user->role !== 'orang_tua') {
            return $next($request);
        }

        $periode = PeriodeUpdate::aktifSekarang();

        if (!$periode) {
            return $next($request);
        }

        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return $next($request);
        }

        $sudahUpdate = $siswa->sudahUpdateDiPeriode($periode);

        if ($sudahUpdate) {
            return $next($request);
        }

        // Route yang tetap boleh dibuka walaupun belum update data.
        if (
            $request->routeIs('profile.edit') ||
            $request->routeIs('profile.update') ||
            $request->routeIs('logout') ||
            $request->routeIs('password.change.*')
        ) {
            return $next($request);
        }

        return redirect()
            ->route('profile.edit')
            ->with(
                'warning',
                'Silakan lengkapi data profil terlebih dahulu sebelum menggunakan fitur ini.'
            );
    }
}