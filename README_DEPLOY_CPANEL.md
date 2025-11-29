# Deploy Laravel ke cPanel (Checklist Singkat)

## Persiapan
- Pastikan PHP server >= 8.1
- Point document root ke folder `public/` proyek (ubah domain atau subdomain agar mengarah ke `project/public`)
- Upload source code (via Git cPanel atau zip), jalankan Composer di server

## Konfigurasi Environment
- Duplikasi `.env.cpanel.example` menjadi `.env` dan isi:
  - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://yourdomain.com`
  - `DB_CONNECTION=mysql`, `DB_HOST=localhost`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
  - `MAIL_MAILER=smtp`, `MAIL_HOST=mail.yourdomain.com`, `MAIL_PORT=465`, `MAIL_ENCRYPTION=ssl`, `MAIL_USERNAME`, `MAIL_PASSWORD`
  - Opsional: `ADMIN_EMAIL`, `ADMIN_PASSWORD` untuk seeder admin

## Perintah Pasca Deploy
Jalankan ini di terminal cPanel pada direktori proyek:

```
composer install --no-dev --prefer-dist
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Email Reset Password
- Pastikan kredensial SMTP benar dan akun email aktif di cPanel
- Uji halaman "Forgot Password" pada login Filament

## Izin & Keamanan
- Pastikan `storage/` dan `bootstrap/cache/` writable
- Pasang SSL dan paksa HTTPS (TrustProxies / `APP_URL` menggunakan https)
- Set `SESSION_DRIVER=file`, `CACHE_DRIVER=file` di cPanel shared hosting

## Queue (opsional)
- Jika ingin email antri: `QUEUE_CONNECTION=database`, buat cron untuk `php artisan queue:work --stop-when-empty`
- Jika tidak, gunakan `QUEUE_CONNECTION=sync`
