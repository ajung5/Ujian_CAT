# Changelog

## Unreleased

### Added

- Laravel 13 modernization.
- Role-based route protection.
- Student assessment engine.
- Server-side assessment timer.
- Student result/review.
- Teacher result reporting.
- PhpSpreadsheet import/export.
- Regression test suite.
- Documentation set.
- Candidate acceptance workflow dari status `C` menjadi `S` pada record user yang sama.
- Candidate access restriction untuk mencegah Calon Siswa mengakses assessment Siswa.
- Pemisahan tab Data Siswa dan Calon Siswa.
- Pre-commit hook untuk PHP runtime, format check, staged diff check, dan regression test.
- Test database fail-safe untuk memastikan schema regression hanya dibuat pada SQLite `:memory:`.

### Changed

- Authentication flow dimodernisasi.
- Pemanggilan authentication pada controller distandardisasi menggunakan Laravel `Auth` facade agar lebih eksplisit dan ramah static analysis.
- Raw MySQL access diganti Eloquent/Query Builder.
- GET logout diganti POST + CSRF.
- PHPExcel diganti PhpSpreadsheet.
- Delete Paket Soal diperkuat dengan transaction dan historical protection.
- Historical class result menggunakan `jawabs.id_kelas`.
- Dependency frontend legacy yang tidak digunakan dibersihkan.
- Paket Soal menampilkan Jenis Soal (Ujian / Latihan) pada kolom terpisah.
- Source formatting distandardisasi menggunakan Prettier, plugin PHP/Blade, dan Blade whitespace cleanup.
- Test bootstrap diperkuat agar `APP_ENV` shell/VS Code tidak mengalihkan regression test ke database development.

### Removed

- Raw `mysqli` views.
- Obsolete legacy views.
- `jquery-toggles`.

### Security

- Ownership/IDOR validation.
- Login rate limiting.
- Cross-package protection.
- Candidate assessment access restriction.
- Critical-flow regression tests.
- SQLite in-memory safety guard untuk regression schema.

### Testing

Baseline regression suite saat ini:

```text
47 passed
0 failed
```

Suite mencakup authentication/role, candidate acceptance, candidate restriction, student assessment access, assessment lifecycle, student/candidate tabs, dan teacher assessment management.
