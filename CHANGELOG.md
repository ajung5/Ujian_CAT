# Changelog

Seluruh perubahan penting Ujian CAT dicatat pada dokumen ini.

Format versi modern menggunakan Semantic Versioning:

```text
MAJOR.MINOR.PATCH
```

Aplikasi legacy sebelum modernisasi menggunakan baseline `v1.1`. Riwayat `v2.x` disusun berdasarkan milestone modernisasi dan histori repository.

---

## Unreleased

Belum ada perubahan aplikasi yang ditetapkan sebagai release berikutnya.

---

## v2.2.1 - 25 September 2026

Release ini berfokus pada **single active session siswa**, **security audit trail**, **administrative session control**, perbaikan navigasi assessment, penyempurnaan histori attempt, serta dukungan fresh installation.

### Added

- Single active session untuk akun Siswa (`S`).
- Kolom `active_session_hash` pada `users`.
- Middleware `EnsureSingleStudentSession`.
- Random student session token terpisah dari Laravel session ID.
- Server-side session revocation melalui `student_session_revoked_at`.
- Informasi `last_login_at`, `last_login_ip`, dan `last_login_user_agent`.
- Security audit trail melalui tabel `user_security_events`.
- Model `UserSecurityEvent`.
- Service `SecurityEventLogger`.
- Event audit:
  - `LOGIN_SUCCESS`;
  - `LOGIN_FAILED`;
  - `LOGOUT`;
  - `SESSION_REPLACED`;
  - `SESSION_INVALIDATED`;
  - `SESSION_FORCE_REQUESTED`;
  - `SESSION_LEGACY_CLAIMED`.
- Halaman Administrator **Aktivitas Keamanan**.
- Filter security event berdasarkan event, role, nama/email, dan IP.
- Ringkasan aktivitas keamanan 24 jam.
- Monitoring session siswa.
- Aksi Administrator **Paksa Logout** khusus Siswa.
- Regression test `SecurityAuditTrailTest`.
- Fresh database installer `database/install/ujian_cat_empty.sql`.
- Dokumentasi installer `database/install/README.md`.
- Installer fresh database hanya membuat satu Administrator dan tidak menyertakan data operasional/sample.

### Changed

- Login terbaru Siswa menjadi satu-satunya session siswa yang sah.
- Login siswa baru mengganti fingerprint session sebelumnya.
- Remember Me tidak diterapkan untuk Siswa.
- Legacy remember token siswa dibersihkan pada login.
- Database hanya menyimpan SHA-256 fingerprint student session token.
- Login Siswa menghapus status revocation sebelumnya dan mendaftarkan session baru.
- Login yang menggantikan session lama menghasilkan `SESSION_REPLACED`.
- Session invalid menghasilkan `SESSION_INVALIDATED` sebelum diputus.
- Session yang dipaksa logout Administrator tetap direvoke sampai Siswa login kembali secara sah.
- Akses Review Latihan dipusatkan pada **Riwayat Percobaan**.
- Riwayat attempt ditampilkan melalui modal.
- Tombol Review tersedia per attempt.
- Status maksimal tiga percobaan Latihan ditampilkan sebagai status.
- Flow **Kembali ke Soal Belum Dijawab** diperjelas.
- Navigasi unanswered dipertahankan sampai seluruh soal kosong selesai.
- Pada mobile, area pertanyaan kembali ke posisi soal ketika berpindah ke soal belum dijawab.
- `.gitignore` mengabaikan SQL dump umum namun mengizinkan fresh installer resmi repository.

### Fixed

- Modal Riwayat Percobaan tidak lagi terblokir konflik stacking context AdminPlus/Bootstrap.
- Modal histori dipindahkan ke level `<body>` saat aktif.
- Navigasi soal belum dijawab tidak kembali ke urutan normal terlalu dini.
- Siswa langsung diarahkan ke soal kosong berikutnya.
- Navigasi kembali normal setelah semua soal kosong terjawab.
- Kompatibilitas timestamp legacy `users` dengan MySQL modern.
- Regression single-session pada Ujian, Latihan, multi-attempt, review, dan timer.
- Authentication regression test tidak lagi terkena false-negative dari throttle global.

### Security

- Satu akun Siswa hanya dapat mempunyai satu session aktif.
- Login terbaru membatalkan validitas session sebelumnya.
- Token session asli tidak disimpan pada database atau audit trail.
- SHA-256 digunakan sebagai fingerprint session.
- Session fixation dimitigasi dengan regenerasi session setelah login.
- Remember Me dinonaktifkan untuk Siswa.
- Session lama tidak dapat membersihkan fingerprint session terbaru.
- Administrator dapat merevoke session Siswa.
- Revocation marker mencegah session lama mengklaim ulang session.
- Password tidak dicatat di security audit trail.
- Security event dan force logout hanya tersedia untuk Administrator (`A`).
- Login gagal dicatat tanpa menyimpan password.
- Login rate limiting tetap aktif.

### Testing

Regression diperluas untuk:

- active session fingerprint;
- session replacement;
- stale session rejection;
- Remember Me siswa;
- login success/failed audit;
- session replacement audit;
- admin force logout;
- revoked session rejection;
- Administrator-only security page;
- Ujian/Latihan setelah single-session;
- multi-attempt;
- review attempt;
- timer isolation;
- timer expiry;
- result history.

Repository saat ini mempunyai **69 skenario feature test**.

---

## v2.2.0 - 24 September 2026

Release ini memperkenalkan arsitektur **multi-attempt** untuk Latihan tanpa mengubah batas Ujian satu attempt.

### Added

- Tabel `assessment_attempts`.
- Attempt number per Siswa dan Paket Soal.
- Status `in_progress` dan `finished`.
- Nilai per attempt.
- `started_at` dan `finished_at`.
- Histori percobaan Latihan.
- Review jawaban berdasarkan attempt.
- Timer per attempt.
- Maksimal tiga attempt Latihan.

### Changed

- Ujian tetap maksimal satu attempt.
- Latihan maksimal tiga attempt.
- `jawabs` dan `countexamtimes` menggunakan `attempt_id`.
- Jawaban dan timer antar attempt terisolasi.
- Hasil utama Latihan menggunakan completed attempt terbaru.
- Review tersedia untuk Latihan berdasarkan attempt.

### Database

Unique timer berubah dari:

```text
id_soal + id_user
```

menjadi:

```text
attempt_id
```

Unique jawaban berubah dari:

```text
no_soal_id + id_soal + id_user
```

menjadi:

```text
attempt_id + no_soal_id
```

Data assessment legacy dimigrasikan sebagai Attempt #1. Timestamp legacy terkait dinormalisasi untuk MySQL strict mode.

### Security

- Attempt aktif divalidasi server-side.
- Cross-package answer protection.
- Attempt final tidak dapat dimodifikasi.
- Direct review Ujian dibatasi.
- Ownership hasil dan attempt divalidasi.

### Testing

Mencakup Attempt #1-#3, penolakan Attempt #4, isolasi jawaban, isolasi timer, review attempt, latest completed attempt, finalization protection, dan timer expiry.

---

## v2.1.0 - 22 September 2026

Release ini berfokus pada workflow Calon Siswa, authorization, testing isolation, dan quality gate.

### Added

- Role Calon Siswa (`C`).
- Workflow penerimaan Calon Siswa menjadi Siswa.
- Tab Data Siswa dan Calon Siswa.
- Import Calon Siswa.
- Penetapan NIS dan kelas final.
- SQLite `:memory:` regression testing.
- Fail-safe agar schema test tidak dibuat di MySQL development/production.
- Pre-commit hook.
- Larastan/PHPStan.
- Dokumentasi teknis.
- Index database.
- Admin-only Changelog page.

### Changed

- Calon Siswa menggunakan record yang sama ketika diterima.
- Assessment hanya tersedia untuk status `S`.
- Authorization route dan ownership diperketat.
- Query assessment legacy dioptimalkan.
- Formatting source distandardisasi.
- Authentication controller dimodernisasi.

### Fixed

- Isolasi test environment.
- Historical assessment access.
- Compatibility issue database legacy.
- Cross-role redirect.
- Candidate acceptance workflow.

### Security

- Calon Siswa tidak dapat mengakses assessment sebelum diterima.
- Ownership dan IDOR protection diperketat.
- Historical result diberi validasi tambahan.
- Logout menggunakan POST + CSRF.
- Login rate limiting aktif.

---

## v2.0.0 - 21 September 2026

Modernisasi utama dari Laravel 5.1 ke Laravel 13.

### Added

- Laravel 13.
- PHP runtime modern.
- Modern authentication.
- Role middleware.
- Regression testing.
- Server-side assessment timer.
- PhpSpreadsheet.
- Intervention Image.
- Dokumentasi instalasi, deployment, arsitektur, database, dan testing.

### Changed

- Area yang dimodernisasi berpindah dari raw access ke Eloquent/Query Builder.
- GET logout diganti POST + CSRF.
- PHPExcel diganti PhpSpreadsheet.
- Dependency obsolete dibersihkan.
- Controller dan route dimodernisasi.
- Business process utama dipertahankan.
- Database legacy dipertahankan sebagai compatibility boundary.
- UI/UX utama dipertahankan.

### Security

- Authentication middleware.
- Role-based authorization.
- CSRF.
- Login rate limiting.
- Ownership validation.
- IDOR protection.
- Cross-package protection.
- Server-side timer.
- Server-side finalization.

---

## v1.1 - Legacy Baseline

Baseline CAT sebelum modernisasi.

### Teknologi

- Laravel 5.1.
- PHP legacy.
- Blade.
- Bootstrap.
- jQuery.
- MySQL.
- PHPExcel.

### Fitur Dasar

- Login Administrator/Guru/Siswa.
- Dashboard.
- Manajemen Guru/Siswa/Kelas.
- Materi.
- Paket dan Detail Soal.
- Distribusi Soal.
- Ujian dan Latihan.
- Timer.
- Jawaban dan Hasil.
- Laporan.
- Import/Export.

### Legacy Characteristics

- Authentication legacy.
- Database schema legacy.
- Sebagian raw database access.
- GET logout.
- PHPExcel.
- Belum ada automated regression suite modern.
- Belum ada multi-attempt architecture.
- Belum ada single active student session.
- Belum ada security audit trail.

### Modernization Objective

```text
Business Process  → dipertahankan
Data Existing     → dipertahankan
UI / UX utama     → dipertahankan
Framework         → dimodernisasi
Security          → diperkuat
Testing           → diperluas
Maintainability   → ditingkatkan
```
