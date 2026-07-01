<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class MustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (auth()->user()->must_change_password) {
                // Izinkan hanya halaman profil dan logout
                if (
                    !$request->routeIs('profile.*') &&
                    !$request->routeIs('logout')
                ) {
                    return redirect()
                        ->route('profile.edit')
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