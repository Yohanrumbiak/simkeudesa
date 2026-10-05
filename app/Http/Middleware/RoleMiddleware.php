<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi Administrator.');
        }

        if (!in_array($user->role, $roles, true)) {
            // Redirect to appropriate dashboard with alert
            $route = match ($user->role) {
                'admin' => 'staff.dashboard',
                'pimpinan' => 'pimpinan.dashboard',
                'kepala_desa' => 'desa.dashboard',
                default => 'login',
            };

            return redirect()->route($route)->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
