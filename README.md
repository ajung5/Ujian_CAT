# Ujian CAT

Aplikasi **Computer Assisted Test (CAT)** berbasis Laravel untuk pengelolaan ujian, latihan, materi pembelajaran, peserta, kelas, serta laporan hasil assessment.

Project ini merupakan hasil modernisasi aplikasi CAT legacy dari **Laravel 5.1** ke **Laravel 13** dengan prinsip utama mempertahankan proses bisnis, UI/UX utama, dan kompatibilitas database legacy sambil memperkuat security, maintainability, testing, serta kualitas source code.

## Status Project

| Komponen | Status |
|---|---|
| Framework | Laravel 13 |
| Database | MySQL legacy `ujian` |
| Runtime PHP | `^8.3` pada Composer; pre-commit project memvalidasi PHP `>= 8.4.1` |
| Frontend | Blade, Bootstrap, jQuery, Vite |
| Import / Export | PhpSpreadsheet |
| Image Processing | Intervention Image |
| Testing | Pest / PHPUnit |
| Regression Baseline | **47 passed, 0 failed** |
| Docker | Tidak diperlukan |

Aplikasi dapat dijalankan secara lokal menggunakan:

```bash
php artisan serve
```

Default development URL:

```text
http://127.0.0.1:8000
```

---

## Fitur Utama

### Administrator / Guru

- Dashboard Guru / Administrator.
- Manajemen profil dan identitas sekolah.
- Manajemen data Guru.
- Manajemen Kelas.
- Manajemen Siswa dan Calon Siswa melalui tab terpisah.
- Penerimaan Calon Siswa menjadi Siswa tanpa membuat record user baru.
- Penetapan NIS final dan kelas saat penerimaan Calon Siswa.
- Import data siswa dari Excel.
- Manajemen Materi pembelajaran.
- Manajemen Paket Soal.
- Pemisahan jenis Paket Soal **Ujian** dan **Latihan**.
- Manajemen Detail Soal.
- Import soal dari Excel.
- Upload dan penghapusan audio soal.
- Distribusi Ujian ke kelas.
- Laporan hasil Ujian dan Latihan.
- Detail hasil per kelas dan per siswa.
- Export hasil per kelas ke Excel.
- Penghapusan hasil siswa / kelas dengan proteksi histori.
- Activity logging untuk operasi administratif penting.

### Siswa

- Dashboard siswa.
- Daftar Ujian berdasarkan distribusi kelas.
- Engine Ujian dengan timer server-side.
- Penyimpanan jawaban selama assessment.
- Finalisasi assessment.
- Finalisasi otomatis ketika waktu habis.
- Proteksi perubahan jawaban setelah assessment final.
- Hasil Ujian.
- Review jawaban.
- Materi pembelajaran.
- Daftar dan engine Latihan.
- Profil siswa.

### Calon Siswa

Role Calon Siswa menggunakan status `C` dan dibatasi hanya pada fitur yang memang diperbolehkan, seperti dashboard/profil yang relevan. Calon Siswa tidak memperoleh akses ke flow assessment Siswa sebelum diterima menjadi status `S`.

---

## Role Pengguna

| Status | Role |
|---|---|
| `A` | Administrator |
| `G` | Guru |
| `S` | Siswa |
| `C` | Calon Siswa |

Authorization diterapkan pada route dan resource. Validasi akses tidak hanya mengandalkan UI, tetapi juga dilakukan pada server-side controller/middleware dan ownership resource.

---

## Requirements

Pastikan environment development memiliki:

```text
PHP
Composer 2.x
MySQL
Node.js
npm
Git
```

Constraint PHP pada `composer.json`:

```text
^8.3
```

Untuk menyamakan dengan validasi pre-commit repository saat ini, gunakan PHP:

```text
>= 8.4.1
```

Cek environment:

```bash
php -v
composer --version
mysql --version
node -v
npm -v
git --version
```

---

## Quick Start

```bash
git clone https://github.com/ajung5/Ujian_CAT.git
cd Ujian_CAT

composer install

cp .env.example .env
php artisan key:generate

npm install
npm run build

php artisan optimize:clear
php artisan test
php artisan serve
```

Sesuaikan koneksi MySQL pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujian
DB_USERNAME=root
DB_PASSWORD=
```

Aplikasi tidak membutuhkan Docker untuk development lokal.

Panduan lebih lengkap tersedia pada [`docs/INSTALLATION.md`](docs/INSTALLATION.md).

---

## Database Legacy

Aplikasi menggunakan **database CAT existing** dengan nama default:

```text
ujian
```

Tabel inti yang digunakan antara lain:

```text
users
kelas
schools
materis
soals
detailsoals
distribusisoals
jawabs
countexamtimes
aktifitas
```

### Peringatan Database

Jangan menjalankan perintah berikut terhadap database legacy yang berisi data existing tanpa review schema dan backup:

```bash
php artisan migrate
php artisan migrate:fresh
```

Aplikasi ini mempertahankan struktur database legacy sebagai compatibility boundary. Beberapa field masih menggunakan tipe/constraint dari aplikasi lama, sehingga perubahan schema harus dilakukan secara terkontrol.

Dokumentasi database: [`docs/DATABASE.md`](docs/DATABASE.md).

---

## Testing

Jalankan seluruh regression suite:

```bash
php artisan test
```

Baseline repository saat dokumentasi ini diperbarui:

```text
47 passed
0 failed
```

Suite utama saat ini:

```text
AuthenticationAndRoleTest
CandidateAcceptanceTest
CandidateAccessRestrictionTest
StudentAssessmentAccessTest
StudentAssessmentLifecycleTest
StudentDataTabsTest
TeacherAssessmentManagementTest
```

Regression test mencakup antara lain:

- authentication dan role redirect;
- POST-only logout;
- akses assessment berdasarkan role;
- pembatasan Calon Siswa;
- penerimaan Calon Siswa menjadi Siswa;
- pemisahan tab Siswa / Calon Siswa;
- distribusi Ujian;
- pemisahan Ujian / Latihan;
- assessment lifecycle;
- server-side timer dan expiry;
- finalisasi jawaban;
- cross-package protection;
- ownership / IDOR protection;
- historical class result;
- proteksi penghapusan Paket Soal dan hasil assessment;
- isolasi timer dan data antar assessment.

### Safety Test Database

Regression test **tidak boleh menggunakan MySQL development/production**.

Test menggunakan:

```text
SQLite :memory:
```

`phpunit.xml`, `tests/TestCase.php`, dan `tests/Support/CreatesLegacySchema.php` memiliki pengamanan untuk memastikan test schema hanya dibuat pada SQLite in-memory. `APP_ENV=testing` juga dipaksa pada test bootstrap agar environment shell/VS Code tidak mengalihkan test ke database development.

Dokumentasi testing: [`docs/TESTING.md`](docs/TESTING.md).

---

## Security Controls

Kontrol keamanan yang sudah diterapkan antara lain:

- authentication middleware;
- role-based authorization;
- resource ownership validation;
- CSRF protection;
- POST-only logout;
- login rate limiting;
- IDOR protection;
- server-side assessment timer;
- validasi Paket Soal dan Detail Soal;
- cross-package assessment protection;
- proteksi historical result;
- transaction untuk operasi database kritis;
- validasi upload file;
- pembatasan akses Calon Siswa;
- regression test untuk critical assessment flow.

Jangan pernah commit:

```text
.env
APP_KEY
password database
API token
private key
production credential
```

Lihat [`SECURITY.md`](SECURITY.md) untuk kebijakan keamanan repository.

---

## Source Code Quality

Project menggunakan Prettier untuk PHP, Blade, dan frontend source, ditambah Blade whitespace cleanup khusus project.

Format seluruh first-party source:

```bash
npm run format
```

Periksa formatting tanpa melakukan perubahan:

```bash
npm run format:check
```

Pemeriksaan whitespace Git:

```bash
git diff --check
```

Controller menggunakan facade Laravel secara eksplisit, termasuk `Auth` dan `DB`, agar source lebih mudah dianalisis oleh IDE/static analyzer seperti Intelephense.

Dokumentasi style: [`docs/CODE-STYLE.md`](docs/CODE-STYLE.md).

---

## Pre-commit Hook

Repository menyediakan hook pada:

```text
.githooks/pre-commit
```

Aktifkan sekali pada clone lokal:

```bash
git config core.hooksPath .githooks
```

Sebelum commit, hook akan menjalankan:

```text
PHP runtime validation
npm run format:check
git diff --cached --check
APP_ENV=testing php artisan test
```

Dengan konfigurasi repository saat ini, hook memerlukan PHP `>= 8.4.1`.

---

## Development Validation

Sebelum commit atau pull request, minimal jalankan:

```bash
npm run format:check
php artisan test
git diff --check
```

Audit dependency bila diperlukan:

```bash
composer validate
composer audit
npm audit
```

Hindari:

```bash
npm audit fix --force
```

tanpa review dependency dan regression test karena dapat menimbulkan breaking changes.

---

## Struktur Utama Project

```text
app/
├── Http/
│   ├── Controllers/
│   └── Middleware/
└── Models/

resources/
└── views/
    ├── guru/
    ├── siswa/
    └── layouts/

routes/
└── web.php

tests/
├── Feature/
└── Support/

docs/
├── ARCHITECTURE.md
├── CODE-STYLE.md
├── DATABASE.md
├── DEPLOYMENT.md
├── INSTALLATION.md
├── MIGRATION-NOTES.md
├── OPERATIONS.md
└── TESTING.md

public/
├── css/
├── js/
├── img/
└── assets/
```

Folder `public/` masih memuat sejumlah asset legacy/third-party sehingga tidak seluruh isinya menjadi target formatter first-party.

---

## Catatan Modernisasi

Aplikasi berasal dari CAT legacy berbasis Laravel 5.1.

```text
Business Process  -> dipertahankan
Database          -> dipertahankan
UI / UX utama     -> dipertahankan
Framework         -> Laravel 13
Security          -> diperkuat
Testing           -> ditambahkan dan diperluas
Code Quality      -> distandardisasi
```

Komponen legacy yang telah diganti atau dibersihkan antara lain:

- legacy authentication flow;
- raw `mysqli` access;
- LaravelCollective HTML;
- PHPExcel;
- GET logout;
- view legacy yang tidak digunakan;
- dependency frontend legacy yang tidak diperlukan.

Import/export Excel sekarang menggunakan **PhpSpreadsheet**.

---

## Dokumentasi

Dokumentasi teknis repository:

- [`docs/INSTALLATION.md`](docs/INSTALLATION.md) — instalasi development.
- [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) — deployment/production readiness.
- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) — arsitektur aplikasi.
- [`docs/DATABASE.md`](docs/DATABASE.md) — database legacy.
- [`docs/TESTING.md`](docs/TESTING.md) — regression testing.
- [`docs/CODE-STYLE.md`](docs/CODE-STYLE.md) — formatting dan source style.
- [`docs/OPERATIONS.md`](docs/OPERATIONS.md) — operasi aplikasi.
- [`docs/MIGRATION-NOTES.md`](docs/MIGRATION-NOTES.md) — catatan modernisasi legacy.
- [`SECURITY.md`](SECURITY.md) — security policy.
- [`CONTRIBUTING.md`](CONTRIBUTING.md) — workflow kontribusi.
- [`CHANGELOG.md`](CHANGELOG.md) — ringkasan perubahan.

---

## Repository

```text
https://github.com/ajung5/Ujian_CAT
```

---

## Status Modernisasi

```text
Laravel 5.1 Legacy
        |
        v
Laravel 13
        |
        v
Security Hardening
        |
        v
Assessment Regression Testing
        |
        v
Candidate Workflow & Access Control
        |
        v
Formatting / Static Analysis Cleanup
        |
        v
Production Readiness
```
