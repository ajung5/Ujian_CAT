<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm(): View|RedirectResponse{
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->status);
        }

        $school = School::first();

        return view('auth.login', compact('school'));
    }

    /**
     * Proses login user legacy.
     */
    public function login(Request $request): RedirectResponse{
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password tidak sesuai.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }

        // Mencegah session fixation.
        $request->session()->regenerate();

        $status = Auth::user()->status;

        // Hanya role legacy yang dikenal aplikasi.
        if (! in_array($status, ['A', 'G', 'S', 'C'], true)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Status pengguna tidak dikenali.',
                ]);
        }

        return $this->redirectByRole($status);
    }

    /**
     * Logout.
     */
    public function logout(Request $request): RedirectResponse{
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect berdasarkan status user legacy.
     */
    private function redirectByRole(?string $status): RedirectResponse {
        return match ($status) {
            'A', 'G' =>
                redirect()->route('guru.index'),
            'S', 'C' =>
                redirect()->route('siswa.index'),
            default =>
                redirect()->route('login'),
        };
    }
}