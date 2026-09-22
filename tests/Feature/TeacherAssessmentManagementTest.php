<?php

use App\Models\Countexamtime;
use App\Models\Detailsoal;
use App\Models\Distribusisoal;
use App\Models\Jawab;
use App\Models\Kelas;
use App\Models\Soal;
use App\Models\User;


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function createManagementPackage(
    int $ownerId,
    array $attributes = []
): Soal {

    return Soal::query()->create(
        array_merge(
            [
                'id_user' =>
                    (string) $ownerId,

                'jenis' =>
                    '1',

                'materi' =>
                    null,

                'paket' =>
                    'Paket Management Test',

                'deskripsi' =>
                    'Regression test paket soal',

                'kkm' =>
                    '75',

                'waktu' =>
                    '3600',

                'tampil' =>
                    'Y',
            ],
            $attributes
        )
    );
}


function createManagementQuestion(
    Soal $soal,
    int $ownerId,
    array $attributes = []
): Detailsoal {

    return Detailsoal::query()->create(
        array_merge(
            [
                'id_soal' =>
                    (string) $soal->id,

                'jenis' =>
                    '1',

                'soal' =>
                    'Pertanyaan regression test',

                'audio' =>
                    null,

                'pila' =>
                    'Jawaban A',

                'pilb' =>
                    'Jawaban B',

                'pilc' =>
                    'Jawaban C',

                'pild' =>
                    'Jawaban D',

                'pile' =>
                    'Jawaban E',

                'kunci' =>
                    'A',

                'score' =>
                    '100',

                'id_user' =>
                    (string) $ownerId,

                'status' =>
                    'Y',

                'sesi' =>
                    'management-'.$soal->id,
            ],
            $attributes
        )
    );
}


function createHistoricalAnswer(
    Soal $soal,
    Detailsoal $detail,
    User $student,
    Kelas $kelas,
    array $attributes = []
): Jawab {

    return Jawab::query()->create(
        array_merge(
            [
                'no_soal_id' =>
                    $detail->id,

                'id_soal' =>
                    (string) $soal->id,

                'id_user' =>
                    (string) $student->id,

                'id_kelas' =>
                    (string) $kelas->id,

                'nama' =>
                    $student->nama,

                'pilihan' =>
                    'A',

                'score' =>
                    '100',

                'status' =>
                    'Y',
            ],
            $attributes
        )
    );
}


/*
|--------------------------------------------------------------------------
| Delete Paket Soal
|--------------------------------------------------------------------------
*/

test(
    'guru dapat menghapus paket yang belum pernah dikerjakan beserta dependensinya',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',
            ]);


        $kelas =
            Kelas::query()->create([
                'nama' =>
                    'Kelas Delete Test',
            ]);


        $paket =
            createManagementPackage(
                $guru->id
            );


        $detail =
            createManagementQuestion(
                $paket,
                $guru->id
            );


        $distribusi =
            Distribusisoal::query()->create([
                'id_soal' =>
                    (string) $paket->id,

                'id_kelas' =>
                    (string) $kelas->id,
            ]);


        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.soal.destroy',
                        $paket->id
                    )
                );


        $response
            ->assertOk()
            ->assertJson([
                'message' =>
                    'Paket soal berhasil dihapus.',
            ]);


        $this->assertDatabaseMissing(
            'soals',
            [
                'id' =>
                    $paket->id,
            ]
        );


        $this->assertDatabaseMissing(
            'detailsoals',
            [
                'id' =>
                    $detail->id,
            ]
        );


        $this->assertDatabaseMissing(
            'distribusisoals',
            [
                'id' =>
                    $distribusi->id,
            ]
        );

    }
);


test(
    'paket yang sudah memiliki jawaban siswa tidak dapat dihapus',
    function () {

        $kelas =
            Kelas::query()->create([
                'nama' =>
                    'Kelas History Test',
            ]);


        $guru =
            $this->createUser([
                'status' => 'G',
            ]);


        $student =
            $this->createUser([
                'status' =>
                    'S',

                'id_kelas' =>
                    $kelas->id,
            ]);


        $paket =
            createManagementPackage(
                $guru->id
            );


        $detail =
            createManagementQuestion(
                $paket,
                $guru->id
            );


        $jawab =
            createHistoricalAnswer(
                $paket,
                $detail,
                $student,
                $kelas
            );


        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.soal.destroy',
                        $paket->id
                    )
                );


        $response
            ->assertStatus(409)
            ->assertJson([
                'message' =>
                    'Paket soal tidak dapat dihapus karena sudah memiliki riwayat pengerjaan siswa.',
            ]);


        $this->assertDatabaseHas(
            'soals',
            [
                'id' =>
                    $paket->id,
            ]
        );


        $this->assertDatabaseHas(
            'detailsoals',
            [
                'id' =>
                    $detail->id,
            ]
        );


        $this->assertDatabaseHas(
            'jawabs',
            [
                'id' =>
                    $jawab->id,
            ]
        );

    }
);


test(
    'paket yang sudah pernah dimulai siswa tidak dapat dihapus meskipun belum ada jawaban',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',
            ]);


        $student =
            $this->createUser([
                'status' => 'S',
            ]);


        $paket =
            createManagementPackage(
                $guru->id
            );


        Countexamtime::query()->create([
            'id_soal' =>
                (string) $paket->id,

            'id_user' =>
                (string) $student->id,

            'waktu' =>
                '3500',
        ]);


        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.soal.destroy',
                        $paket->id
                    )
                );


        $response
            ->assertStatus(409);


        $this->assertDatabaseHas(
            'soals',
            [
                'id' =>
                    $paket->id,
            ]
        );


        $this->assertDatabaseHas(
            'countexamtimes',
            [
                'id_soal' =>
                    (string) $paket->id,

                'id_user' =>
                    (string) $student->id,
            ]
        );

    }
);


/*
|--------------------------------------------------------------------------
| Ownership / IDOR
|--------------------------------------------------------------------------
*/

test(
    'guru tidak dapat menghapus paket milik guru lain',
    function () {

        $pemilik =
            $this->createUser([
                'status' =>
                    'G',

                'nama' =>
                    'Guru Pemilik',
            ]);


        $guruLain =
            $this->createUser([
                'status' =>
                    'G',

                'nama' =>
                    'Guru Lain',
            ]);


        $paket =
            createManagementPackage(
                $pemilik->id
            );


        $response =
            $this
                ->actingAs($guruLain)
                ->postJson(
                    route(
                        'guru.soal.destroy',
                        $paket->id
                    )
                );


        $response
            ->assertNotFound();


        $this->assertDatabaseHas(
            'soals',
            [
                'id' =>
                    $paket->id,

                'id_user' =>
                    (string) $pemilik->id,
            ]
        );

    }
);


/*
|--------------------------------------------------------------------------
| Historical Class Result
|--------------------------------------------------------------------------
*/

test(
    'hasil historis siswa tetap dapat dihapus setelah siswa pindah kelas',
    function () {

        $kelasLama =
            Kelas::query()->create([
                'nama' =>
                    'Kelas Lama',
            ]);


        $kelasBaru =
            Kelas::query()->create([
                'nama' =>
                    'Kelas Baru',
            ]);


        $guru =
            $this->createUser([
                'status' =>
                    'G',
            ]);


        $student =
            $this->createUser([
                'status' =>
                    'S',

                'id_kelas' =>
                    $kelasLama->id,

                'nama' =>
                    'Siswa Pindah Kelas',
            ]);


        $paket =
            createManagementPackage(
                $guru->id
            );


        $detail =
            createManagementQuestion(
                $paket,
                $guru->id
            );


        createHistoricalAnswer(
            $paket,
            $detail,
            $student,
            $kelasLama
        );


        Countexamtime::query()->create([
            'id_soal' =>
                (string) $paket->id,

            'id_user' =>
                (string) $student->id,

            'waktu' =>
                '0',
        ]);


        /*
         * Simulasikan siswa sudah pindah
         * kelas setelah ujian selesai.
         */
        User::query()
            ->whereKey(
                $student->id
            )
            ->update([
                'id_kelas' =>
                    $kelasBaru->id,
            ]);


        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.results.student.destroy'
                    ),
                    [
                        'id_user' =>
                            $student->id,

                        'id_kelas' =>
                            $kelasLama->id,

                        'id_soal' =>
                            $paket->id,
                    ]
                );


        $response
            ->assertOk()
            ->assertJson([
                'success' =>
                    true,

                'message' =>
                    'Hasil siswa berhasil dihapus.',
            ]);


        /*
         * Jawaban historis kelas lama
         * sudah dihapus.
         */
        $this->assertDatabaseMissing(
            'jawabs',
            [
                'id_user' =>
                    (string) $student->id,

                'id_soal' =>
                    (string) $paket->id,

                'id_kelas' =>
                    (string) $kelasLama->id,
            ]
        );


        /*
         * Timer paket tersebut ikut
         * dibersihkan.
         */
        $this->assertDatabaseMissing(
            'countexamtimes',
            [
                'id_user' =>
                    (string) $student->id,

                'id_soal' =>
                    (string) $paket->id,
            ]
        );


        /*
         * Siswa tetap berada di kelas barunya.
         */
        $this->assertDatabaseHas(
            'users',
            [
                'id' =>
                    $student->id,

                'id_kelas' =>
                    $kelasBaru->id,
            ]
        );

    }
);


test(
    'guru tidak dapat menghapus hasil paket milik guru lain',
    function () {

        $kelas =
            Kelas::query()->create([
                'nama' =>
                    'Kelas IDOR',
            ]);


        $pemilik =
            $this->createUser([
                'status' =>
                    'G',
            ]);


        $guruLain =
            $this->createUser([
                'status' =>
                    'G',
            ]);


        $student =
            $this->createUser([
                'status' =>
                    'S',

                'id_kelas' =>
                    $kelas->id,
            ]);


        $paket =
            createManagementPackage(
                $pemilik->id
            );


        $detail =
            createManagementQuestion(
                $paket,
                $pemilik->id
            );


        $jawab =
            createHistoricalAnswer(
                $paket,
                $detail,
                $student,
                $kelas
            );


        $response =
            $this
                ->actingAs($guruLain)
                ->postJson(
                    route(
                        'guru.results.student.destroy'
                    ),
                    [
                        'id_user' =>
                            $student->id,

                        'id_kelas' =>
                            $kelas->id,

                        'id_soal' =>
                            $paket->id,
                    ]
                );


        $response
            ->assertNotFound();


        $this->assertDatabaseHas(
            'jawabs',
            [
                'id' =>
                    $jawab->id,
            ]
        );

    }
);


test(
    'hapus hasil siswa tidak menghapus hasil pada paket lain',
    function () {

        $kelas =
            Kelas::query()->create([
                'nama' =>
                    'Kelas Scope Test',
            ]);


        $guru =
            $this->createUser([
                'status' =>
                    'G',
            ]);


        $student =
            $this->createUser([
                'status' =>
                    'S',

                'id_kelas' =>
                    $kelas->id,
            ]);


        /*
         * Paket pertama.
         */
        $paketA =
            createManagementPackage(
                $guru->id,
                [
                    'paket' =>
                        'Paket A',
                ]
            );


        $detailA =
            createManagementQuestion(
                $paketA,
                $guru->id
            );


        createHistoricalAnswer(
            $paketA,
            $detailA,
            $student,
            $kelas
        );


        Countexamtime::query()->create([
            'id_soal' =>
                (string) $paketA->id,

            'id_user' =>
                (string) $student->id,

            'waktu' =>
                '0',
        ]);


        /*
         * Paket kedua.
         */
        $paketB =
            createManagementPackage(
                $guru->id,
                [
                    'paket' =>
                        'Paket B',
                ]
            );


        $detailB =
            createManagementQuestion(
                $paketB,
                $guru->id
            );


        $jawabB =
            createHistoricalAnswer(
                $paketB,
                $detailB,
                $student,
                $kelas
            );


        $timerB =
            Countexamtime::query()->create([
                'id_soal' =>
                    (string) $paketB->id,

                'id_user' =>
                    (string) $student->id,

                'waktu' =>
                    '1200',
            ]);


        /*
         * Hapus hanya hasil Paket A.
         */
        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.results.student.destroy'
                    ),
                    [
                        'id_user' =>
                            $student->id,

                        'id_kelas' =>
                            $kelas->id,

                        'id_soal' =>
                            $paketA->id,
                    ]
                );


        $response
            ->assertOk();


        /*
         * Paket A hilang.
         */
        $this->assertDatabaseMissing(
            'jawabs',
            [
                'id_user' =>
                    (string) $student->id,

                'id_soal' =>
                    (string) $paketA->id,
            ]
        );


        /*
         * Paket B harus tetap utuh.
         */
        $this->assertDatabaseHas(
            'jawabs',
            [
                'id' =>
                    $jawabB->id,
            ]
        );


        $this->assertDatabaseHas(
            'countexamtimes',
            [
                'id' =>
                    $timerB->id,
            ]
        );

    }
);