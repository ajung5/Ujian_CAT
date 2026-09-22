# Migration Notes — Laravel 5.1 ke Laravel 13

## Prinsip Modernisasi

```text
Business Process  → dipertahankan
Database          → dipertahankan
UI / UX           → dipertahankan
Framework         → diperbarui
Security          → diperkuat
Testing           → ditambahkan
```

## Perubahan Utama

- Authentication legacy dimodernisasi.
- Raw `mysqli` diganti Eloquent/Query Builder.
- LaravelCollective HTML legacy dihilangkan.
- PHPExcel diganti PhpSpreadsheet.
- GET logout diganti POST + CSRF.
- View obsolete dihapus.
- Dependency JS/CSS tidak terpakai dibersihkan.
- `jquery-toggles` dihapus.
- Server-side timer dan assessment lifecycle diperkuat.
- Ownership/IDOR validation ditambahkan.
- Regression suite ditambahkan.

## Historical Class Fix

Histori kelas menggunakan `jawabs.id_kelas`, bukan `users.id_kelas` saat ini.

## Delete Paket Soal

Paket yang belum pernah digunakan dapat dihapus bersama dependensi. Paket yang sudah memiliki histori jawaban atau timer tidak boleh di-hard-delete.

## Legacy Constraints

Beberapa kolom tetap menggunakan tipe legacy, misalnya:

```text
detailsoals.sesi VARCHAR(32)
soals.waktu VARCHAR(25)
soals.kkm VARCHAR(5)
```

Perubahan schema baru harus melalui review dan pengujian.

## Baseline Regression

```text
30 passed
0 failed
```
