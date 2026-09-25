<?php

use App\Models\UserSecurityEvent;
use Illuminate\Routing\Middleware\ThrottleRequests;

beforeEach(function () {
    $this->withoutMiddleware(ThrottleRequests::class);
});

test('login berhasil dicatat pada security audit trail', function () {
    $siswa = $this->createUser([
        'status' => 'S',
        'email' => 'audit-login@example.test',
    ]);

    $response = $this->post(route('login.process'), [
        'email' => 'audit-login@example.test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('siswa.index'));

    $this->assertDatabaseHas('user_security_events', [
        'user_id' => $siswa->id,
        'email' => 'audit-login@example.test',
        'role' => 'S',
        'event' => UserSecurityEvent::LOGIN_SUCCESS,
    ]);

    $siswa->refresh();

    expect($siswa->last_login_at)->not->toBeNull();
    expect($siswa->active_session_hash)->not->toBeNull();
    expect($siswa->student_session_revoked_at)->toBeNull();
});

test('login gagal dicatat tanpa menyimpan password', function () {
    $siswa = $this->createUser([
        'status' => 'S',
        'email' => 'audit-failed@example.test',
    ]);

    $response = $this->from(route('login'))->post(route('login.process'), [
        'email' => 'audit-failed@example.test',
        'password' => 'password-salah',
    ]);

    $response->assertRedirect(route('login'));

    $event = UserSecurityEvent::query()
        ->where('user_id', $siswa->id)
        ->where('event', UserSecurityEvent::LOGIN_FAILED)
        ->latest('id')
        ->firstOrFail();

    expect($event->metadata['reason'] ?? null)->toBe('invalid_credentials');
    expect(json_encode($event->metadata))->not->toContain('password-salah');
});

test('login siswa baru mencatat penggantian session lama', function () {
    $siswa = $this->createUser([
        'status' => 'S',
        'email' => 'audit-replace@example.test',
    ]);

    $siswa
        ->forceFill([
            'active_session_hash' => hash('sha256', 'session-lama'),
        ])
        ->saveQuietly();

    $response = $this->post(route('login.process'), [
        'email' => 'audit-replace@example.test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('siswa.index'));

    $this->assertDatabaseHas('user_security_events', [
        'user_id' => $siswa->id,
        'event' => UserSecurityEvent::SESSION_REPLACED,
    ]);
});

test('administrator dapat menandai session siswa untuk paksa logout', function () {
    $admin = $this->createUser([
        'status' => 'A',
        'email' => 'audit-admin@example.test',
    ]);

    $siswa = $this->createUser([
        'status' => 'S',
        'email' => 'audit-target@example.test',
    ]);

    $siswa
        ->forceFill([
            'active_session_hash' => hash('sha256', 'session-target'),
        ])
        ->saveQuietly();

    $response = $this->actingAs($admin)->post(route('admin.security.force-logout', $siswa));

    $response->assertRedirect();

    $siswa->refresh();

    expect($siswa->active_session_hash)->toBeNull();
    expect($siswa->student_session_revoked_at)->not->toBeNull();

    $this->assertDatabaseHas('user_security_events', [
        'user_id' => $siswa->id,
        'actor_user_id' => $admin->id,
        'event' => UserSecurityEvent::SESSION_FORCE_REQUESTED,
    ]);
});

test('session siswa yang direvoke administrator ditolak middleware', function () {
    $siswa = $this->createUser([
        'status' => 'S',
    ]);

    $siswa
        ->forceFill([
            'student_session_revoked_at' => now(),
        ])
        ->saveQuietly();

    $response = $this->actingAs($siswa)->get(route('siswa.index'));

    $response->assertRedirect(route('login'))->assertSessionHasErrors([
        'email' => 'Sesi Anda telah dihentikan oleh administrator. Silakan login kembali.',
    ]);

    $this->assertGuest();

    $this->assertDatabaseHas('user_security_events', [
        'user_id' => $siswa->id,
        'event' => UserSecurityEvent::SESSION_INVALIDATED,
    ]);
});

test('guru tidak dapat membuka halaman aktivitas keamanan administrator', function () {
    $guru = $this->createUser([
        'status' => 'G',
    ]);

    $response = $this->actingAs($guru)->get(route('admin.security.index'));

    $response->assertRedirect(route('guru.index'));
});
