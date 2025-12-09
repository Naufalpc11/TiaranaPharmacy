# PANDUAN DEPLOYMENT KE NETLIFY + EMAIL SETUP

## BAGIAN 1: PERSIAPAN UNTUK NETLIFY

### A. Yang Perlu Diubah di Project

Netlify **tidak support backend PHP/Laravel native**. Kamu punya 2 pilihan:

#### Pilihan 1: Pisahkan Backend & Frontend (RECOMMENDED)
- **Frontend:** Deploy ke Netlify (React/Vue/Inertia)
- **Backend API:** Deploy ke Vercel, Railway, Heroku, atau PaaS lain

#### Pilihan 2: Full Stack di Service Lain
- Gunakan: **Vercel**, **Railway**, atau **Render** (bukan Netlify)

**SARAN:** Gunakan **Railway.app** (free tier bagus untuk Laravel)

---

## BAGIAN 2: SETUP EMAIL UNTUK FORGOT PASSWORD

### Opsi A: Mailtrap (FREE - Recommended untuk Testing)
Pros: Free, mudah setup, good docs
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="admin@tiaranapharmacy.com"
```

### Opsi B: Brevo (Sendinblue) - FREE dengan limit
- 300 emails/hari gratis
- Email verification berfungsi
- Setup di `.env`

```
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_smtp_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="admin@tiaranapharmacy.com"
```

### Opsi C: Gmail SMTP (TIDAK RECOMMENDED)
- Sering blocked
- Perlu app-specific password
- Risky untuk production

### Opsi D: Supabase Email (Baru)
- Belum stabil untuk production
- Lebih cocok untuk auth flow

---

## BAGIAN 3: IMPLEMENTASI FORGOT PASSWORD

### Step 1: Buat Migration untuk Password Reset
```bash
php artisan make:migration create_password_reset_tokens_table
```
(Sudah ada di default Laravel)

### Step 2: Buat Reset Password Request Handler
```bash
php artisan make:request ResetPasswordRequest
```

### Step 3: Buat Controller
```bash
php artisan make:controller Auth/PasswordResetController
```

### Step 4: Setup Routes
```php
Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])
    ->name('password.request');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->name('password.reset');
```

---

## BAGIAN 4: RECOMMENDED DEPLOYMENT ARCHITECTURE

```
┌─────────────────────────────────────────┐
│         Tiarana Pharmacy                 │
└─────────────────────────────────────────┘
         │                    │
    Frontend            Backend API
    (Netlify)          (Railway.app)
    ├─ Inertia         ├─ Laravel
    ├─ Vue/React       ├─ MySQL
    └─ Static Build    ├─ Redis
                       └─ Mail Server
```

### Services yang Dibutuhkan:

1. **Frontend (Netlify)**
   - Cost: FREE
   - Deploy: `npm run build` → upload `dist/`

2. **Backend (Railway.app)**
   - Cost: FREE tier $5/bulan
   - Include: Compute, Database, Network
   - Skalabel dengan mudah

3. **Email (Brevo/Mailtrap)**
   - Cost: FREE (300 emails/hari)
   - Include: SMTP relay

4. **Database (Railway or separate)**
   - Cost: Included in Railway
   - MySQL 8.0+

---

## BAGIAN 5: STEP-BY-STEP DEPLOYMENT

### Langkah 1: Setup Railway.app
```bash
1. Buat akun di railway.app
2. Create new project
3. Connect GitHub repo
4. Add MySQL service
5. Set environment variables di Railway dashboard
6. Deploy!
```

### Langkah 2: Build Frontend untuk Netlify
```bash
npm run build
# Output: dist/ folder
```

### Langkah 3: Deploy ke Netlify
```bash
1. Buka netlify.com
2. Drag & drop folder `dist/`
3. Atau connect GitHub untuk auto-deploy
4. Set API URL pointing ke Railway backend
```

### Langkah 4: Update CORS di Backend
```php
// config/cors.php
'allowed_origins' => ['https://your-netlify-domain.netlify.app'],
```

---

## BAGIAN 6: ENVIRONMENT VARIABLES UNTUK PRODUCTION

### Railway Backend (.env)
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-railway-url.railway.app

DB_CONNECTION=mysql
DB_HOST=railway-mysql-host
DB_PORT=3306
DB_DATABASE=tiarana
DB_USERNAME=postgres
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_smtp_key
MAIL_FROM_ADDRESS=admin@tiaranapharmacy.com

GEMINI_API_KEY=your_key
SANCTUM_STATEFUL_DOMAINS=your-netlify-domain.netlify.app
SESSION_DOMAIN=.railway.app
CORS_ALLOWED_ORIGINS=https://your-netlify-domain.netlify.app
```

### Netlify Frontend (.env)
```
VITE_API_URL=https://your-railway-url.railway.app
VITE_APP_NAME=Tiarana Pharmacy
```

---

## BAGIAN 7: COST BREAKDOWN (MONTHLY)

| Service | Free Tier | Cost |
|---------|-----------|------|
| Netlify Frontend | ✅ 100GB/month | FREE |
| Railway Backend | ✅ $5 credit/month | FREE (or $5) |
| Brevo Email | ✅ 300 emails/day | FREE |
| MySQL Database | ✅ Included Railway | FREE |
| **TOTAL** | | **FREE - $5/month** |

---

## PERBANDINGAN: RAILWAY vs VERCEL vs HEROKU

| Feature | Railway | Vercel | Heroku |
|---------|---------|--------|--------|
| Backend PHP | ✅ | ❌ (serverless only) | ✅ |
| Database | ✅ | ❌ | ✅ |
| Free Tier | $5/month | ❌ | ❌ |
| Easy Setup | ✅✅ | ✅ | ⚠️ |
| **Recommended** | ✅ | ❌ | ⚠️ |

---

## SELANJUTNYA: TODO

1. **Implementasi Forgot Password**
   - [ ] Buat Mailable untuk reset email
   - [ ] Setup PasswordResetController
   - [ ] Setup routes
   - [ ] Setup email config

2. **Setup Railway**
   - [ ] Create Railway account
   - [ ] Connect repository
   - [ ] Deploy MySQL
   - [ ] Configure environment

3. **Setup Email Provider**
   - [ ] Daftar Brevo/Mailtrap
   - [ ] Get SMTP credentials
   - [ ] Update .env
   - [ ] Test send email

4. **Deploy Frontend**
   - [ ] Setup Netlify
   - [ ] Connect GitHub
   - [ ] Setup build command
   - [ ] Deploy

5. **Post-Deployment**
   - [ ] Test login/logout
   - [ ] Test forgot password
   - [ ] Test email verification
   - [ ] Check logs

---

Mau saya implementasikan forgot password sekarang? 🚀
