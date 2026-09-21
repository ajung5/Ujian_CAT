<?php

use App\Models\Aktifitas;
use App\Models\Kelas;

test(
    'guest tidak dapat membuka area guru',
    function () {

        $response =
            $this->get('/guru');

        $response
            ->assertRedirect(
                route('login')
            );

    }
);


test(
    'guest tidak dapat membuka area siswa',
    function () {

        $response =
            $this->get('/siswa');

        $response
            ->assertRedirect(
                route('login')
            );

    }
);


test(
    'guru diarahkan dari area siswa ke dashboard guru',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',
            ]);

        Aktifitas::query()->create([
            'id_user' => $guru->id,
            'nama' => 'Test activity',
        ]);

        $response =
            $this
                ->actingAs($guru)
                ->get('/siswa');

        $response
            ->assertRedirect('/guru');

    }
);


test(
    'siswa diarahkan dari area guru ke dashboard siswa',
    function () {

        $kelas =
            Kelas::query()->create([
                'nama' => 'Kelas Test',
            ]);

        $siswa =
            $this->createUser([
                'status' => 'S',
                'id_kelas' => $kelas->id,
            ]);

        $response =
            $this
                ->actingAs($siswa)
                ->get('/guru');

        $response
            ->assertRedirect('/siswa');

    }
);


test(
    'root mengarahkan guru ke dashboard guru',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',
            ]);

        $response =
            $this
                ->actingAs($guru)
                ->get('/');

        $response
            ->assertRedirect(
                route('guru.index')
            );

    }
);


test(
    'root mengarahkan siswa ke dashboard siswa',
    function () {

        $siswa =
            $this->createUser([
                'status' => 'S',
            ]);

        $response =
            $this
                ->actingAs($siswa)
                ->get('/');

        $response
            ->assertRedirect(
                route('siswa.index')
            );

    }
);


test(
    'login guru berhasil dan diarahkan ke dashboard guru',
    function () {

        $guru =
            $this->createUser([
                'status' => 'G',

                'email' =>
                    'guru@example.test',
            ]);

        $response =
            $this->post(
                '/auth/login',
                [
                    'email' =>
                        ' GURU@EXAMPLE.TEST ',

                    'password' =>
                        'password123',
                ]
            );

        $response
            ->assertRedirect(
                route('guru.index')
            );

        $this->assertAuthenticatedAs(
            $guru
        );

    }
);


test(
    'login siswa berhasil dan diarahkan ke dashboard siswa',
    function () {

        $siswa =
            $this->createUser([
                'status' => 'S',

                'email' =>
                    'siswa@example.test',
            ]);

        $response =
            $this->post(
                '/auth/login',
                [
                    'email' =>
                        'siswa@example.test',

                    'password' =>
                        'password123',
                ]
            );

        $response
            ->assertRedirect(
                route('siswa.index')
            );

        $this->assertAuthenticatedAs(
            $siswa
        );

    }
);

test(
    'logout hanya menerima post',
    function () {

        $user =
            $this->createUser([
                'status' => 'S',
            ]);

        $this
            ->actingAs($user)
            ->get('/auth/logout')
            ->assertStatus(405);

    }
);


test(
    'user dapat logout menggunakan post',
    function () {

        $user =
            $this->createUser([
                'status' => 'S',
            ]);

        $response =
            $this
                ->actingAs($user)
                ->post(
                    route('logout')
                );

        $response
            ->assertRedirect(
                route('login')
            );

        $this->assertGuest();

    }
);