# Ujian CAT

Aplikasi **Computer Assisted Test (CAT)** berbasis Laravel untuk pengelolaan ujian, latihan, materi pembelajaran, peserta, kelas, hasil assessment, serta aktivitas keamanan pengguna.

Project ini merupakan hasil modernisasi aplikasi CAT legacy dari **Laravel 5.1** ke **Laravel 13**. Modernisasi dilakukan dengan tetap mempertahankan proses bisnis utama, pola UI/UX legacy yang masih digunakan, dan kompatibilitas database existing, sambil memperkuat security, automated testing, static analysis, serta maintainability source code.

## Status Aplikasi

| Komponen | Kondisi Saat Ini |
| --- | --- |
| Versi aplikasi | `2.2.1` |
| Framework | Laravel `^13.17` |
| PHP | Composer `^8.3`; pre-commit project mensyaratkan `>= 8.4.1` |
| Database | MySQL, default database `ujian` |
| Frontend | Blade, Bootstrap, jQuery, Vite |
| Import / Export | PhpSpreadsheet `^5.10` |
| Image Processing | Intervention Image `^4.0` |
| Testing | Pest / PHPUnit, SQLite `:memory:` |
| Static Analysis | Larastan / PHPStan level 6 |
| Feature test | 69 skenario feature test pada repository saat ini |
| Docker | Tidak diperlukan |
| Fresh database installer | `database/install/ujian_cat_empty.sql` |

Aplikasi dapat dijalankan langsung menggunakan:

```bash
php artisan serve
```

Default URL development:

```text
http://127.0.0.1:8000
```

---

## Fitur Utama

### Administrator

Administrator menggunakan status `A`.

Fitur utama:

- dashboard Administrator/Guru;
- profil dan identitas sekolah;
- manajemen Guru;
- manajemen Kelas;
- manajemen Siswa dan Calon Siswa;
- penerimaan Calon Siswa menjadi Siswa;
- import siswa dan calon siswa;
- manajemen Materi;
- manajemen Paket Soal;
- manajemen Detail Soal;
- upload audio soal;
- import soal;
- distribusi Ujian ke kelas;
- laporan hasil assessment;
- export hasil per kelas;
- penghapusan hasil dengan validasi histori dan ownership;
- halaman Changelog;
- halaman **Aktivitas Keamanan**;
- monitoring session siswa;
- **Paksa Logout** session siswa.

### Guru

Guru menggunakan status `G`.

Fitur utama:

- dashboard Guru;
- profil;
- manajemen Kelas sesuai flow aplikasi;
- manajemen Siswa/Calon Siswa;
- Materi;
- Paket Soal;
- Detail Soal;
- distribusi assessment;
- hasil dan laporan assessment;
- import/export yang diizinkan aplikasi.

Fitur Administrator-only seperti Security Audit Trail dan Changelog administratif dibatasi melalui middleware role.

### Siswa

Siswa menggunakan status `S`.

Fitur utama:

- dashboard;
- profil;
- daftar Ujian sesuai distribusi kelas;
- engine Ujian;
- engine Latihan;
- timer server-side;
- penyimpanan jawaban;
- finalisasi assessment;
- auto-finalize ketika waktu habis;
- hasil assessment;
- riwayat percobaan Latihan;
- review Latihan berdasarkan attempt;
- Materi pembelajaran;
- navigasi kembali ke soal yang belum dijawab;
- single active session.

### Calon Siswa

Calon Siswa menggunakan status `C`.

Calon Siswa hanya memperoleh akses ke area yang memang diizinkan, seperti dashboard/profil. Assessment belum dapat diakses sampai user diterima menjadi Siswa (`S`).

---

## Role Pengguna

| Status | Role |
| --- | --- |
| `A` | Administrator |
| `G` | Guru |
| `S` | Siswa |
| `C` | Calon Siswa |

Authorization diterapkan melalui middleware dan validasi server-side. UI bukan satu-satunya kontrol akses.

---

## Perilaku Assessment

### Ujian

- Maksimal **1 attempt** per siswa dan paket Ujian.
- Paket harus didistribusikan ke kelas siswa.
- Attempt aktif divalidasi pada server-side.
- Timer dikelola per attempt.
- Jawaban tidak dapat diubah setelah attempt final.
- Paket Ujian tidak dapat direview melalui direct URL sebagai Latihan.

### Latihan

- Maksimal **3 attempt**.
- Setiap attempt disimpan terpisah.
- Jawaban setiap attempt menggunakan `attempt_id`.
- Timer setiap attempt terisolasi.
- Nilai setiap attempt disimpan terpisah.
- Hasil utama menampilkan completed attempt terbaru.
- Riwayat Percobaan tersedia dari halaman hasil.
- Review dilakukan per attempt.
- Attempt ke-4 ditolak server-side.

### Navigasi Soal

Ketika siswa menekan selesai tetapi masih ada soal kosong:

```text
Klik Selesai
    ↓
Sistem mendeteksi soal belum dijawab
    ↓
Kembali ke Soal Belum Dijawab
    ↓
Buka soal kosong
    ↓
Jawab
    ↓
Pindah ke soal kosong berikutnya
    ↓
Kembali ke mode normal setelah semua terjawab
```

Pada perangkat mobile, area soal akan diarahkan kembali ke posisi pertanyaan ketika berpindah ke soal yang belum dijawab.

---

## Single Active Session Siswa

Akun dengan status `S` hanya dapat mempunyai **satu session aktif**.

Flow:

```text
Perangkat A login
    ↓
Random student session token dibuat
    ↓
Token asli disimpan di Laravel session
    ↓
SHA-256(token) disimpan sebagai active_session_hash

Perangkat B login dengan akun yang sama
    ↓
Token baru dibuat
    ↓
active_session_hash diganti
    ↓
Perangkat A ditolak pada request berikutnya
    ↓
Perangkat B tetap aktif
```

Ketentuan:

- login terbaru menang;
- token session asli tidak disimpan di database;
- database hanya menyimpan SHA-256 fingerprint;
- Remember Me dinonaktifkan untuk Siswa;
- remember token legacy siswa dibersihkan pada login;
- session lama tidak dapat menghapus fingerprint session terbaru;
- Administrator dapat melakukan Paksa Logout;
- force logout menggunakan `student_session_revoked_at` sehingga session lama tidak dapat mengklaim ulang session aktif.

---

## Security Audit Trail

Aplikasi mempunyai tabel `user_security_events`.

Event yang dicatat:

```text
LOGIN_SUCCESS
LOGIN_FAILED
LOGOUT
SESSION_REPLACED
SESSION_INVALIDATED
SESSION_FORCE_REQUESTED
SESSION_LEGACY_CLAIMED
```

Informasi yang dapat dicatat sesuai event:

- user ID;
- actor user ID;
- snapshot email;
- role;
- IP address;
- user-agent;
- SHA-256 session fingerprint;
- metadata event;
- waktu event.

Password tidak dicatat pada audit trail.

Administrator dapat membuka:

```text
/admin/security-events
```

Fitur halaman tersebut meliputi:

- filter event;
- filter role;
- pencarian nama/email/IP;
- pagination;
- ringkasan aktivitas keamanan 24 jam;
- monitoring session siswa;
- last login;
- last login IP;
- user-agent;
- Paksa Logout siswa.

---

## Security Controls

Kontrol yang sudah tersedia antara lain:

- Laravel authentication;
- role-based authorization;
- CSRF protection;
- POST-only logout;
- login rate limiting;
- ownership validation;
- IDOR protection;
- cross-package assessment protection;
- server-side timer;
- transaction untuk operasi database kritis;
- upload validation;
- single active session khusus Siswa;
- server-side session revocation;
- security audit trail;
- admin force logout siswa;
- last-login tracking;
- regression test untuk critical flow.

Jangan pernah commit secret seperti:

```text
.env
APP_KEY
database password
API token
private key
production credential
```

Lihat [`SECURITY.md`](SECURITY.md).

---

# Instalasi Baru

Untuk instalasi baru, gunakan **fresh database installer** yang tersedia di repository.

## 1. Requirements

Pastikan tersedia:

```text
PHP
Composer 2.x
MySQL
Node.js
npm
Git
```

Constraint project:

```text
PHP ^8.3
```

Untuk menggunakan pre-commit hook repository tanpa perbedaan runtime:

```text
PHP >= 8.4.1
```

## 2. Clone dan Dependency

```bash
git clone https://github.com/ajung5/Ujian_CAT.git
cd Ujian_CAT
composer install
cp .env.example .env
php artisan key:generate
```

Contoh database pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujian
DB_USERNAME=root
DB_PASSWORD=
```

Default session development:

```dotenv
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

## 3. Buat Database Kosong

```sql
CREATE DATABASE ujian
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

## 4. Import Fresh Database

Gunakan:

```text
database/install/ujian_cat_empty.sql
```

Import:

```bash
mysql -u root -p ujian < database/install/ujian_cat_empty.sql
```

Installer tersebut:

- ditujukan untuk database kosong;
- tidak menggunakan `DROP TABLE`;
- membuat struktur aplikasi saat ini;
- mencakup multi-attempt;
- mencakup single active student session;
- mencakup security audit trail;
- tidak membuat data Guru/Siswa/Kelas/Materi/Soal/Jawaban dummy;
- hanya membuat satu Administrator awal;
- mengisi tabel `migrations` untuk schema yang sudah direpresentasikan installer.

Administrator awal:

```text
Email : admin@ujian-cat.local
Role  : A
```

Password awal sengaja tidak didistribusikan.

Set password sendiri:

```bash
php artisan tinker
```

Kemudian:

```php
$admin = \App\Models\User::where(
    'email',
    'admin@ujian-cat.local'
)->firstOrFail();

$admin->password =
    \Illuminate\Support\Facades\Hash::make(
        'GANTI-DENGAN-PASSWORD-KUAT'
    );

$admin->save();
```

Opsional:

```php
$admin->email = 'admin@example.go.id';
$admin->save();
```

## 5. Frontend dan Validasi

```bash
npm install
npm run build
php artisan optimize:clear
php artisan migrate:status
php artisan test
php artisan serve
```

Akses:

```text
http://127.0.0.1:8000
```

Dokumentasi khusus installer tersedia pada [`database/install/README.md`](database/install/README.md).

---

# Menggunakan Database Legacy Existing

Aplikasi tetap mendukung database CAT legacy existing.

Sebelum perubahan schema:

1. backup database;
2. gunakan environment non-production terlebih dahulu;
3. cek migration yang pending;
4. review perubahan schema;
5. baru jalankan migration yang dibutuhkan.

Cek:

```bash
php artisan migrate:status
```

Migration modernisasi saat ini mencakup antara lain:

- `assessment_attempts`;
- `attempt_id` pada jawaban dan timer;
- unique index multi-attempt;
- normalisasi timestamp legacy;
- `active_session_hash`;
- session revocation dan last-login fields;
- `user_security_events`.

Setelah backup dan review:

```bash
php artisan migrate
```

**Jangan** menggunakan:

```bash
php artisan migrate:fresh
```

pada database existing karena perintah tersebut destruktif.

Migration production tidak disarankan dijalankan otomatis tanpa review.

---

## Tabel Database Utama

Schema aplikasi saat ini menggunakan antara lain:

```text
schools
kelas
users
materis
soals
detailsoals
distribusisoals
assessment_attempts
jawabs
countexamtimes
aktifitas
user_security_events
migrations
```

Beberapa kolom tetap mempertahankan penamaan dan tipe legacy untuk compatibility.

---

## Testing

Testing menggunakan Pest/PHPUnit dengan SQLite `:memory:`.

Feature suite repository saat ini berisi **69 skenario test**:

```text
AdminChangelogTest
AuthenticationAndRoleTest
CandidateAcceptanceTest
CandidateAccessRestrictionTest
SecurityAuditTrailTest
StudentAssessmentAccessTest
StudentAssessmentLifecycleTest
StudentDataTabsTest
TeacherAssessmentManagementTest
```

Jalankan:

```bash
php artisan test
```

Regression mencakup authentication, role, candidate workflow, single-session, security audit, Ujian/Latihan, multi-attempt, timer, review, ownership, IDOR, dan historical result.

---

## Static Analysis dan Formatting

Static analysis:

```bash
composer analyse
```

PHPStan/Larastan saat ini menggunakan level 6.

Format source:

```bash
npm run format
```

Validasi:

```bash
npm run format:check
git diff --check
```

---

## Pre-commit Hook

Aktifkan:

```bash
git config core.hooksPath .githooks
```

Hook menjalankan:

```text
PHP runtime validation
npm run format:check
composer analyse
git diff --cached --check
APP_ENV=testing php artisan test
```

Pre-commit saat ini mensyaratkan PHP `>= 8.4.1`.

---

## Quality Gate Sebelum Commit

```bash
php artisan test
composer analyse
npm run format:check
git diff --check
```

Audit dependency:

```bash
composer audit
npm audit
```

Hindari `npm audit fix --force` tanpa review.

---

## Struktur Project

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   └── Auth/
│   └── Middleware/
├── Models/
└── Services/

database/
├── install/
│   ├── README.md
│   └── ujian_cat_empty.sql
└── migrations/

resources/
└── views/
    ├── admin/
    ├── auth/
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
```

---

## Dokumentasi

- [`database/install/README.md`](database/install/README.md) — fresh database installer.
- [`docs/INSTALLATION.md`](docs/INSTALLATION.md) — instalasi development.
- [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) — deployment.
- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) — arsitektur.
- [`docs/DATABASE.md`](docs/DATABASE.md) — database.
- [`docs/TESTING.md`](docs/TESTING.md) — testing.
- [`docs/CODE-STYLE.md`](docs/CODE-STYLE.md) — code style.
- [`docs/OPERATIONS.md`](docs/OPERATIONS.md) — operasi.
- [`docs/MIGRATION-NOTES.md`](docs/MIGRATION-NOTES.md) — modernisasi.
- [`SECURITY.md`](SECURITY.md) — security policy.
- [`CONTRIBUTING.md`](CONTRIBUTING.md) — kontribusi.
- [`CHANGELOG.md`](CHANGELOG.md) — histori perubahan.

---

## Catatan Modernisasi

```text
Business Process  → dipertahankan
UI / UX utama     → dipertahankan
Database legacy   → compatibility dijaga
Framework         → Laravel 13
Security          → diperkuat
Testing           → diperluas
Static Analysis   → diterapkan
Code Quality      → distandardisasi
```

Komponen yang telah dimodernisasi antara lain authentication, authorization, logout POST + CSRF, PhpSpreadsheet, assessment lifecycle, multi-attempt, server-side timer, candidate workflow, single student session, security audit trail, serta automated regression testing.

---

## Production Notes

Minimal:

```dotenv
APP_ENV=production
APP_DEBUG=false
```

Gunakan HTTPS dan pastikan:

- `.env` tidak dapat diakses publik;
- MySQL tidak diekspos langsung ke internet;
- backup dilakukan sebelum upgrade;
- `storage/` dan `bootstrap/cache/` writable;
- DocumentRoot mengarah ke `public/`;
- migration production tidak otomatis;
- regression test dan smoke test dilakukan sebelum go-live.

---

## Repository

```text
https://github.com/ajung5/Ujian_CAT
```

Versi aplikasi pada `config/app.php` saat ini:

```text
2.2.1
```
