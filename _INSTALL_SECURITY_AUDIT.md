# Paket Security Audit Trail & Session Management

Baseline paket ini adalah Ujian_CAT `main` setelah fitur single-active-session siswa v2.2.1.

Referensi baseline GitHub saat paket dibuat: `062b6932a5530cc9b900a7bcfbab3fe68ab1f407`.

## Fitur

- Audit login berhasil/gagal dan logout.
- Audit penggantian serta invalidasi session siswa.
- Admin-only dashboard **Aktivitas Keamanan**.
- Filter event, role, user/email, dan IP.
- Ringkasan security event 24 jam.
- Riwayat IP dan user-agent login terakhir.
- Admin dapat **Paksa Logout** siswa.
- Token session asli tidak ditulis ke database; hanya SHA-256 fingerprint.
- Regression tests untuk alur audit dan force logout.

## Cara memasang

1. Pastikan source lokal sudah berada pada baseline v2.2.1 yang telah memiliki `active_session_hash` dan middleware `single.student.session`.
2. Buat backup atau branch terlebih dahulu:

```bash
git switch -c feat/security-audit-trail
```

3. Extract ZIP ini langsung ke root project `Ujian_CAT`, lalu overwrite file yang sama.
4. Jalankan migration:

```bash
php artisan migrate
```

5. Bersihkan cache aplikasi:

```bash
php artisan optimize:clear
```

6. Format source dengan tool project:

```bash
npx prettier --write \
app/Http/Controllers/Admin/SecurityEventController.php \
app/Http/Controllers/Auth/AuthController.php \
app/Http/Middleware/EnsureSingleStudentSession.php \
app/Models/User.php \
app/Models/UserSecurityEvent.php \
app/Services/SecurityEventLogger.php \
database/migrations/2026_09_25_110000_add_student_session_security_columns_to_users_table.php \
database/migrations/2026_09_25_110100_create_user_security_events_table.php \
resources/views/admin/security-events/index.blade.php \
resources/views/layouts/guru_baru.blade.php \
routes/web.php \
tests/Support/CreatesLegacySchema.php \
tests/Feature/SecurityAuditTrailTest.php
```

7. Jalankan test khusus:

```bash
php artisan test tests/Feature/SecurityAuditTrailTest.php
```

8. Jalankan seluruh quality gate:

```bash
php artisan test
composer analyse
npm run format:check
git diff --check
```

## Manual test

### Audit login

- Login sebagai siswa.
- Login sebagai guru/admin.
- Coba satu login dengan password salah.
- Login administrator dan buka menu **Aktivitas Keamanan**.
- Pastikan `LOGIN_SUCCESS` dan `LOGIN_FAILED` tercatat dengan IP, user-agent, role, dan waktu.

### Single-session

- Login siswa pada Device A.
- Login akun siswa yang sama pada Device B.
- Device A melakukan request berikutnya dan harus logout.
- Dashboard audit harus mencatat `SESSION_REPLACED` dan `SESSION_INVALIDATED`.

### Paksa logout admin

- Login siswa pada Device A.
- Login administrator pada browser lain.
- Buka **Aktivitas Keamanan**.
- Klik **Paksa Logout** pada siswa.
- Device A melakukan refresh/request berikutnya.
- Siswa harus diarahkan ke login dengan pesan bahwa session dihentikan administrator.
- Siswa dapat login kembali secara normal; login baru menghapus revocation marker.

## Verifikasi database

```sql
SHOW COLUMNS FROM users
WHERE Field IN (
    'active_session_hash',
    'student_session_revoked_at',
    'last_login_at',
    'last_login_ip',
    'last_login_user_agent'
);

SHOW TABLES LIKE 'user_security_events';

SELECT
    id,
    user_id,
    actor_user_id,
    email,
    role,
    event,
    ip_address,
    created_at
FROM user_security_events
ORDER BY id DESC
LIMIT 20;
```

## Changelog

Isi `CHANGELOG_SECURITY_AUDIT.md` dapat dipindahkan ke bagian `Unreleased` pada `CHANGELOG.md` utama setelah seluruh test lolos.

## Commit yang disarankan

```bash
git add .
git commit -m "security: add audit trail and student session management"
```
