# Installation Guide — Ujian CAT

Dokumen ini menjelaskan proses instalasi **Ujian CAT** untuk environment development/local tanpa Docker.

Aplikasi merupakan hasil modernisasi dari aplikasi CAT legacy dan tetap menggunakan struktur database legacy yang sudah ada.

---

## 1. Requirements

Pastikan perangkat sudah memiliki:

- PHP sesuai constraint Composer `^8.3`;
- PHP `>= 8.4.1` direkomendasikan untuk menyamakan dengan validasi pre-commit repository saat ini;
- Composer 2.x;
- MySQL;
- Node.js dan npm;
- Git.

Cek versi:

```bash
php -v
composer --version
mysql --version
node -v
npm -v
git --version
```

---

## 2. Clone Repository

```bash
git clone https://github.com/ajung5/Ujian_CAT.git
cd Ujian_CAT
```

---

## 3. Install PHP Dependencies

```bash
composer install
```

Jika proses berhasil, folder berikut akan dibuat:

```text
vendor/
```

---

## 4. Buat File Environment

Copy template:

```bash
cp .env.example .env
```

File `.env` digunakan untuk konfigurasi lokal dan **tidak boleh di-commit ke Git**.

---

## 5. Generate Application Key

```bash
php artisan key:generate
```

Verifikasi:

```bash
grep '^APP_KEY=' .env
```

`APP_KEY` harus terisi. Jangan membagikan atau memasukkan `APP_KEY` asli ke repository.

---

## 6. Konfigurasi Database

Edit `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujian
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan username dan password dengan MySQL lokal.

---

## 7. Siapkan Database Legacy

Aplikasi menggunakan database:

```text
ujian
```

Jika database belum dibuat:

```sql
CREATE DATABASE ujian
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Kemudian import backup database CAT legacy yang sesuai.

Contoh:

```bash
mysql -u root -p ujian < backup_ujian.sql
```

Nama file SQL pada contoh hanya contoh. Gunakan backup yang memang akan digunakan untuk environment tersebut.

---

## 8. Jangan Menjalankan Migration pada Database Legacy

Aplikasi ini tidak menggunakan migration Laravel sebagai sumber utama schema production legacy.

Jangan menjalankan:

```bash
php artisan migrate
```

atau:

```bash
php artisan migrate:fresh
```

terhadap database CAT existing tanpa review dan backup.

---

## 9. Install Frontend Dependencies

```bash
npm install
```

Build asset:

```bash
npm run build
```

Untuk development frontend:

```bash
npm run dev
```

---

## 10. Clear Laravel Cache

```bash
php artisan optimize:clear
```

---

## 11. Jalankan Regression Test

Sebelum menjalankan aplikasi:

```bash
php artisan test
```

Baseline saat dokumentasi ini diperbarui:

```text
47 passed
0 failed
```

Testing menggunakan SQLite `:memory:` dan memiliki fail-safe agar schema regression tidak dibuat pada database MySQL development/production.

---

## 12. Jalankan Aplikasi

```bash
php artisan serve
```

Default URL:

```text
http://127.0.0.1:8000
```

Aplikasi dapat dijalankan langsung dengan `php artisan serve` dan tidak membutuhkan Docker.

---

## 13. Struktur Environment Development

Contoh:

```dotenv
APP_NAME="Ujian CAT"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujian
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Untuk production:

```dotenv
APP_ENV=production
APP_DEBUG=false
```

`APP_URL`, credential database, mail configuration, dan secret lain harus disesuaikan dengan server production.

---

## 14. Validasi Instalasi

```bash
php artisan about
php artisan route:list
php artisan test
composer validate
```

---

## 15. Aktifkan Pre-commit Hook

Repository menyediakan hook lokal:

```text
.githooks/pre-commit
```

Aktifkan sekali:

```bash
git config core.hooksPath .githooks
```

Hook menjalankan:

```text
PHP runtime validation
npm run format:check
git diff --cached --check
APP_ENV=testing php artisan test
```

---

## 16. Troubleshooting

### Missing APP_KEY

Jika muncul:

```text
No application encryption key has been specified.
```

jalankan:

```bash
php artisan key:generate
php artisan optimize:clear
```

### Database Connection Error

Periksa `.env` dan tes koneksi MySQL:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p
```

### Port 8000 Sudah Digunakan

```bash
php artisan serve --port=8080
```

Akses:

```text
http://127.0.0.1:8080
```

### Perubahan `.env` Tidak Terbaca

```bash
php artisan optimize:clear
```

Kemudian restart development server.

### Composer Dependency Bermasalah

```bash
composer install
composer validate
```

Hindari menghapus `composer.lock` tanpa alasan yang jelas.

### Frontend Asset Tidak Muncul

```bash
npm install
npm run build
php artisan optimize:clear
```

Kemudian lakukan hard refresh pada browser.

---

## 17. Security Notes

Jangan commit:

```text
.env
APP_KEY
password database
credential layanan eksternal
private key
token API
```

Sebelum commit:

```bash
git status
```

Pastikan `.env` tidak masuk staged files.

---

## 18. Development Validation

Setelah perubahan source code:

```bash
npm run format:check
php artisan test
git diff --check
```

Tambahan bila diperlukan:

```bash
composer validate
composer audit
npm audit
```

Hindari:

```bash
npm audit fix --force
```

tanpa review dependency dan regression test.

---

## 19. Catatan Database Legacy

Tabel inti yang digunakan aplikasi antara lain:

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

Beberapa kolom menggunakan tipe dan constraint legacy untuk menjaga kompatibilitas dengan data aplikasi lama. Perubahan schema harus melalui review dan pengujian terlebih dahulu.

---

## 20. Quick Start

```bash
git clone https://github.com/ajung5/Ujian_CAT.git
cd Ujian_CAT

composer install

cp .env.example .env
php artisan key:generate

# konfigurasi database pada .env

npm install
npm run build

php artisan optimize:clear
php artisan test

php artisan serve
```

Akses:

```text
http://127.0.0.1:8000
```
