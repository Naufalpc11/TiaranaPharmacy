# Deploy Laravel ke Hostinger (hPanel) — Langkah Ringkas

## 1) Persiapan
- Paket harus mendukung PHP 8.2/8.3 + akses SSH/Terminal.
- Aktifkan ekstensi PHP: pdo_mysql, openssl, mbstring, tokenizer, xml, curl, fileinfo, gd.
- Siapkan domain/subdomain dan pastikan SSL aktif (HTTPS).

## 2) Struktur & Upload
- Atur document root domain/subdomain ke folder `public/` proyek (hPanel → Edit Website Path).
- Upload kode via Git (Deploy from Git) atau upload ZIP lalu extract ke folder proyek.
- Jika Composer tidak tersedia di server, install dependencies di lokal (`composer install --no-dev --prefer-dist`) lalu upload folder `vendor/`.

## 3) Database
- Buat MySQL di hPanel → Database → MySQL.
- Catat: host (kadang bukan `localhost`), nama DB, user, password, port (umumnya 3306).

## 4) Konfigurasi .env
- Salin `.env.cpanel.example` menjadi `.env`, isi:
  - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domainmu`
  - `DB_CONNECTION=mysql`, `DB_HOST=<host dari hPanel>`, `DB_PORT=3306`, `DB_DATABASE/USERNAME/PASSWORD` sesuai yang dibuat.
  - `MAIL_*` sesuai SMTP yang dipakai (Brevo/Mailtrap/email Hostinger).
  - `QUEUE_CONNECTION=sync` jika belum menyiapkan worker.
- `APP_KEY` dikosongkan dulu, nanti diisi via perintah `php artisan key:generate`.

## 5) Jalankan perintah via SSH (folder proyek)
```bash
composer install --no-dev --prefer-dist
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 6) Permission & keamanan
- Pastikan `storage/` dan `bootstrap/cache/` writable (umumnya 755/775).
- `APP_URL` harus HTTPS; aktifkan SSL di hPanel.
- Jika perlu scheduler: cron `* * * * * php /home/USER/tiaranapharmacy/artisan schedule:run >> /dev/null 2>&1`.
- Jika butuh queue async: cron/daemon `php artisan queue:work --stop-when-empty` atau tetap `QUEUE_CONNECTION=sync`.

## 7) Verifikasi pasca-deploy
- Cek homepage, login, forgot password, dan kirim email.
- Jika error 500: lihat `storage/logs/laravel.log`; bisa reset cache `php artisan config:clear && php artisan cache:clear`.

## Catatan API/Frontend
- Jika frontend di-build terpisah (Vite/Inertia SPA), tetap set `VITE_API_URL` ke domain backend.
- Atur CORS di backend untuk domain frontend jika beda origin.
