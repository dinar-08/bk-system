<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (auth()->check()) {

            // Cek apakah user wajib mengganti password
            if (auth()->user()->must_change_password) {

                // Izinkan hanya halaman ganti password dan logout
                if (
                    !$request->routeIs('password.change.*') &&
                    !$request->routeIs('logout')
                ) {
                    return redirect()
                        ->route('password.change.form')
                        ->with(
                            'info',
                            'Anda harus mengganti password sebelum melanjutkan.'
                        );
                }
            }
        }

        return $next($request);
    }
}