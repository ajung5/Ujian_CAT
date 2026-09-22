<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware {
    /**
     * Batasi route berdasarkan status legacy.
     *
     * Contoh:
     * role:A,G
     * role:S,C
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (in_array($user->status, $roles, true)) {
            return $next($request);
        }

        /*
         * Pertahankan behavior aplikasi lama:
         *
         * Guru/Admin yang mencoba area siswa -> guru
         * Siswa/Calon siswa yang mencoba area guru -> siswa
         */
        return match ($user->status) {
            'A', 'G' => redirect('/guru'),
            'S', 'C' => redirect('/siswa'),
            default => abort(403, 'Role pengguna tidak dikenali.'),
        };
    }
}
