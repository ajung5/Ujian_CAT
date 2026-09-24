<?php

test('administrator dapat membuka halaman changelog', function () {
    $admin = $this->createUser([
        'status' => 'A',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.changelog'));

    $response
        ->assertOk()
        ->assertSee('Riwayat Perubahan Aplikasi')
        ->assertSee('v2.2.0')
        ->assertSee('Legacy Baseline')
        ->assertSee('multi-attempt');
});

test('guru tidak dapat membuka halaman changelog administrator', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $response = $this->actingAs($guru)->get(route('admin.changelog'));

    $response->assertRedirect('/guru');
});

test('siswa tidak dapat membuka halaman changelog administrator', function () {
    $siswa = $this->createUser([
        'status' => 'S',
    ]);

    $response = $this->actingAs($siswa)->get(route('admin.changelog'));

    $response->assertRedirect('/siswa');
});
