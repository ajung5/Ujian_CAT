# Contributing — Ujian CAT

## Prinsip

Perubahan harus mempertahankan proses bisnis existing, kompatibilitas database legacy, UI/UX utama, security controls, dan regression test.

## Branch

```bash
git checkout -b feature/nama-perubahan
```

atau:

```bash
git checkout -b fix/nama-bug
```

## Sebelum Coding

Review route, controller, model, Blade, schema legacy, dan test terkait.

## Database

Jangan menambahkan perubahan schema otomatis terhadap production legacy tanpa review. Jangan gunakan `php artisan migrate:fresh` pada database existing.

## Validation

```bash
php artisan optimize:clear
php artisan test
./vendor/bin/pint
composer validate
composer audit
npm audit
```

## Commit

Contoh:

```text
Fix historical class result authorization
Add regression tests for assessment lifecycle
Optimize legacy frontend dependencies
```

## Pull Request

Sertakan tujuan perubahan, file utama, dampak business process, dampak database, security impact, hasil test, dan langkah manual test.
