<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\DataguruController;
use App\Http\Controllers\DatasiswaController;

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

Route::middleware([
    'auth',
    'role:A,G',
    ])->group(function () {

        Route::get(
            '/profil-guru',
            [GuruController::class, 'profil']
        )->name('guru.profil');


        Route::post(
            '/updateprofil',
            [GuruController::class, 'updateProfil']
        )->name('guru.profil.update');


        Route::post(
            '/upload-foto-user',
            [GuruController::class, 'uploadFotoUser']
        )->name('guru.profil.photo');


        Route::post(
            '/update-profil-sekolah',
            [GuruController::class, 'updateProfilSekolah']
        )->name('guru.school.update');


        Route::post(
            '/upload-foto-sekolah',
            [GuruController::class, 'uploadFotoSekolah']
        )->name('guru.school.photo');

        // route Guru lain...

        Route::get(
            '/data-guru',
            [DataguruController::class, 'index']
        )->name('guru.data');

        Route::post(
            '/get-user',
            [DataguruController::class, 'getUser']
        )->name('guru.search');

        Route::post(
            '/simpanformguru',
            [DataguruController::class, 'store']
        )->name('guru.store');

        Route::get(
            '/detail-guru/{id}',
            [DataguruController::class, 'show']
        )
            ->whereNumber('id')
            ->name('guru.detail');

        Route::post(
            '/hapusguru/{id}',
            [DataguruController::class, 'destroy']
        )
            ->whereNumber('id')
            ->name('guru.destroy');
        Route::get(
        '/kelas',
        [GuruController::class, 'kelas']
            )->name('guru.kelas');


        Route::post(
            '/ajax/ubah-kelas',
            [GuruController::class, 'ubahKelas']
            )->name('guru.kelas.update-inline');


        Route::post(
            '/ubahkelas',
            [GuruController::class, 'ubahKelas']
        )->name('guru.kelas.update');


        Route::post(
            '/tambahkelas',
            [GuruController::class, 'tambahKelas']
        )->name('guru.kelas.store');


        Route::post(
            '/hapuskelas',
            [GuruController::class, 'hapusKelas']
        )->name('guru.kelas.destroy');


        Route::get(
            '/detail-kelas/{id}',
            [GuruController::class, 'detailKelas']
        )
            ->whereNumber('id')
            ->name('guru.kelas.detail');


        Route::post(
            '/cekkelassiswa',
            [GuruController::class, 'cekKelasSiswa']
        )->name('guru.kelas.siswa.check');


        Route::post(
            '/tambahsiswakekelas',
            [GuruController::class, 'tambahSiswaKeKelas']
        )->name('guru.kelas.siswa.add');


        Route::post(
            '/hapuskelassiswa',
            [GuruController::class, 'hapusKelasSiswa']
        )->name('guru.kelas.siswa.remove');

        Route::get(
            '/data-siswa',
            [DatasiswaController::class, 'index']
        )->name('guru.siswa');


        Route::post(
            '/get-siswa',
            [DatasiswaController::class, 'search']
        )->name('guru.siswa.search');


        Route::post(
            '/simpanformsiswa',
            [DatasiswaController::class, 'store']
        )->name('guru.siswa.store');


        Route::get(
            '/detail-kelas-siswa/{id}',
            [DatasiswaController::class, 'show']
        )
            ->whereNumber('id')
            ->name('guru.siswa.detail');


        Route::post(
            '/updateprofilsiswa',
            [DatasiswaController::class, 'update']
        )->name('guru.siswa.update');


        Route::post(
            '/updateprofilfotosiswa',
            [DatasiswaController::class, 'updatePhoto']
        )->name('guru.siswa.photo');


        Route::post(
            '/hapussiswa',
            [DatasiswaController::class, 'destroy']
        )->name('guru.siswa.destroy');


        Route::post(
            '/hapuscalonsiswa',
            [DatasiswaController::class, 'destroyCandidates']
        )->name('guru.siswa.candidates.destroy');

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