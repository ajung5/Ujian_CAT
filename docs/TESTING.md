# Testing — Ujian CAT

## Menjalankan Test

```bash
php artisan test
```

Baseline saat dokumentasi ini diperbarui:

```text
47 passed
0 failed
```

## Database Testing

Feature test menggunakan SQLite `:memory:` dengan schema legacy yang dibuat oleh:

```text
tests/Support/CreatesLegacySchema.php
```

Database MySQL development/production `ujian` **tidak boleh digunakan oleh regression test**.

Pengamanan test berada pada beberapa lapisan:

- `phpunit.xml` memaksa `APP_ENV=testing` dan `DB_CONNECTION=sqlite`.
- `tests/TestCase.php` memaksa environment testing sebelum application test diinisialisasi serta mengatur SQLite `:memory:` sebagai default connection.
- `tests/Support/CreatesLegacySchema.php` melakukan safety abort bila schema test tidak berjalan pada SQLite `:memory:`.

Pengamanan ini penting karena schema helper bersifat destructive terhadap database tempat ia dijalankan.

## Suite Utama

### AuthenticationAndRoleTest

Mencakup guest access, role redirect, login Guru/Siswa, POST logout, dan penolakan GET logout.

### CandidateAcceptanceTest

Mencakup penerimaan Calon Siswa (`C`) menjadi Siswa (`S`) pada record yang sama, NIS final, kelas final, validation, transaction, dan activity logging.

### CandidateAccessRestrictionTest

Mencakup pembatasan akses Calon Siswa terhadap Ujian, hasil assessment, Latihan, dan endpoint assessment lain yang hanya diperbolehkan untuk Siswa.

### StudentDataTabsTest

Mencakup pemisahan data Siswa dan Calon Siswa pada tab/filter manajemen data peserta.

### StudentAssessmentAccessTest

Mencakup distribusi Ujian per kelas, pemisahan endpoint Ujian/Latihan, dan akses assessment yang relevan.

### TeacherAssessmentManagementTest

Mencakup delete Paket Soal, dependency cleanup, historical protection, ownership/IDOR, historical class result, dan isolasi hasil antar paket.

### StudentAssessmentLifecycleTest

Mencakup start, save answer, finish, unanswered score 0, proteksi setelah final, cross-package IDOR, timer expiry, dan timer isolation.

## Menjalankan Satu File

```bash
php artisan test tests/Feature/StudentAssessmentLifecycleTest.php
```

## Filter

```bash
php artisan test --filter="timer"
```

## Stress Check Environment Isolation

Test suite seharusnya tetap memaksa environment testing walaupun shell memiliki `APP_ENV` lain.

Contoh verifikasi:

```bash
APP_ENV=local php artisan test
```

Hasil yang diharapkan tetap menggunakan SQLite `:memory:` dan regression suite tetap berjalan aman.

## Validation Sebelum Commit

```bash
npm run format:check
php artisan test
git diff --check
```

Jika pre-commit hook repository sudah diaktifkan:

```bash
git config core.hooksPath .githooks
```

maka validation utama dijalankan otomatis ketika commit.

Setiap bug kritis yang diperbaiki sebaiknya mendapat regression test baru.
