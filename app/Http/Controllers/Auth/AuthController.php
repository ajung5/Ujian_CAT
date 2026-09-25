<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller {
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
        /*
         * Normalisasi email agar login legacy tetap konsisten.
         */
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

        /*
         * Cari role sebelum autentikasi.
         *
         * Tujuannya menentukan apakah Remember Me
         * diperbolehkan.
         *
         * Remember Me dinonaktifkan khusus siswa.
         */
        $loginUser = User::query()->where('email', $credentials['email'])->first();

        $remember = $request->boolean('remember') && $loginUser?->status !== 'S';

        /*
         * Autentikasi user.
         */
        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password tidak sesuai.',
                ])
                ->withInput($request->only('email'));
        }

        /*
         * Regenerasi session ID untuk mencegah
         * session fixation.
         */
        $request->session()->regenerate();

        $user = Auth::user();

        /*
         * Validasi user hasil autentikasi.
         */
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

        /*
         * Validasi role legacy aplikasi.
         *
         * A = Administrator
         * G = Guru
         * S = Siswa
         * C = Calon Siswa
         */
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

        /*
         * =====================================================
         * SINGLE ACTIVE SESSION KHUSUS SISWA
         * =====================================================
         *
         * Setiap login siswa menghasilkan random token baru.
         *
         * Token asli:
         *     hanya disimpan di Laravel session.
         *
         * Database:
         *     hanya menyimpan SHA-256 hash token.
         *
         * Login terbaru otomatis mengganti hash sebelumnya.
         * Akibatnya session/perangkat lama akan ditolak
         * oleh middleware EnsureSingleStudentSession.
         */
        if ($status === 'S') {
            $studentSessionToken = bin2hex(random_bytes(32));

            /*
             * Simpan token asli di session.
             */
            $request->session()->put('student_session_token', $studentSessionToken);

            /*
             * Remember Me tidak digunakan untuk siswa.
             *
             * remember_token lama juga dihapus agar cookie
             * persistent login dari versi aplikasi sebelumnya
             * tidak dapat digunakan kembali.
             *
             * Database hanya menyimpan hash dari token session.
             */
            $user
                ->forceFill([
                    'remember_token' => null,
                    'active_session_hash' => hash('sha256', $studentSessionToken),
                ])
                ->saveQuietly();
        }

        return $this->redirectByRole($status);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): RedirectResponse {
        $user = Auth::user();

        /*
         * =====================================================
         * CLEANUP ACTIVE SESSION SISWA
         * =====================================================
         *
         * Hanya session siswa yang masih merupakan session
         * aktif yang diperbolehkan menghapus
         * active_session_hash.
         *
         * Ini penting karena session lama tidak boleh
         * menghapus hash milik login/perangkat terbaru.
         */
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
                    ])
                    ->saveQuietly();
            }
        }

        /*
         * Logout Laravel.
         */
        Auth::logout();

        /*
         * Hapus seluruh data session lama.
         */
        $request->session()->invalidate();

        /*
         * Regenerasi CSRF token.
         */
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
