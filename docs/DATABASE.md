# Database — Ujian CAT

## Overview

Database utama adalah MySQL legacy bernama `ujian`. Schema dipertahankan untuk kompatibilitas data dan business process aplikasi lama.

## Tabel Inti

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

## users

Kolom penting: `id`, `id_kelas`, `nama`, `no_induk`, `jk`, `status`, `gambar`, `email`, `password`, `sekolah_asal`.

Role: `A` Administrator, `G` Guru, `S` Siswa, `C` Calon Siswa.

## soals

Kolom penting: `id`, `id_user`, `jenis`, `materi`, `paket`, `deskripsi`, `kkm`, `waktu`, `tampil`.

Jenis: `1` Ujian, `2` Latihan. `waktu` disimpan sebagai nilai legacy dalam detik.

## detailsoals

Kolom penting: `id`, `id_soal`, `jenis`, `soal`, `audio`, `pila`–`pile`, `kunci`, `score`, `id_user`, `status`, `sesi`.

`detailsoals.sesi` adalah `VARCHAR(32)`, sehingga gunakan token 32 karakter dan bukan UUID 36 karakter.

## jawabs

Kolom penting: `no_soal_id`, `id_soal`, `id_user`, `id_kelas`, `nama`, `pilihan`, `score`, `status`.

Status: `N` draft, `Y` final. `id_kelas` dan `nama` menyimpan snapshot historis saat assessment.

## countexamtimes

Kolom: `id_soal`, `id_user`, `waktu`. Kunci logis: `id_soal + id_user`.

## Migration Policy

Jangan menjalankan `php artisan migrate` atau `php artisan migrate:fresh` terhadap database production legacy tanpa review.

## Backup

```bash
mysqldump --single-transaction --routines --triggers -u root -p ujian > ujian_backup.sql
```

## Index Audit

Sebelum menambah index:

```sql
SHOW INDEX FROM jawabs;
SHOW INDEX FROM detailsoals;
SHOW INDEX FROM distribusisoals;
SHOW INDEX FROM countexamtimes;
SHOW INDEX FROM soals;
```

Hot-path yang perlu dianalisis dengan `EXPLAIN`:

```text
jawabs          (id_soal, id_user, id_kelas, status)
countexamtimes  (id_soal, id_user)
detailsoals     (id_soal, status)
distribusisoals (id_soal, id_kelas)
soals           (id_user, jenis, materi)
```
