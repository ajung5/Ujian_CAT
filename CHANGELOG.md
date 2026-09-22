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

### Changed

- Authentication flow dimodernisasi.
- Raw MySQL access diganti Eloquent/Query Builder.
- GET logout diganti POST + CSRF.
- PHPExcel diganti PhpSpreadsheet.
- Delete Paket Soal diperkuat dengan transaction dan historical protection.
- Historical class result menggunakan `jawabs.id_kelas`.
- Dependency frontend legacy yang tidak digunakan dibersihkan.

### Removed

- Raw `mysqli` views.
- Obsolete legacy views.
- `jquery-toggles`.

### Security

- Ownership/IDOR validation.
- Login rate limiting.
- Cross-package protection.
- Critical-flow regression tests.

### Testing

```text
30 passed
0 failed
```
