# Architecture — Ujian CAT

## Overview

Ujian CAT menggunakan arsitektur monolithic Laravel.

```text
Browser
  ↓
Routes
  ↓
Middleware (auth + role)
  ↓
Controllers
  ├─ Eloquent / Query Builder → MySQL
  └─ Blade Views → Browser
```

## Komponen Utama

- `routes/web.php` — routing aplikasi.
- `app/Http/Controllers/` — business flow dan request handling.
- `app/Models/` — representasi tabel legacy.
- `resources/views/guru/` — UI Guru/Admin.
- `resources/views/siswa/` — UI Siswa.
- `resources/views/layouts/` — layout utama.
- `tests/Feature/` — regression test.
- `tests/Support/CreatesLegacySchema.php` — schema SQLite untuk test terisolasi.

## Role dan Authorization

```text
A = Administrator
G = Guru
S = Siswa
C = Calon Siswa
```

Guru hanya dapat mengelola paket miliknya, sedangkan Administrator dapat mengakses seluruh paket. Siswa hanya dapat membuka ujian yang didistribusikan ke kelasnya dan latihan yang terhubung dengan materi aktif.

## Assessment Lifecycle

```text
Distribusi Paket
  ↓
Siswa membuka assessment
  ↓
Question order dibuat di session
  ↓
Start assessment
  ↓
Countexamtime dibuat
  ↓
Jawaban disimpan status=N
  ↓
Finish / timer habis
  ↓
Semua soal aktif difinalisasi status=Y
  ↓
Hasil dan laporan
```

Timer bersifat server-authoritative. Kunci logis timer adalah `id_soal + id_user`.

## Historical Result

`jawabs.id_kelas` adalah snapshot kelas saat assessment berlangsung. Nilai ini digunakan untuk histori dan laporan, bukan hanya `users.id_kelas` yang merepresentasikan kelas siswa saat ini.

## Integritas Delete Paket

```text
Belum pernah dikerjakan
→ detailsoals dibersihkan
→ distribusisoals dibersihkan
→ file audio terkait dibersihkan
→ soals dihapus

Sudah pernah dikerjakan / sudah memiliki timer
→ hard-delete ditolak
→ histori dipertahankan
```

## Import / Export

Import dan export Excel menggunakan PhpSpreadsheet. PHPExcel legacy tidak digunakan lagi.
