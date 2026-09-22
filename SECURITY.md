# Security Policy

## Reporting a Vulnerability

Jangan mempublikasikan detail kerentanan aktif melalui public issue sebelum mitigasi tersedia. Laporkan secara privat kepada maintainer/repository owner.

Sertakan minimal:

```text
Judul
Komponen terdampak
Versi/commit
Langkah reproduksi
Dampak
Bukti pendukung
Saran mitigasi bila ada
```

Jangan sertakan credential production, password, token, private key, atau data sensitif nyata.

## Security Controls

- Authentication
- Role-based authorization
- Resource ownership validation
- CSRF protection
- POST-only logout
- Login rate limiting
- IDOR protection
- Server-side assessment timer
- Upload validation
- Transaction untuk operasi kritis
- Historical result protection
- Regression testing

## Secrets

Jangan commit:

```text
.env
APP_KEY
private key
API token
database password
production credential
```

`.env.example` hanya berisi template tanpa secret.

## Dependency Audit

```bash
composer audit
npm audit
```

Hindari `npm audit fix --force` tanpa review.

## Production

Gunakan:

```dotenv
APP_ENV=production
APP_DEBUG=false
```

Production harus menggunakan HTTPS dan database tidak boleh diekspos langsung ke internet tanpa kebutuhan dan kontrol yang jelas.
