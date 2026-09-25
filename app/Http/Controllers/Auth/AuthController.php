<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\UserSecurityEvent;
use App\Services\SecurityEventLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller {
    public function __construct(private readonly SecurityEventLogger $securityEvents) {}

    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm(): View|RedirectResponse {
        $user = Auth::user();

        if ($user instanceof User) {
            return $this->redirectByRole($user->status);
        }

        $school = School::first();

        return view('auth.login', compact('school'));
    }

    /**
     * Proses login.
     */
    public function login(Request $request): RedirectResponse {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $credentials = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
            ],
        );

        $loginUser = User::query()->where('email', $credentials['email'])->first();

        $remember = $request->boolean('remember') && $loginUser?->status !== 'S';

        if (!Auth::attempt($credentials, $remember)) {
            $this->securityEvents->log(
                request: $request,
                event: UserSecurityEvent::LOGIN_FAILED,
                subject: $loginUser,
                metadata: [
                    'reason' => 'invalid_credentials',
                ],
                email: $credentials['email'],
            );

            return back()
                ->withErrors([
                    'email' => 'Email atau password tidak sesuai.',
                ])
                ->withInput($request->only('email'));
        }

        /*
         * Regenerasi session ID untuk mencegah session fixation.
         */
        $request->session()->regenerate();

        $user = Auth::user();

        if (!($user instanceof User)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Data pengguna tidak valid.',
                ]);
        }

        $status = $user->status;

        if (!in_array($status, ['A', 'G', 'S', 'C'], true)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Status pengguna tidak dikenali.',
                ]);
        }

        $loginMetadata = [
            'remember_requested' => $request->boolean('remember'),
            'remember_applied' => $remember,
        ];

        if ($status === 'S') {
            $previousSessionHash = (string) ($user->active_session_hash ?? '');
            $studentSessionToken = bin2hex(random_bytes(32));
            $currentSessionHash = hash('sha256', $studentSessionToken);

            $request->session()->put('student_session_token', $studentSessionToken);

            $user
                ->forceFill([
                    'remember_token' => null,
                    'active_session_hash' => $currentSessionHash,
                    'student_session_revoked_at' => null,
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                    'last_login_user_agent' => $request->userAgent(),
                ])
                ->saveQuietly();

            if ($previousSessionHash !== '') {
                $this->securityEvents->log(
                    request: $request,
                    event: UserSecurityEvent::SESSION_REPLACED,
                    subject: $user,
                    actor: $user,
                    metadata: [
                        'reason' => 'new_student_login',
                        'previous_session_existed' => true,
                    ],
                );
            }
        } else {
            $user
                ->forceFill([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                    'last_login_user_agent' => $request->userAgent(),
                ])
                ->saveQuietly();
        }

        $this->securityEvents->log(
            request: $request,
            event: UserSecurityEvent::LOGIN_SUCCESS,
            subject: $user,
            actor: $user,
            metadata: $loginMetadata,
        );

        return $this->redirectByRole($status);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): RedirectResponse {
        $user = Auth::user();

        if ($user instanceof User) {
            $this->securityEvents->log(
                request: $request,
                event: UserSecurityEvent::LOGOUT,
                subject: $user,
                actor: $user,
            );
        }

        if ($user instanceof User && $user->status === 'S') {
            $activeSessionHash = (string) ($user->active_session_hash ?? '');
            $studentSessionToken = (string) $request->session()->get('student_session_token', '');

            if (
                $activeSessionHash !== '' &&
                $studentSessionToken !== '' &&
                hash_equals($activeSessionHash, hash('sha256', $studentSessionToken))
            ) {
                $user
                    ->forceFill([
                        'active_session_hash' => null,
                        'student_session_revoked_at' => null,
                    ])
                    ->saveQuietly();
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect user berdasarkan role legacy.
     */
    private function redirectByRole(?string $status): RedirectResponse {
        return match ($status) {
            'A', 'G' => redirect()->route('guru.index'),
            'S', 'C' => redirect()->route('siswa.index'),
            default => redirect()->route('login'),
        };
    }
}
