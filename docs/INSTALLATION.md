# Installation Guide — Ujian CAT

Dokumen ini menjelaskan proses instalasi **Ujian CAT** untuk environment development/local tanpa Docker.

Aplikasi merupakan hasil modernisasi dari aplikasi CAT legacy dan tetap menggunakan struktur database legacy yang sudah ada.

---

## 1. Requirements

Pastikan perangkat sudah memiliki:

- PHP 8.3 atau lebih baru
- Composer 2.x
- MySQL
- Node.js dan npm
- Git

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

Pastikan `.env` tercantum di `.gitignore`.

---

## 5. Generate Application Key

```bash
php artisan key:generate
```

Verifikasi:

```bash
grep '^APP_KEY=' .env
```

`APP_KEY` harus terisi.

Jangan membagikan atau memasukkan `APP_KEY` asli ke repository.

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

Kemudian import database CAT legacy yang digunakan oleh aplikasi sebelumnya.

Contoh:

```bash
mysql -u root -p ujian < backup_ujian.sql
```

> Nama file SQL pada contoh di atas hanya contoh. Gunakan file backup database yang Anda miliki.

---

## 8. Jangan Menjalankan Migration pada Database Legacy

Aplikasi ini tidak menggunakan migration Laravel sebagai sumber utama schema production.

Jangan menjalankan:

```bash
php artisan migrate
```

atau:

```bash
php artisan migrate:fresh
```

terhadap database CAT yang berisi data existing.

Perintah tersebut dapat menyebabkan perubahan atau kehilangan struktur/data database legacy.

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

Setelah instalasi atau perubahan konfigurasi:

```bash
php artisan optimize:clear
```

---

## 11. Jalankan Regression Test

Sebelum menjalankan aplikasi:

```bash
php artisan test
```

Baseline saat dokumentasi ini dibuat:

```text
30 passed
0 failed
```

Testing menggunakan database SQLite terisolasi dan tidak menggunakan database production/local `ujian`.

---

## 12. Jalankan Aplikasi

```bash
php artisan serve
```

Default URL:

```text
http://127.0.0.1:8000
```

Aplikasi dapat dijalankan menggunakan `php artisan serve` dan tidak membutuhkan Docker.

---

## 13. Struktur Environment yang Direkomendasikan

Contoh konfigurasi development:

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

`APP_URL`, database credential, mail configuration, dan secret lainnya harus disesuaikan dengan server production.

---

## 14. Validasi Instalasi

Jalankan:

```bash
php artisan about
```

Kemudian:

```bash
php artisan route:list
```

Pastikan route aplikasi berhasil dimuat.

Lanjutkan:

```bash
php artisan test
```

dan:

```bash
composer validate
```

---

## 15. Troubleshooting

### Missing APP_KEY

Jika muncul error:

```text
No application encryption key has been specified.
```

Jalankan:

```bash
php artisan key:generate
php artisan optimize:clear
```

Kemudian restart:

```bash
php artisan serve
```

---

### Database Connection Error

Periksa `.env`:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujian
DB_USERNAME=root
DB_PASSWORD=
```

Tes koneksi MySQL:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p
```

---

### Port 8000 Sudah Digunakan

Gunakan port lain:

```bash
php artisan serve --port=8080
```

Akses:

```text
http://127.0.0.1:8080
```

---

### Perubahan `.env` Tidak Terbaca

Jalankan:

```bash
php artisan optimize:clear
```

Kemudian restart development server.

---

### Composer Dependency Bermasalah

Jalankan:

```bash
composer install
composer validate
```

Hindari menghapus `composer.lock` tanpa alasan yang jelas karena file tersebut menjaga versi dependency tetap konsisten.

---

### Frontend Asset Tidak Muncul

Jalankan:

```bash
npm install
npm run build
```

Kemudian:

```bash
php artisan optimize:clear
```

Reload browser dengan hard refresh.

---

## 16. Security Notes

Jangan commit file atau data berikut:

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

## 17. Development Validation

Setelah melakukan perubahan source code:

```bash
php artisan optimize:clear
php artisan test
./vendor/bin/pint
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

## 18. Catatan Database Legacy

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

Beberapa kolom menggunakan tipe dan constraint legacy untuk menjaga kompatibilitas dengan data aplikasi lama.

Perubahan schema harus dilakukan melalui proses review dan pengujian terlebih dahulu.

---

## 19. Quick Start

Setelah database tersedia, instalasi development secara ringkas:

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
