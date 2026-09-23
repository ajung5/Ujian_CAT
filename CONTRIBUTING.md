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

Jangan menambahkan perubahan schema otomatis terhadap production legacy tanpa review. Jangan menggunakan:

```bash
php artisan migrate:fresh
```

pada database CAT existing.

Regression test wajib menggunakan SQLite `:memory:`. Jangan mengubah fail-safe pada `tests/TestCase.php` atau `tests/Support/CreatesLegacySchema.php` tanpa review khusus.

## Authentication / Static Analysis

Controller first-party menggunakan facade Laravel secara eksplisit untuk authentication:

```php
use Illuminate\Support\Facades\Auth;

$userId = Auth::id();
```

Untuk object user yang membutuhkan type `App\Models\User`, gunakan query model yang eksplisit bila sesuai konteks:

```php
$user = User::findOrFail((int) Auth::id());
```

Tujuannya menjaga source tetap mudah dianalisis oleh IDE/static analyzer tanpa mengubah business logic.

## Formatting

Format source:

```bash
npm run format
```

Periksa tanpa menulis:

```bash
npm run format:check
```

Periksa whitespace Git:

```bash
git diff --check
```

## Regression Test

```bash
php artisan test
```

Baseline repository saat ini:

```text
47 passed
0 failed
```

## Pre-commit Hook

Aktifkan hook repository sekali pada clone lokal:

```bash
git config core.hooksPath .githooks
```

Hook saat ini menjalankan:

```text
PHP runtime validation
npm run format:check
git diff --cached --check
APP_ENV=testing php artisan test
```

Hook memvalidasi PHP `>= 8.4.1`.

## Validation Sebelum Commit

Minimal:

```bash
npm run format:check
php artisan test
git diff --check
```

Tambahan bila relevan:

```bash
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
Refactor authentication calls to use Auth facade
```

## Pull Request

Sertakan:

- tujuan perubahan;
- file utama;
- dampak business process;
- dampak database;
- security impact;
- hasil regression test;
- langkah manual test bila diperlukan.
