# Code Style — Ujian_CAT

## Tujuan

Source code first-party dibuat compact, konsisten, mudah dibaca, dan tidak
mengubah business logic aplikasi.

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
class ExampleController extends Controller {
    public function show(int $id): View {
        $user = auth()->user();

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

Formatter hanya ditujukan untuk source first-party:

- `app/**/*.php`
- `bootstrap/*.php`
- `config/**/*.php`
- `database/**/*.php`
- `routes/**/*.php`
- `tests/**/*.php`
- `resources/views/**/*.blade.php`
- `resources/js/**/*.js`
- `resources/css/**/*.css`

`public/` sengaja di-ignore karena berisi asset legacy, third-party, dan file
minified.

## Commands

Format semua source:

```bash
npm run format
```

Periksa tanpa menulis:

```bash
npm run format:check
```

Regression test:

```bash
php artisan optimize:clear
php artisan test
```

## Blade Whitespace Cleanup

Selain Prettier, project menggunakan `scripts/cleanup_blade_whitespace.py`
untuk menghapus blank line berlebih tepat setelah opening HTML container dan
tepat sebelum closing HTML container.

Contoh target:

```blade
<div class="col-sm-10">
    <input type="text" class="form-control" id="score">
</div>
```

Blank line struktural milik root `<html>` dipertahankan agar hasil
`prettier-plugin-blade` tetap idempotent.

Isi `<pre>`, `<textarea>`, `<script>`, dan `<style>` tidak dimodifikasi oleh
cleanup script.

### Format

```bash
npm run format
```

### Check

```bash
npm run format:check
```

`format:check` akan gagal dengan exit code 1 jika aturan whitespace custom
atau aturan Prettier belum terpenuhi.
