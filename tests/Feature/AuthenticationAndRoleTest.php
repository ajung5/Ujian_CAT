<?php

use App\Models\Aktifitas;
use App\Models\Kelas;
use Illuminate\Routing\Middleware\ThrottleRequests;

beforeEach(function () {
    $this->withoutMiddleware(ThrottleRequests::class);
});

test('guest tidak dapat membuka area guru', function () {
    $response = $this->get('/guru');

    $response->assertRedirect(route('login'));
});

test('guest tidak dapat membuka area siswa', function () {
    $response = $this->get('/siswa');

    $response->assertRedirect(route('login'));
});

test('guru diarahkan dari area siswa ke dashboard guru', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    Aktifitas::query()->create([
        'id_user' => $guru->id,
        'nama' => 'Test activity',
    ]);

    $response = $this->actingAs($guru)->get('/siswa');

    $response->assertRedirect('/guru');
});

test('siswa diarahkan dari area guru ke dashboard siswa', function () {
    $kelas = Kelas::query()->create([
        'nama' => 'Kelas Test',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'id_kelas' => $kelas->id,
    ]);

    $response = $this->actingAs($siswa)->get('/guru');

    $response->assertRedirect('/siswa');
});

test('root mengarahkan guru ke dashboard guru', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $response = $this->actingAs($guru)->get('/');

    $response->assertRedirect(route('guru.index'));
});

test('root mengarahkan siswa ke dashboard siswa', function () {
    $siswa = $this->createUser([
        'status' => 'S',
    ]);

    $response = $this->actingAs($siswa)->get('/');

    $response->assertRedirect(route('siswa.index'));
});

test('login guru berhasil dan diarahkan ke dashboard guru', function () {
    $guru = $this->createUser([
        'status' => 'G',
        'email' => 'guru@example.test',
    ]);

    $response = $this->post('/auth/login', [
        'email' => ' GURU@EXAMPLE.TEST ',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('guru.index'));

    $this->assertAuthenticatedAs($guru);
});

test('login siswa berhasil dan diarahkan ke dashboard siswa', function () {
    $siswa = $this->createUser([
        'status' => 'S',
        'email' => 'siswa@example.test',
    ]);

    $response = $this->post('/auth/login', [
        'email' => 'siswa@example.test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('siswa.index'));

    $this->assertAuthenticatedAs($siswa);
});

test('login siswa menyimpan active session hash', function () {
    $siswa = $this->createUser([
        'status' => 'S',
        'email' => 'single-session@example.test',
    ]);

    $response = $this->post(route('login.process'), [
        'email' => 'single-session@example.test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('siswa.index'));

    $this->assertAuthenticatedAs($siswa);

    $siswa->refresh();

    expect($siswa->active_session_hash)->not->toBeNull();

    expect(strlen((string) $siswa->active_session_hash))->toBe(64);
});

test('login siswa terbaru mengganti active session sebelumnya', function () {
    $siswa = $this->createUser([
        'status' => 'S',
        'email' => 'latest-session@example.test',
    ]);

    $oldSessionHash = hash('sha256', 'session-perangkat-lama');

    $siswa
        ->forceFill([
            'active_session_hash' => $oldSessionHash,
        ])
        ->saveQuietly();

    $response = $this->post(route('login.process'), [
        'email' => 'latest-session@example.test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('siswa.index'));

    $siswa->refresh();

    expect($siswa->active_session_hash)->not->toBe($oldSessionHash);

    expect(strlen((string) $siswa->active_session_hash))->toBe(64);
});

test('remember me dinonaktifkan dan token lama siswa dihapus', function () {
    $siswa = $this->createUser([
        'status' => 'S',
        'email' => 'remember-siswa@example.test',
    ]);

    $siswa->setRememberToken('legacy-remember-token');

    $siswa->saveQuietly();

    expect($siswa->remember_token)->toBe('legacy-remember-token');

    $response = $this->post(route('login.process'), [
        'email' => 'remember-siswa@example.test',
        'password' => 'password123',
        'remember' => '1',
    ]);

    $response->assertRedirect(route('siswa.index'));

    $siswa->refresh();

    expect($siswa->remember_token)->toBeNull();
});

test('session siswa lama ditolak jika akun login di perangkat lain', function () {
    $siswa = $this->createUser([
        'status' => 'S',
    ]);

    /*
     * Simulasikan hash session milik login terbaru
     * dari perangkat lain.
     */
    $siswa
        ->forceFill([
            'active_session_hash' => hash('sha256', 'session-perangkat-baru'),
        ])
        ->saveQuietly();

    /*
     * actingAs menggunakan session request test
     * yang berbeda dari hash di atas.
     *
     * Middleware harus mendeteksi mismatch.
     */
    $response = $this->actingAs($siswa)->get(route('siswa.index'));

    $response->assertRedirect(route('login'))->assertSessionHasErrors([
        'email' => 'Sesi Anda telah berakhir karena akun ini digunakan untuk login di perangkat lain.',
    ]);

    $this->assertGuest();
});

test('logout hanya menerima post', function () {
    $user = $this->createUser([
        'status' => 'S',
    ]);

    $this->actingAs($user)->get('/auth/logout')->assertStatus(405);
});

test('user dapat logout menggunakan post', function () {
    $user = $this->createUser([
        'status' => 'S',
    ]);

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));

    $this->assertGuest();
});
