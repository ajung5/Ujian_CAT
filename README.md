# Ujian CAT

Aplikasi **Computer Assisted Test (CAT)** berbasis Laravel untuk pengelolaan ujian, latihan, materi pembelajaran, peserta, kelas, serta laporan hasil ujian.

Project ini merupakan hasil modernisasi aplikasi CAT legacy dari Laravel 5.1 ke Laravel 13 dengan mempertahankan proses bisnis, struktur database utama, serta alur penggunaan aplikasi.

---

## Teknologi

- PHP 8.3+
- Laravel 13
- MySQL
- Blade
- Bootstrap
- jQuery
- Vite
- PhpSpreadsheet
- Intervention Image
- Pest / PHPUnit

Aplikasi dapat dijalankan langsung menggunakan:

```bash
php artisan serve
```

Docker tidak diperlukan.

---

## Fitur Utama

### Guru / Administrator

- Dashboard
- Manajemen data Guru
- Manajemen Kelas
- Manajemen Siswa dan Calon Siswa
- Import data siswa dari Excel
- Manajemen Materi
- Manajemen Paket Soal
- Manajemen Detail Soal
- Import soal dari Excel
- Upload audio soal
- Distribusi ujian ke kelas
- Laporan hasil ujian dan latihan
- Export hasil per kelas ke Excel
- Pengelolaan hasil siswa

### Siswa

- Dashboard
- Daftar ujian berdasarkan distribusi kelas
- Engine ujian dengan timer server-side
- Penyimpanan jawaban
- Finalisasi otomatis ketika waktu habis
- Hasil ujian
- Review jawaban
- Materi pembelajaran
- Latihan
- Profil siswa

---

## Role Pengguna

| Status | Role |
|---|---|
| `A` | Administrator |
| `G` | Guru |
| `S` | Siswa |
| `C` | Calon Siswa |

Authorization diterapkan pada route dan resource yang diakses oleh masing-masing role.

---

## Instalasi Singkat

```bash
git clone https://github.com/ajung5/Ujian_CAT.git
cd Ujian_CAT
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan optimize:clear
php artisan serve
```

Sesuaikan konfigurasi MySQL pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujian
DB_USERNAME=root
DB_PASSWORD=
```

Default development URL:

```text
http://127.0.0.1:8000
```

---

## Database

Aplikasi menggunakan **database legacy CAT** dengan database default:

```text
ujian
```

> **Penting:** jangan menjalankan `php artisan migrate` terhadap database produksi legacy tanpa proses review terlebih dahulu.

Beberapa tabel utama:

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

---

## Testing

```bash
php artisan test
```

Current regression suite:

```text
30 passed
0 failed
```

Regression test mencakup authentication, role authorization, distribusi ujian, latihan, ownership/IDOR protection, integritas penghapusan Paket Soal, historical class result, lifecycle ujian, timer expiry, cross-package protection, dan isolasi timer.

Database testing menggunakan SQLite terisolasi dan tidak menggunakan database produksi.

---

## Security

Kontrol keamanan yang sudah diterapkan antara lain:

- Authentication middleware
- Role-based authorization
- Resource ownership validation
- CSRF protection
- POST-only logout
- Login rate limiting
- IDOR protection
- Server-side assessment timer
- Validasi Paket Soal dan Detail Soal
- Proteksi hasil ujian historis
- Transaction untuk operasi database kritis
- Validasi upload file
- Regression test untuk critical assessment flow

Jangan pernah commit file `.env`, `APP_KEY`, password database, maupun credential lainnya.

---

## Struktur Utama Project

```text
app/
├── Http/
│   ├── Controllers/
│   └── Middleware/
├── Models/
└── ...

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

public/
├── css/
├── js/
├── img/
└── assets/
```

---

## Catatan Migrasi

Aplikasi ini berasal dari aplikasi CAT legacy berbasis Laravel 5.1.

```text
Business Process  → dipertahankan
Database          → dipertahankan
UI / UX           → dipertahankan
Security          → diperkuat
Framework         → dimodernisasi
Testing           → ditambahkan
```

Beberapa komponen legacy telah diganti atau dihapus:

- Legacy authentication
- Raw `mysqli`
- LaravelCollective HTML
- PHPExcel
- Dependency JavaScript yang tidak digunakan
- GET logout
- View legacy yang sudah tidak digunakan

Export Excel sekarang menggunakan **PhpSpreadsheet**.

---

## Development

```bash
php artisan optimize:clear
php artisan test
./vendor/bin/pint
composer validate
composer audit
npm audit
```

Hindari `npm audit fix --force` tanpa review karena dapat menghasilkan breaking changes.

---

## Repository

https://github.com/ajung5/Ujian_CAT

---

## Status Modernisasi

```text
Laravel Legacy 5.1
       ↓
Laravel 13
       ↓
Security Hardening
       ↓
Regression Testing
       ↓
Performance & Database Optimization
```
