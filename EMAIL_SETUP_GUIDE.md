# SETUP GUIDE: EMAIL & FORGOT PASSWORD

## ✅ SUDAH DIIMPLEMENTASIKAN

### 1. Fitur Forgot Password
- ✅ Form request email di halaman login admin
- ✅ Generate secure token (64 karakter)
- ✅ Kirim email dengan link reset
- ✅ Validasi token (max 24 jam)
- ✅ Reset password baru
- ✅ Delete token setelah berhasil

### 2. Email Template
- ✅ Professional HTML email
- ✅ Link reset dengan signed URL
- ✅ 24 hour expiration
- ✅ User-friendly message

### 3. Database Table
- ✅ `password_reset_tokens` (already created by migration)
- Columns: email, token, created_at

---

## 🔧 SETUP YANG HARUS KAMU LAKUKAN

### Step 1: Pilih Email Provider

#### OPSI A: Brevo (RECOMMENDED - Free 300/day)
```
1. Daftar di https://www.brevo.com
2. Buat API key/SMTP credentials
3. Copy credentials ke .env
```

#### OPSI B: Mailtrap (Free for testing)
```
1. Daftar di https://mailtrap.io
2. Setup inbox
3. Copy SMTP credentials
```

#### OPSI C: Gmail SMTP (Tidak recommended)
```
Risky dan sering diblock
```

### Step 2: Update .env

**Untuk Brevo:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-smtp-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="admin@tiaranapharmacy.com"
MAIL_FROM_NAME="Tiarana Pharmacy"
```

**Untuk Mailtrap:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="admin@tiaranapharmacy.com"
MAIL_FROM_NAME="Tiarana Pharmacy"
```

**Untuk Gmail (Optional):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
```

### Step 3: Test Email

```bash
# Test send email
php artisan tinker

Mail::to('your-email@gmail.com')->send(
    new App\Mail\ResetPasswordMail(
        \App\Models\User::first(), 
        'test-token-123'
    )
);
```

### Step 4: Update APP_URL

```env
APP_URL=http://127.0.0.1:8000  # Local development
# Nanti diganti saat production:
APP_URL=https://your-railway-url.railway.app
```

---

## 📱 CARA MENGGUNAKAN

### Admin Lupa Password:
1. Buka `/admin` (Filament login)
2. Klik "Lupa Password?" link
3. Masukkan email
4. Klik "Kirim Link Reset"
5. Cek email (inbox atau spam)
6. Klik link di email
7. Set password baru
8. Login dengan password baru

---

## 🔒 FITUR KEAMANAN

### Yang sudah diimplementasikan:
- ✅ Token 64-bit random (secure)
- ✅ Token di-hash sebelum disimpan
- ✅ Token expired dalam 24 jam
- ✅ Validasi email exists
- ✅ Validasi password strong (min 8 char)
- ✅ Email verification
- ✅ One-time use token (delete setelah use)

---

## 📊 DATABASE STRUCTURE

```sql
-- password_reset_tokens (sudah ada dari default Laravel)
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255),
    created_at TIMESTAMP
);
```

---

## 🚀 DEPLOYMENT CHECKLIST

### Saat Deploy ke Production (Railway):

1. **Email Setup**
   - [ ] Pilih email provider (Brevo/Mailtrap)
   - [ ] Get SMTP credentials
   - [ ] Set di Railway dashboard environment variables

2. **Configuration**
   - [ ] Update `APP_URL` di .env production
   - [ ] Set `MAIL_FROM_ADDRESS`
   - [ ] Set `MAIL_FROM_NAME`

3. **Testing**
   - [ ] Test forgot password flow
   - [ ] Verify email diterima
   - [ ] Test reset password
   - [ ] Verify new password works

4. **Security**
   - [ ] Enable HTTPS only
   - [ ] Check CSRF protection
   - [ ] Verify token validation

---

## 💡 TIPS

### Testing Email Locally
Gunakan **Mailtrap** (free) atau **MailHog** untuk testing tanpa send email sebenarnya.

### Production Ready
Sebelum production:
1. Test email sending
2. Test full forgot password flow
3. Check email deliverability
4. Setup email domain (optional SPF/DKIM)

### Monitoring
Monitor email logs:
```bash
# See email logs (Laravel)
tail -f storage/logs/laravel.log

# Check Brevo dashboard untuk delivery status
```

---

## ❓ TROUBLESHOOTING

### Email tidak terkirim?
- [ ] Check `.env` SMTP credentials
- [ ] Check firewall/port 587 terbuka
- [ ] Check email provider API key valid
- [ ] Check logs: `storage/logs/laravel.log`

### Token invalid?
- [ ] Check `password_reset_tokens` table punya data
- [ ] Check token belum expired (24 jam)
- [ ] Check email match

### Password reset tidak work?
- [ ] Check user email valid
- [ ] Check password meet validation
- [ ] Check no database errors

---

## NEXT STEPS

1. **Setup Email Provider** (Brevo recommended)
   - Free 300 emails/day
   - No credit card needed
   - SMTP mudah setup

2. **Test Forgot Password**
   - Buka `/admin`
   - Click "Lupa Password?"
   - Test flow end-to-end

3. **Setup Railway** (untuk production)
   - Deploy backend
   - Set environment variables
   - Test di production

4. **Deploy Frontend** (Netlify)
   - Push frontend ke Netlify
   - Update API_URL

---

**Status:** ✅ Forgot Password Feature Ready to Use
**Need:** Email Provider Setup + .env Configuration

Mau langsung test sekarang atau perlu bantuan setup email provider? 🚀
