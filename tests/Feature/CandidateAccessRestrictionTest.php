<?php

use App\Models\Kelas;

/*
|--------------------------------------------------------------------------
| Candidate Access Restriction
|--------------------------------------------------------------------------
*/

test(
    'calon siswa tetap dapat membuka dashboard dan profil',
    function () {

        $candidate =
            $this->createUser([
                'status' => 'C',

                'nama' => 'Calon Siswa Restricted',
            ]);

        $this
            ->actingAs($candidate)
            ->get(
                route('siswa.index')
            )
            ->assertOk()
            ->assertSee(
                'Status Anda masih Calon Siswa'
            );

        $this
            ->actingAs($candidate)
            ->get(
                route('siswa.profile')
            )
            ->assertOk();

    }
);

test(
    'calon siswa tidak dapat mengakses seluruh route assessment siswa',
    function () {

        $candidate =
            $this->createUser([
                'status' => 'C',
            ]);

        $requests = [
            [
                'GET',
                route('siswa.soal'),
                [],
            ],
            [
                'GET',
                route('siswa.exam', 1),
                [],
            ],
            [
                'POST',
                route('siswa.exam.start', 1),
                [],
            ],
            [
                'POST',
                route('siswa.exam.question', 1),
                [],
            ],
            [
                'POST',
                route('siswa.exam.answer'),
                [],
            ],
            [
                'POST',
                route('siswa.exam.time'),
                [],
            ],
            [
                'POST',
                route('siswa.exam.finish'),
                [],
            ],
            [
                'GET',
                route('siswa.results'),
                [],
            ],
            [
                'POST',
                route('siswa.results.search'),
                [],
            ],
            [
                'GET',
                route('siswa.results.detail', 1),
                [],
            ],
            [
                'GET',
                route('siswa.latihan'),
                [],
            ],
            [
                'GET',
                route('siswa.training', 1),
                [],
            ],
            [
                'POST',
                route('siswa.training.start', 1),
                [],
            ],
            [
                'GET',
                route(
                    'siswa.latihan.detail',
                    [
                        'id' => 1,

                        'judul' => 'restricted',
                    ]
                ),
                [],
            ],
        ];

        foreach ($requests as [
            $method,
            $url,
            $data,
        ]) {
            $response =
                $this
                    ->actingAs($candidate)
                    ->call(
                        $method,
                        $url,
                        $data
                    );

            $response
                ->assertRedirect('/siswa');
        }

    }
);

test(
    'dashboard calon siswa tidak menampilkan menu assessment',
    function () {

        $candidate =
            $this->createUser([
                'status' => 'C',
            ]);

        $response =
            $this
                ->actingAs($candidate)
                ->get(
                    route('siswa.index')
                );

        $response
            ->assertOk()
            ->assertSee(
                'Status Anda masih Calon Siswa'
            )
            ->assertDontSee(
                'Lihat Soal Ujian'
            )
            ->assertDontSee(
                'Latihan Materi'
            )
            ->assertDontSee(
                'Hasil Ujian'
            )
            ->assertDontSee(
                'Soal Ujian'
            );

    }
);

test(
    'siswa berstatus s tetap dapat mengakses daftar ujian dan materi latihan',
    function () {

        $kelas =
            Kelas::query()->create([
                'nama' => 'Kelas Aktif',
            ]);

        $siswa =
            $this->createUser([
                'status' => 'S',

                'id_kelas' => $kelas->id,
            ]);

        $this
            ->actingAs($siswa)
            ->get(
                route('siswa.soal')
            )
            ->assertOk();

        $this
            ->actingAs($siswa)
            ->get(
                route('siswa.latihan')
            )
            ->assertOk();

    }
);

test(
    'setelah status calon siswa berubah menjadi s akses assessment otomatis terbuka',
    function () {

        $kelas =
            Kelas::query()->create([
                'nama' => 'Kelas Setelah Diterima',
            ]);

        $candidate =
            $this->createUser([
                'status' => 'C',

                'id_kelas' => $kelas->id,
            ]);

        $this
            ->actingAs($candidate)
            ->get(
                route('siswa.soal')
            )
            ->assertRedirect('/siswa');

        $candidate->status = 'S';
        $candidate->save();

        $this
            ->actingAs($candidate->fresh())
            ->get(
                route('siswa.soal')
            )
            ->assertOk();

    }
);
