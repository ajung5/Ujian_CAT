# Ujian CAT - Fresh Database Installer

File `ujian_cat_empty.sql` menyediakan struktur database untuk instalasi baru aplikasi Ujian CAT, tanpa data operasional/sample. Satu-satunya data aplikasi yang dibuat adalah akun Administrator.

## Karakteristik

- Tidak menjalankan `DROP TABLE`.
- Ditujukan hanya untuk database kosong.
- Struktur sudah mencakup multi-attempt, single active student session, dan security audit trail.
- Tabel `migrations` sudah diisi untuk migration yang hasil akhirnya sudah terdapat di SQL ini.
- Tidak membuat akun siswa, guru, kelas, materi, soal, jawaban, hasil, atau log dummy.
- Akun Administrator dibuat dengan password acak yang sengaja tidak didistribusikan.

## Import

Buat database kosong terlebih dahulu, misalnya:

```sql
CREATE DATABASE ujian
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

Lalu import:

```bash
mysql -u root -p ujian < database/install/ujian_cat_empty.sql
```

Sesuaikan `.env` aplikasi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujian
DB_USERNAME=root
DB_PASSWORD=
```

Setelah itu:

```bash
php artisan optimize:clear
php artisan migrate:status
```

`migrate:status` harus menunjukkan migration yang sudah direpresentasikan oleh installer sebagai **Ran**.

## Aktivasi Administrator

Administrator awal:

```text
Email : admin@ujian-cat.local
Role  : A
```

Password awal sengaja tidak diketahui. Set password Anda sendiri setelah import:

```bash
php artisan tinker
```

Di Tinker:

```php
$admin = \App\Models\User::where('email', 'admin@ujian-cat.local')->firstOrFail();
$admin->password = \Illuminate\Support\Facades\Hash::make('GANTI-DENGAN-PASSWORD-KUAT');
$admin->save();
```

Opsional, langsung ganti email Administrator:

```php
$admin->email = 'admin@example.go.id';
$admin->save();
```

Keluar dari Tinker:

```php
exit
```

Kemudian jalankan aplikasi:

```bash
php artisan serve
```

## Validasi

Setelah import, cek bahwa hanya satu data user yang tersedia:

```sql
SELECT id, nama, email, status
FROM users;
```

Expected:

```text
1 | Administrator | admin@ujian-cat.local | A
```

Tabel data bisnis lain seharusnya kosong.

## Git

File installer ini aman untuk disimpan di repository karena tidak berisi dump data produksi, password plaintext, session token, maupun data siswa/guru.

Jika `.gitignore` Anda sudah memiliki aturan `*.sql`, tambahkan exception:

```gitignore
*.sql
!database/install/
!database/install/*.sql
```

Lalu:

```bash
git add database/install .gitignore
git diff --cached --check
```
