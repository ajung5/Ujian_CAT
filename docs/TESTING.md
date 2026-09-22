# Testing — Ujian CAT

## Menjalankan Test

```bash
php artisan test
```

Baseline saat dokumentasi ini dibuat:

```text
30 passed
0 failed
```

## Database Testing

Feature test menggunakan SQLite terisolasi dengan schema legacy yang dibuat oleh `tests/Support/CreatesLegacySchema.php`. Database production `ujian` tidak digunakan.

## Suite Utama

### AuthenticationAndRoleTest

Mencakup guest access, role redirect, login Guru/Siswa, POST logout, dan penolakan GET logout.

### StudentAssessmentAccessTest

Mencakup distribusi ujian per kelas, pemisahan endpoint Ujian/Latihan, dan status materi latihan.

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

## Validation Sebelum Commit

```bash
php artisan optimize:clear
php artisan test
./vendor/bin/pint
composer validate
```

Setiap bug kritis yang diperbaiki sebaiknya mendapat regression test baru.
