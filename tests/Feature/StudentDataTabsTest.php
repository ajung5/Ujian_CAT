<?php

use App\Models\Kelas;

/*
|--------------------------------------------------------------------------
| Data Siswa Tabs
|--------------------------------------------------------------------------
*/

test('tab default hanya menampilkan siswa berstatus s', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Tab Siswa',
    ]);

    $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
        'nama' => 'Siswa Tab Aktif',
    ]);

    $this->createUser([
        'status' => 'C',
        'id_kelas' => $kelas->id,
        'nama' => 'Calon Tidak Boleh Muncul',
    ]);

    $response = $this->actingAs($guru)->get(route('guru.siswa'));

    $response
        ->assertOk()
        ->assertSee('Siswa Tab Aktif')
        ->assertDontSee('Calon Tidak Boleh Muncul')
        ->assertViewHas('activeTab', 'siswa')
        ->assertViewHas('statusAktif', 'S');
});

test('tab calon siswa hanya menampilkan user berstatus c', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Tab Calon',
    ]);

    $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
        'nama' => 'Siswa Tidak Boleh Muncul',
    ]);

    $this->createUser([
        'status' => 'C',
        'id_kelas' => $kelas->id,
        'nama' => 'Calon Tab Aktif',
        'sekolah_asal' => 'SMP Tab Test',
    ]);

    $response = $this->actingAs($guru)->get(route('guru.siswa', ['tab' => 'calon']));

    $response
        ->assertOk()
        ->assertSee('Calon Tab Aktif')
        ->assertSee('SMP Tab Test')
        ->assertDontSee('Siswa Tidak Boleh Muncul')
        ->assertViewHas('activeTab', 'calon')
        ->assertViewHas('statusAktif', 'C');
});

test('tab tidak valid kembali ke tab siswa', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $this->actingAs($guru)
        ->get(route('guru.siswa', ['tab' => 'invalid']))
        ->assertOk()
        ->assertViewHas('activeTab', 'siswa')
        ->assertViewHas('statusAktif', 'S');
});

test('pencarian data siswa dibatasi oleh status tab aktif', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $this->createUser([
        'status' => 'S',
        'nama' => 'Nama Sama Siswa',
    ]);

    $this->createUser([
        'status' => 'C',
        'nama' => 'Nama Sama Calon',
    ]);

    $this->actingAs($guru)
        ->post(route('guru.siswa.search'), [
            'q' => 'Nama Sama',
            'status' => 'S',
        ])
        ->assertOk()
        ->assertSee('Nama Sama Siswa')
        ->assertDontSee('Nama Sama Calon');

    $this->actingAs($guru)
        ->post(route('guru.siswa.search'), [
            'q' => 'Nama Sama',
            'status' => 'C',
        ])
        ->assertOk()
        ->assertSee('Nama Sama Calon')
        ->assertDontSee('Nama Sama Siswa');
});

test('status pencarian selain s atau c ditolak', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $this->actingAs($guru)
        ->postJson(route('guru.siswa.search'), [
            'q' => '',
            'status' => 'A',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('status');
});

test('pagination calon siswa mempertahankan parameter tab', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    for ($i = 1; $i <= 11; $i++) {
        $this->createUser([
            'status' => 'C',
            'nama' => 'Calon Pagination ' . $i,
        ]);
    }

    $response = $this->actingAs($guru)->get(route('guru.siswa', ['tab' => 'calon']));

    $response->assertOk()->assertViewHas('users', function ($users) {
        $nextPageUrl = $users->nextPageUrl();

        return is_string($nextPageUrl) && str_contains($nextPageUrl, 'tab=calon');
    });
});
