# Deployment Guide — Ujian CAT

Dokumen ini menjelaskan proses deployment **Ujian CAT** ke environment production tanpa Docker.

Aplikasi menggunakan Laravel 13 dan database MySQL legacy. Proses deployment harus menjaga kompatibilitas database existing dan tidak menjalankan migration secara otomatis.

---

## 1. Prinsip Deployment

Deployment Ujian CAT mengikuti prinsip berikut:

```text
Source Code   → dapat diperbarui
Dependency    → di-install ulang sesuai lock file
.env          → tetap lokal di server
Database      → dipertahankan
Migration     → tidak dijalankan otomatis
APP_KEY       → tidak boleh berubah sembarangan
Testing       → wajib sebelum go-live
```

Hal yang paling penting:

> Jangan menjalankan `php artisan migrate`, `migrate:fresh`, atau perintah database destruktif terhadap database production legacy tanpa review terlebih dahulu.

---

## 2. Requirement Production

Minimum requirement:

- PHP 8.3+
- Composer 2.x
- MySQL
- Web server:
  - Nginx + PHP-FPM, atau
  - Apache
- Node.js dan npm untuk proses build
- Git
- HTTPS certificate untuk production

PHP extension yang umumnya diperlukan Laravel dan dependency project:

```text
ctype
curl
dom
fileinfo
filter
hash
mbstring
openssl
pdo
pdo_mysql
session
tokenizer
xml
xmlwriter
zip
gd atau imagick
```

Cek extension:

```bash
php -m
```

---

# BAGIAN A — DEPLOYMENT LINUX

## 3. Contoh Struktur Server

Contoh lokasi aplikasi:

```text
/var/www/Ujian_CAT
```

Clone repository:

```bash
cd /var/www

git clone https://github.com/ajung5/Ujian_CAT.git

cd Ujian_CAT
```

Jika repository sudah ada:

```bash
cd /var/www/Ujian_CAT

git pull origin main
```

---

## 4. Install PHP Dependencies

Untuk production gunakan:

```bash
composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction
```

Jangan menjalankan:

```bash
composer update
```

secara langsung di production tanpa review.

`composer install` akan mengikuti versi yang tercatat pada:

```text
composer.lock
```

---

## 5. Environment Production

File `.env` dibuat langsung di server production.

Contoh:

```dotenv
APP_NAME="Ujian CAT"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://ujian.example.go.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujian
DB_USERNAME=ujian_app
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_STORE=file
QUEUE_CONNECTION=sync
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

MAIL_MAILER=log

VITE_APP_NAME="${APP_NAME}"
```

### APP_KEY

Untuk instalasi baru:

```bash
php artisan key:generate
```

Untuk server yang sudah berjalan:

> Jangan mengganti `APP_KEY` tanpa perencanaan.

Perubahan `APP_KEY` dapat menyebabkan session, cookie terenkripsi, dan data terenkripsi Laravel tidak lagi dapat dibaca.

---

## 6. Database Production

Database default:

```text
ujian
```

Disarankan membuat account MySQL khusus aplikasi.

Contoh:

```sql
CREATE USER 'ujian_app'@'localhost'
IDENTIFIED BY 'PASSWORD_KUAT';

GRANT SELECT, INSERT, UPDATE, DELETE
ON ujian.*
TO 'ujian_app'@'localhost';

FLUSH PRIVILEGES;
```

Hak akses database sebaiknya menggunakan prinsip **least privilege**.

Jika kebutuhan aplikasi berubah, privilege dapat disesuaikan setelah diuji.

---

## 7. Backup Sebelum Deployment

Sebelum update production, lakukan backup database.

Contoh:

```bash
mysqldump \
    --single-transaction \
    --routines \
    --triggers \
    -u root \
    -p \
    ujian \
    > backup_ujian_$(date +%Y%m%d_%H%M%S).sql
```

Backup juga file aplikasi yang di-upload atau asset yang dibuat dinamis jika ada.

Contoh:

```bash
tar -czf \
    backup_ujiancat_files_$(date +%Y%m%d_%H%M%S).tar.gz \
    public/img \
    public/assets
```

Sesuaikan dengan lokasi file upload yang benar-benar digunakan pada server.

---

## 8. Install dan Build Frontend

Install dependency:

```bash
npm ci
```

Build production:

```bash
npm run build
```

Jika `package-lock.json` tersedia, `npm ci` lebih direkomendasikan dibanding `npm install` untuk deployment karena menggunakan dependency yang terkunci.

Jika `package-lock.json` belum tersedia:

```bash
npm install
npm run build
```

---

## 9. Laravel Cache

Sebelum optimasi:

```bash
php artisan optimize:clear
```

Untuk production dapat dilanjutkan dengan:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika setelah perubahan muncul perilaku tidak sesuai:

```bash
php artisan optimize:clear
```

---

## 10. Permission Linux

Web server harus dapat menulis ke:

```text
storage/
bootstrap/cache/
```

Contoh untuk user web server `www-data`:

```bash
sudo chown -R www-data:www-data \
    storage \
    bootstrap/cache
```

Kemudian:

```bash
sudo chmod -R 775 \
    storage \
    bootstrap/cache
```

Jangan memberikan permission:

```text
777
```

kecuali untuk troubleshooting sementara dan dengan alasan yang jelas.

---

## 11. Nginx Virtual Host

Contoh:

```nginx
server {
    listen 80;
    server_name ujian.example.go.id;

    root /var/www/Ujian_CAT/public;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;

        fastcgi_pass unix:/run/php/php8.3-fpm.sock;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;

        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(env|log|sql|bak|old|ini)$ {
        deny all;
    }
}
```

Sesuaikan:

```text
server_name
root
PHP-FPM socket
```

dengan server yang digunakan.

Validasi:

```bash
sudo nginx -t
```

Reload:

```bash
sudo systemctl reload nginx
```

---

## 12. HTTPS

Production harus menggunakan HTTPS.

Jika menggunakan Let's Encrypt:

```bash
sudo certbot --nginx \
    -d ujian.example.go.id
```

Setelah HTTPS aktif, `.env` harus menggunakan:

```dotenv
APP_URL=https://ujian.example.go.id
```

---

## 13. PHP-FPM

Restart PHP-FPM jika diperlukan:

```bash
sudo systemctl restart php8.3-fpm
```

Cek status:

```bash
sudo systemctl status php8.3-fpm
```

Versi service harus disesuaikan dengan PHP yang terpasang.

---

# BAGIAN B — DEPLOYMENT WINDOWS / XAMPP

## 14. Lokasi Project

Contoh:

```text
C:\xampp\htdocs\Ujian_CAT
```

atau:

```text
C:\Dev\Ujian_CAT
```

Aplikasi dapat dijalankan dengan dua pendekatan:

### Development / internal testing

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

### Production/internal server

Gunakan Apache pada XAMPP dengan DocumentRoot diarahkan ke:

```text
C:\xampp\htdocs\Ujian_CAT\public
```

Bukan ke root project Laravel.

---

## 15. Apache VirtualHost Windows

Contoh konfigurasi:

```apache
<VirtualHost *:80>

    ServerName ujian.local

    DocumentRoot "C:/xampp/htdocs/Ujian_CAT/public"

    <Directory "C:/xampp/htdocs/Ujian_CAT/public">

        AllowOverride All

        Require all granted

    </Directory>

    ErrorLog "logs/ujian-error.log"

    CustomLog "logs/ujian-access.log" common

</VirtualHost>
```

Pastikan module rewrite Apache aktif:

```text
mod_rewrite
```

Laravel membutuhkan URL rewrite agar routing bekerja dengan benar.

---

## 16. Windows Firewall

Jika aplikasi perlu diakses dari perangkat lain:

- buka port web yang digunakan,
- batasi source network bila memungkinkan,
- jangan expose MySQL langsung ke internet,
- gunakan VPN jika akses hanya untuk internal.

Jika menggunakan:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

pastikan Windows Firewall hanya mengizinkan network yang diperlukan.

Untuk deployment jangka panjang, Apache/Nginx lebih direkomendasikan dibanding development server `php artisan serve`.

---

# BAGIAN C — PROSEDUR UPDATE PRODUCTION

## 17. Deployment Update

Urutan deployment yang direkomendasikan:

```text
1. Backup database
2. Backup file upload
3. Aktifkan maintenance mode
4. Pull source code
5. Composer install
6. Build frontend
7. Clear cache
8. Jalankan regression test
9. Cache konfigurasi production
10. Matikan maintenance mode
11. Smoke test
```

Aktifkan maintenance:

```bash
php artisan down
```

Update source:

```bash
git pull origin main
```

Install dependency:

```bash
composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction
```

Build:

```bash
npm ci
npm run build
```

Clear cache:

```bash
php artisan optimize:clear
```

Testing:

```bash
php artisan test
```

Optimasi production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Aktifkan kembali:

```bash
php artisan up
```

---

## 18. Jangan Menjalankan Migration Otomatis

Pada deployment project ini jangan menambahkan:

```bash
php artisan migrate --force
```

ke script deployment otomatis.

Database merupakan database legacy existing dan perubahan schema harus melalui review tersendiri.

---

## 19. Smoke Test Setelah Deployment

Setelah deployment cek minimal:

### Authentication

```text
Login Admin
Login Guru
Login Siswa
Logout
```

### Guru

```text
Dashboard
Master Guru
Master Kelas
Master Siswa
Materi
Paket Soal
Detail Soal
Distribusi
Laporan
```

### Siswa

```text
Dashboard
Daftar Ujian
Mulai Ujian
Simpan Jawaban
Timer
Finish
Hasil
Review
Materi
Latihan
```

### Security

Pastikan:

```text
Guru tidak dapat membuka paket guru lain
Siswa tidak dapat membuka ujian kelas lain
GET logout ditolak
Paket dengan histori tidak dapat dihapus
Historical result tetap valid setelah siswa pindah kelas
```

---

## 20. Regression Test

Sebelum go-live:

```bash
php artisan test
```

Baseline saat dokumentasi dibuat:

```text
30 passed
0 failed
```

Jika ada test gagal, deployment sebaiknya dihentikan sampai penyebabnya diketahui.

---

## 21. Dependency Audit

PHP:

```bash
composer audit
```

Node.js:

```bash
npm audit
```

Jangan langsung menjalankan:

```bash
npm audit fix --force
```

karena dapat menaikkan versi dependency secara agresif dan menyebabkan breaking changes.

---

## 22. Laravel Health Check

Cek informasi aplikasi:

```bash
php artisan about
```

Cek route:

```bash
php artisan route:list
```

Cek error log Laravel:

```text
storage/logs/laravel.log
```

Linux:

```bash
tail -f storage/logs/laravel.log
```

Windows PowerShell:

```powershell
Get-Content .\storage\logs\laravel.log -Wait
```

---

## 23. Log Web Server

### Nginx

Umumnya:

```text
/var/log/nginx/access.log
/var/log/nginx/error.log
```

### Apache Linux

Umumnya:

```text
/var/log/apache2/access.log
/var/log/apache2/error.log
```

### XAMPP Windows

Umumnya:

```text
C:\xampp\apache\logs\access.log
C:\xampp\apache\logs\error.log
```

---

## 24. Production Security Checklist

Pastikan:

```text
[ ] APP_ENV=production
[ ] APP_DEBUG=false
[ ] HTTPS aktif
[ ] APP_URL menggunakan HTTPS
[ ] APP_KEY valid
[ ] .env tidak dapat diakses dari web
[ ] Database tidak diekspos ke internet
[ ] Database menggunakan user khusus aplikasi
[ ] Backup database tersedia
[ ] storage/logs tidak dapat diakses publik
[ ] Directory listing dinonaktifkan
[ ] Firewall aktif
[ ] Regression test lulus
[ ] Dependency audit dilakukan
```

---

## 25. Database Backup dan Restore

Backup:

```bash
mysqldump \
    --single-transaction \
    --routines \
    --triggers \
    -u root \
    -p \
    ujian \
    > ujian_backup.sql
```

Restore:

```bash
mysql \
    -u root \
    -p \
    ujian \
    < ujian_backup.sql
```

Selalu lakukan restore test pada environment non-production jika memungkinkan.

---

## 26. Rollback Deployment

Jika deployment bermasalah:

### Source Code

Lihat commit:

```bash
git log --oneline -10
```

Rollback ke commit yang sudah diketahui stabil:

```bash
git checkout <commit>
```

Kemudian:

```bash
composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader
```

Build frontend:

```bash
npm ci
npm run build
```

Clear cache:

```bash
php artisan optimize:clear
```

### Database

Database hanya direstore jika deployment memang melakukan perubahan data/schema yang perlu dikembalikan.

Gunakan backup yang dibuat sebelum deployment.

---

## 27. Maintenance Mode

Aktifkan:

```bash
php artisan down
```

Nonaktifkan:

```bash
php artisan up
```

Jangan meninggalkan aplikasi dalam maintenance mode setelah deployment selesai.

---

## 28. Recommended Production Flow

```text
Developer
   │
   ▼
Local Regression Test
   │
   ▼
Git Commit
   │
   ▼
Git Push
   │
   ▼
Server Backup
   │
   ▼
Maintenance Mode
   │
   ▼
Git Pull
   │
   ▼
Composer Install
   │
   ▼
Frontend Build
   │
   ▼
Regression Test
   │
   ▼
Laravel Cache
   │
   ▼
Smoke Test
   │
   ▼
Production
```

---

## 29. Deployment Command Summary

Contoh deployment Linux:

```bash
cd /var/www/Ujian_CAT

php artisan down

git pull origin main

composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

npm ci
npm run build

php artisan optimize:clear

php artisan test

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan up
```

Tidak ada perintah:

```bash
php artisan migrate
```

dalam deployment default aplikasi ini.

---

## 30. Final Verification

Sebelum deployment dinyatakan selesai:

```bash
php artisan about
php artisan route:list
php artisan test
composer audit
npm audit
```

Kemudian lakukan smoke test melalui browser dengan minimal satu akun Guru dan satu akun Siswa.

Deployment dinyatakan selesai jika:

```text
✓ Aplikasi dapat diakses
✓ Authentication berfungsi
✓ Role authorization benar
✓ Ujian dapat dijalankan
✓ Timer berjalan
✓ Hasil tersimpan
✓ Laporan dapat dibuka
✓ Tidak ada error kritis pada log
✓ Regression test lulus
```
