<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleStudentSession {
    /**
     * Pastikan akun siswa hanya mempunyai satu session aktif.
     *
     * Login siswa terbaru menjadi session yang sah.
     * Session lama akan dipaksa logout pada request berikutnya.
     *
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response {
        $user = Auth::user();

        if (!($user instanceof User) || $user->status !== 'S') {
            return $next($request);
        }

        $activeSessionHash = (string) ($user->active_session_hash ?? '');

        $studentSessionToken = (string) $request->session()->get('student_session_token', '');

        /*
         * Kompatibilitas untuk session siswa lama
         * sebelum fitur single-session diterapkan.
         *
         * Jika belum ada active_session_hash,
         * session saat ini menjadi session aktif pertama.
         */
        if ($activeSessionHash === '') {
            if ($studentSessionToken === '') {
                $studentSessionToken = bin2hex(random_bytes(32));

                $request->session()->put('student_session_token', $studentSessionToken);
            }

            $user
                ->forceFill([
                    'active_session_hash' => hash('sha256', $studentSessionToken),
                ])
                ->saveQuietly();

            return $next($request);
        }

        /*
         * Jika database sudah memiliki session aktif,
         * request juga wajib mempunyai token session.
         */
        if ($studentSessionToken === '') {
            return $this->terminateStudentSession($request);
        }

        $currentSessionHash = hash('sha256', $studentSessionToken);

        /*
         * Hash berbeda berarti ada login siswa
         * yang lebih baru dari perangkat/session lain.
         */
        if (!hash_equals($activeSessionHash, $currentSessionHash)) {
            return $this->terminateStudentSession($request);
        }

        return $next($request);
    }

    /**
     * Akhiri session siswa yang sudah tidak sah.
     */
    private function terminateStudentSession(Request $request): RedirectResponse {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Sesi Anda telah berakhir karena akun ini digunakan untuk login di perangkat lain.',
            ]);
    }
}
