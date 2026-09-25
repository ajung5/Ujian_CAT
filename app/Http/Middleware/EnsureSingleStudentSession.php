<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserSecurityEvent;
use App\Services\SecurityEventLogger;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleStudentSession {
    public function __construct(private readonly SecurityEventLogger $securityEvents) {}

    /**
     * Pastikan akun siswa hanya mempunyai satu session aktif.
     *
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response {
        $user = Auth::user();

        if (!($user instanceof User) || $user->status !== 'S') {
            return $next($request);
        }

        if ($user->student_session_revoked_at !== null) {
            $this->securityEvents->log(
                request: $request,
                event: UserSecurityEvent::SESSION_INVALIDATED,
                subject: $user,
                metadata: [
                    'reason' => 'admin_forced_logout',
                ],
            );

            return $this->terminateStudentSession(
                request: $request,
                message: 'Sesi Anda telah dihentikan oleh administrator. Silakan login kembali.',
            );
        }

        $activeSessionHash = (string) ($user->active_session_hash ?? '');
        $studentSessionToken = (string) $request->session()->get('student_session_token', '');

        /*
         * Kompatibilitas session siswa yang sudah aktif sebelum
         * fitur fingerprint session diterapkan.
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

            $this->securityEvents->log(
                request: $request,
                event: UserSecurityEvent::SESSION_LEGACY_CLAIMED,
                subject: $user,
                metadata: [
                    'reason' => 'missing_active_session_hash',
                ],
            );

            return $next($request);
        }

        if ($studentSessionToken === '') {
            $this->securityEvents->log(
                request: $request,
                event: UserSecurityEvent::SESSION_INVALIDATED,
                subject: $user,
                metadata: [
                    'reason' => 'missing_student_session_token',
                ],
            );

            return $this->terminateStudentSession($request);
        }

        $currentSessionHash = hash('sha256', $studentSessionToken);

        if (!hash_equals($activeSessionHash, $currentSessionHash)) {
            $this->securityEvents->log(
                request: $request,
                event: UserSecurityEvent::SESSION_INVALIDATED,
                subject: $user,
                metadata: [
                    'reason' => 'session_replaced_or_mismatch',
                ],
            );

            return $this->terminateStudentSession($request);
        }

        return $next($request);
    }

    /**
     * Akhiri session siswa yang sudah tidak sah.
     */
    private function terminateStudentSession(
        Request $request,
        string $message = 'Sesi Anda telah berakhir karena akun ini digunakan untuk login di perangkat lain.',
    ): RedirectResponse {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => $message,
            ]);
    }
}
