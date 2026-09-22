<?php

use App\Models\Kelas;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Terima Calon Siswa
|--------------------------------------------------------------------------
*/

test(
    'guru dapat menerima calon siswa menjadi siswa dan menetapkan kelas final',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',

                'nama' => 'Guru Penerimaan',
            ]);

        $kelasAwal =
            Kelas::query()->create([
                'nama' => 'Kelas Sementara',
            ]);

        $kelasTujuan =
            Kelas::query()->create([
                'nama' => 'Kelas X-A',
            ]);

        $candidate =
            $this->createUser([
                'status' => 'C',

                'id_kelas' => $kelasAwal->id,

                'nama' => 'Calon Siswa Test',

                'no_induk' => 'REG-2026-001',

                'sekolah_asal' => 'SMP Asal',
            ]);

        $emailSebelum =
            $candidate->email;

        $passwordSebelum =
            $candidate->password;

        $jumlahUserSebelum =
            User::query()->count();

        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.siswa.candidate.accept'
                    ),
                    [
                        'id_siswa' => $candidate->id,

                        'nis' => 'NIS-2026-001',

                        'id_kelas' => $kelasTujuan->id,
                    ]
                );

        $response
            ->assertOk()
            ->assertContent(
                'berhasil'
            );

        $candidate->refresh();

        expect($candidate->status)
            ->toBe('S');

        expect($candidate->no_induk)
            ->toBe('NIS-2026-001');

        expect((int) $candidate->id_kelas)
            ->toBe($kelasTujuan->id);

        expect($candidate->email)
            ->toBe($emailSebelum);

        expect($candidate->password)
            ->toBe($passwordSebelum);

        expect($candidate->sekolah_asal)
            ->toBe('SMP Asal');

        expect(User::query()->count())
            ->toBe($jumlahUserSebelum);

        $this->assertDatabaseHas(
            'aktifitas',
            [
                'id_user' => $guru->id,

                'nama' => 'Menerima calon siswa '.
                    'Calon Siswa Test '.
                    '(ID Pendaftaran: REG-2026-001) '.
                    'menjadi siswa, NIS: NIS-2026-001, '.
                    'kelas: Kelas X-A.',
            ]
        );

    }
);

test(
    'nis final yang sudah digunakan tidak dapat dipakai saat menerima calon siswa',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',
            ]);

        $kelas =
            Kelas::query()->create([
                'nama' => 'Kelas X-B',
            ]);

        $siswaExisting =
            $this->createUser([
                'status' => 'S',

                'no_induk' => 'NIS-DUPLIKAT',
            ]);

        $candidate =
            $this->createUser([
                'status' => 'C',

                'no_induk' => 'REG-DUPLIKAT',
            ]);

        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.siswa.candidate.accept'
                    ),
                    [
                        'id_siswa' => $candidate->id,

                        'nis' => $siswaExisting->no_induk,

                        'id_kelas' => $kelas->id,
                    ]
                );

        $response
            ->assertStatus(422)
            ->assertContent(
                'NIS sudah digunakan.'
            );

        $candidate->refresh();

        expect($candidate->status)
            ->toBe('C');

        expect($candidate->no_induk)
            ->toBe('REG-DUPLIKAT');

    }
);

test(
    'siswa yang sudah berstatus siswa tidak dapat diterima ulang sebagai calon siswa',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',
            ]);

        $kelas =
            Kelas::query()->create([
                'nama' => 'Kelas XI-A',
            ]);

        $siswa =
            $this->createUser([
                'status' => 'S',

                'no_induk' => 'NIS-SUDAH-SISWA',
            ]);

        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.siswa.candidate.accept'
                    ),
                    [
                        'id_siswa' => $siswa->id,

                        'nis' => 'NIS-BARU',

                        'id_kelas' => $kelas->id,
                    ]
                );

        $response
            ->assertStatus(409)
            ->assertContent(
                'Calon siswa tidak ditemukan atau sudah diterima.'
            );

        $siswa->refresh();

        expect($siswa->status)
            ->toBe('S');

        expect($siswa->no_induk)
            ->toBe('NIS-SUDAH-SISWA');

    }
);

test(
    'kelas tujuan yang tidak valid ditolak saat menerima calon siswa',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',
            ]);

        $candidate =
            $this->createUser([
                'status' => 'C',

                'no_induk' => 'REG-KELAS-INVALID',
            ]);

        $response =
            $this
                ->actingAs($guru)
                ->postJson(
                    route(
                        'guru.siswa.candidate.accept'
                    ),
                    [
                        'id_siswa' => $candidate->id,

                        'nis' => 'NIS-KELAS-INVALID',

                        'id_kelas' => 999999,
                    ]
                );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'id_kelas'
            );

        $candidate->refresh();

        expect($candidate->status)
            ->toBe('C');

        expect($candidate->no_induk)
            ->toBe('REG-KELAS-INVALID');

    }
);

test(
    'siswa tidak dapat menjalankan endpoint penerimaan calon siswa',
    function () {

        $siswaLogin =
            $this->createUser([
                'status' => 'S',
            ]);

        $kelas =
            Kelas::query()->create([
                'nama' => 'Kelas XII-A',
            ]);

        $candidate =
            $this->createUser([
                'status' => 'C',

                'no_induk' => 'REG-IDOR',
            ]);

        $response =
            $this
                ->actingAs($siswaLogin)
                ->post(
                    route(
                        'guru.siswa.candidate.accept'
                    ),
                    [
                        'id_siswa' => $candidate->id,

                        'nis' => 'NIS-IDOR',

                        'id_kelas' => $kelas->id,
                    ]
                );

        $response
            ->assertRedirect('/siswa');

        $candidate->refresh();

        expect($candidate->status)
            ->toBe('C');

        expect($candidate->no_induk)
            ->toBe('REG-IDOR');

    }
);

test(
    'detail calon siswa menampilkan form penerimaan dan kelas tujuan',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',
            ]);

        $kelas =
            Kelas::query()->create([
                'nama' => 'Kelas Penerimaan Test',
            ]);

        $candidate =
            $this->createUser([
                'status' => 'C',

                'id_kelas' => $kelas->id,

                'no_induk' => 'REG-UI-001',
            ]);

        $response =
            $this
                ->actingAs($guru)
                ->get(
                    route(
                        'guru.siswa.detail',
                        $candidate->id
                    )
                );

        $response
            ->assertOk()
            ->assertSee(
                'Terima Menjadi Siswa'
            )
            ->assertSee(
                'Kelas Penerimaan Test'
            )
            ->assertSee(
                'ID Pendaftaran'
            );

    }
);
