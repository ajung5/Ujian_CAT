<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\DataguruController;
use App\Http\Controllers\DatasiswaController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\LatihanController;
use App\Http\Controllers\HasilController;

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
        'A', 'G' =>
            redirect()->route('guru.index'),
        'S', 'C' =>
            redirect()->route('siswa.index'),
        default =>
            abort(
                403,
                'Role pengguna tidak dikenali.'
            ),
    };
});

Route::middleware([
    'auth',
    'role:A,G',
    ])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard Guru / Admin
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/guru',
            [GuruController::class, 'index']
        )->name('guru.index');

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

        Route::post(
            '/uploadsiswa',
            [DatasiswaController::class, 'importStudents']
        )->name('guru.siswa.import');


        Route::post(
            '/uploadcalonsiswa',
            [DatasiswaController::class, 'importCandidates']
        )->name('guru.siswa.candidate.import');

        Route::get(
            '/materi',
            [MateriController::class, 'index']
        )->name('guru.materi');


        Route::get(
            '/materi/ubah/{id}',
            [MateriController::class, 'edit']
        )
            ->whereNumber('id')
            ->name('guru.materi.edit');


        Route::post(
            '/simpan-materi',
            [MateriController::class, 'save']
        )->name('guru.materi.save');


        Route::get(
            '/materi/detail/{id}',
            [MateriController::class, 'show']
        )
            ->whereNumber('id')
            ->name('guru.materi.detail');


        Route::post(
            '/upload-gambar-materi',
            [MateriController::class, 'uploadImage']
        )->name('guru.materi.image');


        Route::post(
            '/hapus_materi',
            [MateriController::class, 'destroy']
        )->name('guru.materi.destroy');


        Route::post(
            '/get-materi',
            [MateriController::class, 'search']
        )->name('guru.materi.search');

        /*
        |--------------------------------------------------------------------------
        | Paket Soal
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/soal-guru',
            [SoalController::class, 'index']
        )->name('guru.soal');


        Route::post(
            '/get-soal-guru',
            [SoalController::class, 'search']
        )->name('guru.soal.search');


        Route::post(
            '/simpanformsoal',
            [SoalController::class, 'store']
        )->name('guru.soal.store');


        Route::get(
            '/edit-soal/{id}',
            [SoalController::class, 'edit']
        )
            ->whereNumber('id')
            ->name('guru.soal.edit');


        Route::post(
            '/updateformsoal',
            [SoalController::class, 'update']
        )->name('guru.soal.update');


        Route::get(
            '/hapus-soal/{id}',
            [SoalController::class, 'deleteConfirm']
        )
            ->whereNumber('id')
            ->name('guru.soal.delete-confirm');


        Route::post(
            '/eksekusi-hapus-paket-soal/{id}',
            [SoalController::class, 'destroy']
        )
            ->whereNumber('id')
            ->name('guru.soal.destroy');
        
        /*
        |--------------------------------------------------------------------------
        | Detail Soal
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/detail-soal/{id}',
            [SoalController::class, 'detail']
        )
            ->whereNumber('id')
            ->name('guru.soal.detail');


        Route::post(
            '/simpanformdetailsoal',
            [SoalController::class, 'storeDetail']
        )->name('guru.soal.detail.store');


        Route::get(
            '/ubah-detail-soal/{id}',
            [SoalController::class, 'editDetail']
        )
            ->whereNumber('id')
            ->name('guru.soal.detail.edit');


        Route::post(
            '/ubahformdetailsoal',
            [SoalController::class, 'updateDetail']
        )->name('guru.soal.detail.update');


        Route::post(
            '/hapusdetailsoal',
            [SoalController::class, 'destroyDetail']
        )->name('guru.soal.detail.destroy');


        Route::post(
            '/upload_file_audio',
            [SoalController::class, 'uploadAudio']
        )->name('guru.soal.audio.upload');


        Route::post(
            '/hapus_audio',
            [SoalController::class, 'destroyAudio']
        )->name('guru.soal.audio.destroy');


        Route::post(
            '/simpandistribusikelas',
            [SoalController::class, 'storeDistribution']
        )->name('guru.soal.distribution.store');


        Route::post(
            '/hapusdistribusikelas',
            [SoalController::class, 'destroyDistribution']
        )->name('guru.soal.distribution.destroy');

       Route::post(
            '/uploadsoal',
            [SoalController::class, 'importQuestions']
            )->name('guru.soal.import');

    }
);

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

    /*
    |--------------------------------------------------------------------------
    | Hasil / Laporan Guru
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/hasil-guru',
        [HasilController::class, 'index']
    )->name('guru.results');


    Route::post(
        '/get-hasil-guru',
        [HasilController::class, 'search']
    )->name('guru.results.search');
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
| Area Siswa
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:S,C',
    ])->group(function () {
        Route::get(
            '/siswa',
            [SiswaController::class, 'index']
        )->name('siswa.index');

        Route::get(
            '/soal-siswa',
            [SiswaController::class, 'exams']
        )->name('siswa.soal');

        Route::get(
            '/soal-siswa/{id}',
            [SiswaController::class, 'exam']
        )
            ->whereNumber('id')
            ->name('siswa.exam');


        Route::post(
            '/ujian/{id}/start',
            [SiswaController::class, 'startExam']
        )
            ->whereNumber('id')
            ->name('siswa.exam.start');


        Route::post(
            '/get-soal/{id}',
            [SiswaController::class, 'question']
        )
            ->whereNumber('id')
            ->name('siswa.exam.question');


        Route::post(
            '/simpanjawabankliksiswa',
            [SiswaController::class, 'saveAnswer']
        )->name('siswa.exam.answer');


        Route::post(
            '/countexamtime',
            [SiswaController::class, 'syncTime']
        )->name('siswa.exam.time');


        Route::post(
            '/kirimjawaban',
            [SiswaController::class, 'finishExam']
        )->name('siswa.exam.finish');

        /*
        |--------------------------------------------------------------------------
        | Hasil Ujian Siswa
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/hasil-siswa',
            [SiswaController::class, 'results']
        )->name('siswa.results');


        Route::post(
            '/get-hasil',
            [SiswaController::class, 'searchResults']
        )->name('siswa.results.search');


        Route::get(
            '/hasil-siswa/detail/{id}',
            [SiswaController::class, 'resultDetail']
        )
            ->whereNumber('id')
            ->name('siswa.results.detail');

        /*
        |--------------------------------------------------------------------------
        | Profil Siswa
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profil-siswa',
            [SiswaController::class, 'profile']
        )->name('siswa.profile');


        Route::post(
            '/updateprofilfoto',
            [SiswaController::class, 'updateProfilePhoto']
        )->name('siswa.profile.photo');

        /*
        |--------------------------------------------------------------------------
        | Materi & Latihan Siswa
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/latihan',
            [LatihanController::class, 'index']
        )->name('siswa.latihan');


        Route::get(
            '/latihan/read/{id}/{judul}',
            [LatihanController::class, 'detail']
        )
            ->whereNumber('id')
            ->name('siswa.latihan.detail');

    }
);