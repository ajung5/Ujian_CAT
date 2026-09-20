<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;

/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->status) {
        'A', 'G' => redirect('/guru'),
        'S', 'C' => redirect('/siswa'),
        default => abort(403, 'Role pengguna tidak dikenali.'),
    };
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get(
        '/auth/login',
        [AuthController::class, 'showLoginForm']
    )->name('login');

    Route::post(
        '/auth/login',
        [AuthController::class, 'login']
    )
        ->middleware('throttle:5,1')
        ->name('login.process');
});


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
|
| GET dipertahankan sementara untuk kompatibilitas aplikasi Laravel 5.1.
| Nantinya sebaiknya UI logout dipindahkan ke POST + CSRF.
|
*/

Route::match(
    ['get', 'post'],
    '/auth/logout',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Temporary Role Test
|--------------------------------------------------------------------------
|
| Route ini nanti akan diganti dengan GuruController dan SiswaController
| hasil migrasi.
|
*/

Route::get(
    '/guru',
    [GuruController::class, 'index']
)
    ->middleware(['auth', 'role:A,G'])
    ->name('guru.index');


Route::get('/siswa', function () {

    $user = auth()->user();

    return response()->json([
        'authenticated' => true,
        'area' => 'siswa',
        'id' => $user->id,
        'nama' => $user->nama,
        'email' => $user->email,
        'status' => $user->status,
        'id_kelas' => $user->id_kelas,
    ]);

})
    ->middleware(['auth', 'role:S,C'])
    ->name('siswa.index');