# Code Style — Ujian_CAT

## Tujuan

Source code first-party dibuat compact, konsisten, mudah dibaca, ramah IDE/static analyzer, dan tidak mengubah business logic aplikasi.

## Indentasi

- 4 spaces per indentation level.
- Tidak menggunakan hard tab.
- LF line endings.
- Maksimum target line length: 120 karakter.
- Trailing whitespace dihapus.
- Multiple empty lines diminimalkan oleh formatter.

## PHP

Gunakan 1TBS / same-line opening brace.

```php
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExampleController extends Controller {
    public function show(int $id): View {
        $user = User::findOrFail((int) Auth::id());

        $siswa = User::query()
            ->whereKey($id)
            ->firstOrFail();

        return view('guru.detail', compact('user', 'siswa'));
    }
}
```

Control structure:

```php
if ($siswa->status === 'C') {
    return redirect()->route('siswa.index');
}
```

Jangan menulis opening brace tanpa spasi:

```php
// Hindari
public function show(int $id): View{
}
```

Gunakan:

```php
public function show(int $id): View {
}
```

## Laravel Facades dan Static Analysis

Untuk authentication pada controller, project menggunakan facade `Auth` secara eksplisit:

```php
use Illuminate\Support\Facades\Auth;

$userId = Auth::id();
```

Untuk object user yang membutuhkan type model eksplisit:

```php
$user = User::findOrFail((int) Auth::id());
```

Untuk database expression gunakan facade yang di-import:

```php
use Illuminate\Support\Facades\DB;

DB::raw('COUNT(*) as total');
```

Hindari pola global yang tidak di-import seperti:

```php
\DB::raw('COUNT(*) as total');
```

Tujuannya menjaga source konsisten dan mengurangi false-positive diagnostic dari IDE/static analyzer seperti Intelephense.

## Blade / HTML

- 4 spaces.
- Attribute pendek tetap satu baris bila masih terbaca.
- Attribute panjang otomatis di-wrap.
- Directive Blade mengikuti hierarki HTML.
- Hindari blank line berlebihan.

```blade
@if ($siswa->status === 'C')
    <div class="alert alert-warning">
        Status Anda masih Calon Siswa.
    </div>
@endif
```

## Scope Formatter

Formatter ditujukan untuk first-party source:

- `app/**/*.php`
- `bootstrap/*.php`
- `config/**/*.php`
- `database/**/*.php`
- `routes/**/*.php`
- `tests/**/*.php`
- `resources/views/**/*.blade.php`
- `resources/js/**/*.{js,ts,jsx,tsx}`
- `resources/css/**/*.{css,scss}`

`public/` sengaja di-ignore karena berisi asset legacy, third-party, dan file minified.

## Commands

Format seluruh source:

```bash
npm run format
```

Periksa tanpa menulis:

```bash
npm run format:check
```

Regression test:

```bash
php artisan test
```

Git whitespace check:

```bash
git diff --check
```

## Blade Whitespace Cleanup

Selain Prettier, project menggunakan:

```text
scripts/cleanup_blade_whitespace.py
```

untuk menghapus blank line berlebih tepat setelah opening HTML container dan tepat sebelum closing HTML container.

Contoh target:

```blade
<div class="col-sm-10">
    <input type="text" class="form-control" id="score">
</div>
```

Blank line struktural milik root `<html>` dipertahankan agar hasil `prettier-plugin-blade` tetap idempotent.

Isi `<pre>`, `<textarea>`, `<script>`, dan `<style>` tidak dimodifikasi oleh cleanup script.

## Pre-commit

Aktifkan repository hook:

```bash
git config core.hooksPath .githooks
```

Hook akan memastikan formatting, staged diff, PHP runtime, dan regression test lolos sebelum commit dibuat.
