# Changelog

Seluruh perubahan penting pada aplikasi Ujian CAT dicatat pada dokumen ini.

Format versi mulai modernisasi mengikuti Semantic Versioning:

`MAJOR.MINOR.PATCH`

> Catatan: riwayat versi awal v2.x direkonstruksi berdasarkan milestone
> modernisasi dan histori Git. Aplikasi legacy sebelumnya menggunakan
> keterangan versi 1.1 dan belum menggunakan release tagging secara
> konsisten.

---

## Unreleased

Belum ada perubahan yang dijadwalkan untuk release berikutnya.

---

## v2.2.1 - 25 September 2026

### Added

- Menambahkan pembatasan satu session aktif untuk setiap akun Siswa.
- Menambahkan `active_session_hash` pada tabel `users` untuk menyimpan fingerprint session aktif siswa.
- Menambahkan middleware `EnsureSingleStudentSession` untuk memvalidasi session siswa pada setiap akses ke area siswa.

### Changed

- Login terbaru siswa sekarang menjadi session aktif yang sah.
- Session siswa dari perangkat sebelumnya akan dihentikan pada request berikutnya setelah akun yang sama login dari perangkat lain.
- Remember Me dinonaktifkan khusus untuk akun Siswa.
- Token Remember Me legacy milik siswa dibersihkan saat login.
- Session siswa menggunakan random token terpisah dari Laravel Session ID.
- Database hanya menyimpan SHA-256 hash dari token session siswa, bukan token aslinya.
- Memindahkan akses Review Latihan dari kolom Aksi utama ke Riwayat Percobaan.
- Menambahkan tombol Riwayat Percobaan pada tabel hasil Latihan.
- Riwayat setiap attempt sekarang ditampilkan melalui modal agar tabel hasil tetap ringkas.
- Tombol Review tersedia pada masing-masing attempt di dalam Riwayat Percobaan.
- Status batas maksimal 3 percobaan ditampilkan sebagai status, bukan tombol disabled.
- Teks tombol konfirmasi pengerjaan diubah menjadi **Kembali ke Soal Belum Dijawab** agar lebih mudah dipahami.
- Tampilan aksi pada halaman hasil siswa dirapikan agar tombol lebih konsisten dan mudah digunakan.
- Bagian **Histori Percobaan** pada hasil Latihan sekarang ditampilkan sebagai kontrol yang lebih menyerupai tombol.
- Tombol **Lihat** pada Histori Percobaan diperbesar agar lebih jelas dan konsisten dengan tombol aksi lainnya.

### Fixed

- Memperbaiki navigasi ketika siswa kembali ke soal yang belum dijawab.
- Setelah menjawab satu soal yang sebelumnya kosong, sistem langsung membuka soal kosong berikutnya.
- Navigasi tidak lagi berpindah ke nomor soal yang sudah dijawab ketika berada dalam mode penyelesaian soal belum dijawab.
- Setelah seluruh soal kosong selesai dijawab, navigasi kembali ke mode normal.
- Memperbaiki bug modal Riwayat Percobaan yang tidak dapat diklik karena konflik stacking context antara layout AdminPlus dan Bootstrap backdrop.
- Modal Riwayat Percobaan sekarang dipindahkan ke level `<body>` saat dibuka agar tombol Review, Tutup, dan kontrol modal berfungsi normal.
- Memperbaiki kompatibilitas tabel `users` legacy dengan MySQL modern dengan menormalisasi `created_at` dan `updated_at` dari zero-date default menjadi nullable timestamp.
- Memperbaiki navigasi soal ketika siswa memilih kembali ke soal yang belum dijawab dari dialog penyelesaian assessment.
- Setelah siswa menjawab salah satu soal yang sebelumnya kosong, sistem sekarang langsung membuka soal berikutnya yang masih belum dijawab.
- Navigasi tidak lagi kembali ke urutan soal normal ketika siswa sedang menyelesaikan daftar soal yang belum dijawab.
- Setelah seluruh soal yang sebelumnya kosong selesai dijawab, sistem otomatis kembali ke mode navigasi normal.

### Security

- Satu akun Siswa hanya dapat mempunyai satu session aktif.
- Login siswa terbaru membatalkan validitas session siswa sebelumnya tanpa menghapus session aktif terbaru.
- Session token asli tidak disimpan pada database.
- Fingerprint session siswa disimpan menggunakan SHA-256.
- Session fixation tetap dicegah melalui regenerasi session setelah autentikasi.
- Session lama tidak dapat menghapus `active_session_hash` milik login siswa terbaru.
- Persistent login melalui Remember Me tidak digunakan untuk akun Siswa.

### UX

- Mengurangi duplikasi aksi Review pada halaman hasil.
- Memisahkan fungsi Aksi utama dan Riwayat Percobaan agar lebih mudah dipahami.
- Meningkatkan keterbacaan tombol dan kontrol histori percobaan.

Alur penyelesaian soal yang belum dijawab:

```text
Klik Selesai
    ↓
Sistem mendeteksi soal yang belum dijawab
    ↓
Kembali ke Soal Belum Dijawab
    ↓
Buka soal kosong pertama
    ↓
Jawab soal
    ↓
Langsung ke soal kosong berikutnya
    ↓
Sampai seluruh soal terjawab
```

### Testing

Regression test diperluas untuk mencakup:

- Login siswa menghasilkan active session fingerprint.
- Login siswa terbaru mengganti session aktif sebelumnya.
- Session siswa lama ditolak setelah login dari session/perangkat lain.
- Remember Me tidak dipertahankan untuk akun Siswa.
- Token Remember Me legacy siswa dibersihkan saat login.
- Compatibility session pada seluruh workflow Ujian dan Latihan.
- Regression workflow multi-attempt, review, dan timer setelah penerapan single-session.

## v2.2.0 - 24 September 2026

### Added

- Sistem multi-attempt untuk Latihan.
- Maksimal 3 kali percobaan Latihan per siswa.
- Tabel `assessment_attempts` untuk menyimpan setiap sesi pengerjaan.
- Histori Percobaan pada halaman hasil siswa.
- Review jawaban berdasarkan attempt.
- Penyimpanan nilai per attempt.
- Timer assessment per attempt.
- Status `in_progress` dan `finished` pada setiap attempt.
- Tombol Ulangi pada hasil Latihan.
- Informasi nomor percobaan pada proses pengerjaan dan hasil.

### Changed

- Ujian tetap dibatasi maksimal 1 attempt.
- Latihan dapat dilakukan maksimal 3 attempt.
- Hasil utama Latihan menampilkan attempt terakhir yang selesai.
- Penyimpanan jawaban sekarang terisolasi menggunakan `attempt_id`.
- Penyimpanan timer sekarang terisolasi menggunakan `attempt_id`.
- Review jawaban hanya tersedia untuk paket Latihan.
- Layout tombol aksi hasil siswa dirapikan.
- Query assessment disesuaikan untuk mendukung histori attempt.

### Fixed

- Memperbaiki duplicate constraint pada `countexamtimes`.
- Mengganti unique constraint legacy:

    `id_soal + id_user`

    menjadi:

    `attempt_id`

- Memperbaiki duplicate constraint pada `jawabs`.
- Mengganti unique constraint legacy:

    `no_soal_id + id_soal + id_user`

    menjadi:

    `attempt_id + no_soal_id`

- Memperbaiki kompatibilitas timer dan jawaban untuk Attempt #2 dan #3.
- Memperbaiki layout tombol Review dan Ulangi pada halaman hasil siswa.

### Security

- Attempt aktif divalidasi pada server-side.
- Jawaban tidak dapat ditulis ke attempt milik assessment lain.
- Attempt yang telah selesai tidak dapat dimodifikasi kembali.
- Direct access ke review Ujian dibatasi.
- Ownership hasil dan attempt tetap divalidasi berdasarkan user login.

### Testing

Regression test diperluas untuk mencakup:

- Attempt pertama Latihan.
- Attempt kedua Latihan.
- Attempt ketiga Latihan.
- Penolakan Attempt ke-4.
- Isolasi jawaban antar attempt.
- Isolasi timer antar attempt.
- Review attempt tertentu.
- Histori percobaan.
- Latest completed attempt.
- Proteksi assessment yang sudah final.

---

## v2.1.0 - 22 September 2026

### Added

- Role Calon Siswa dengan status `C`.
- Workflow penerimaan Calon Siswa menjadi Siswa.
- Pemisahan tab Data Siswa dan Calon Siswa.
- Import Calon Siswa.
- Penetapan NIS dan kelas saat penerimaan.
- Pre-commit quality gate.
- SQLite `:memory:` untuk regression testing.
- Dokumentasi teknis project.
- Static analysis menggunakan Larastan.
- Database index untuk optimasi query assessment.

### Changed

- Calon Siswa menggunakan record user yang sama ketika diterima.
- Authorization assessment diperketat berdasarkan role.
- Query assessment legacy dioptimalkan.
- Source formatting distandardisasi.
- Authentication controller menggunakan Laravel facade secara konsisten.
- Struktur testing diperkuat agar tidak menyentuh database development.

### Security

- Calon Siswa tidak dapat membuka assessment sebelum diterima.
- Regression test dipisahkan dari database development.
- Resource ownership diperketat.
- Proteksi IDOR diperluas.
- Historical assessment diberi proteksi tambahan.

### Fixed

- Isolasi test environment.
- Query historical assessment.
- Sejumlah compatibility issue dari database legacy.

---

## v2.0.0 - 21 September 2026

### Major Modernization

Versi ini merupakan modernisasi besar aplikasi CAT legacy.

### Added

- Laravel 13.
- PHP runtime modern.
- Authentication Laravel modern.
- Middleware role.
- Regression testing.
- Modern assessment controller.
- Server-side assessment timer.
- Import dan export menggunakan PhpSpreadsheet.
- Modern image processing.
- Dokumentasi instalasi.
- Dokumentasi deployment.
- Dokumentasi arsitektur.
- Dokumentasi database.
- Dokumentasi testing.
- Security policy.

### Changed

- Framework dimigrasikan dari Laravel 5.1 ke Laravel 13.
- Raw database access dimigrasikan ke Eloquent dan Query Builder.
- Authentication legacy dimodernisasi.
- Logout GET diganti menjadi POST + CSRF.
- PHPExcel diganti menjadi PhpSpreadsheet.
- Dependency lama yang tidak diperlukan dibersihkan.
- Controller dan route dimodernisasi.
- Business process utama tetap dipertahankan.
- Database legacy tetap dipertahankan.
- UI/UX utama tetap dipertahankan.

### Security

- Middleware authentication.
- Role-based authorization.
- CSRF protection.
- Login rate limiting.
- Ownership validation.
- IDOR protection.
- Cross-package assessment protection.
- Server-side timer enforcement.
- Finalisasi assessment pada server-side.

---

## v1.1 - Legacy Baseline

### Legacy Application

Versi ini merupakan baseline aplikasi CAT sebelum proses modernisasi.

Teknologi utama:

- Laravel 5.1.
- PHP legacy.
- Blade.
- Bootstrap.
- jQuery.
- MySQL.
- PHPExcel.
- Komponen frontend legacy.

### Fitur Dasar

- Login Administrator.
- Login Guru.
- Login Siswa.
- Dashboard.
- Manajemen Guru.
- Manajemen Siswa.
- Manajemen Kelas.
- Manajemen Materi.
- Manajemen Paket Soal.
- Manajemen Detail Soal.
- Distribusi soal ke kelas.
- Ujian siswa.
- Latihan siswa.
- Timer ujian.
- Penyimpanan jawaban.
- Hasil assessment.
- Laporan Guru.
- Import data.
- Export hasil.

### Legacy Technical Characteristics

- Framework Laravel generasi lama.
- Sebagian akses database masih menggunakan pola legacy.
- PHPExcel digunakan untuk pengolahan spreadsheet.
- GET logout masih digunakan.
- Dependency frontend lama masih tersedia.
- Database dirancang dengan asumsi satu assessment per user dan paket soal.
- Belum memiliki arsitektur multi-attempt.
- Belum memiliki regression test modern yang melindungi critical flow.

### Modernization Objective

Modernisasi dilakukan dengan prinsip:

- mempertahankan proses bisnis;
- mempertahankan data existing;
- mempertahankan struktur database yang masih dibutuhkan;
- mempertahankan UI/UX utama;
- meningkatkan security;
- meningkatkan maintainability;
- meningkatkan automated testing;
- meningkatkan code quality.

```

```

```

```
