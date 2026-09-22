# Operations — Ujian CAT

## Health Check

```bash
php artisan about
php artisan route:list
```

## Cache

```bash
php artisan optimize:clear
```

Production dapat menggunakan:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Maintenance Mode

```bash
php artisan down
php artisan up
```

## Laravel Log

Lokasi: `storage/logs/laravel.log`.

Linux:

```bash
tail -f storage/logs/laravel.log
```

Windows PowerShell:

```powershell
Get-Content .\storage\logs\laravel.log -Wait
```

## Backup Database

```bash
mysqldump --single-transaction --routines --triggers -u root -p ujian > backup_ujian.sql
```

## Dependency Audit

```bash
composer audit
npm audit
```

Jangan gunakan `npm audit fix --force` tanpa review.

## Routine Release Checklist

```text
[ ] Backup database
[ ] Backup file upload
[ ] Pull source terbaru
[ ] composer install
[ ] npm build
[ ] optimize:clear
[ ] regression test
[ ] smoke test
[ ] review error log
[ ] maintenance mode off
```
