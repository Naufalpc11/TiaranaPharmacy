# Supabase Integration Setup

## Apa itu Supabase?
Supabase adalah Firebase open-source alternative dengan PostgreSQL database built-in, authentication, real-time subscriptions, dan storage. Cocok untuk production dengan tier gratis yang sangat generous.

## Setup Credentials ✅
```
SUPABASE_URL=https://ppmtmekrwlbtedumxwqi.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
SUPABASE_SERVICE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

## Fitur yang Sudah Diimplementasi

### 1. Password Reset via Supabase Auth
**File:** `app/Services/SupabaseService.php`
- `sendPasswordResetEmail($email)` - Kirim email reset via Supabase
- `resetPassword($email, $token, $password)` - Verify token dan update password

**Controller:** `app/Http/Controllers/Auth/PasswordResetController.php`
- Sudah diupdate untuk pakai Supabase API
- Token verification via Supabase
- Sync password ke local database juga

### 2. Routes
```
POST /auth/forgot-password - Send reset email
POST /auth/reset-password - Reset password dengan token
GET /test-supabase - Test Supabase connection
```

## Keuntungan Menggunakan Supabase

✅ **Email Built-in**
- Automatic email reset password
- No third-party SMTP needed (Brevo/Mailtrap)
- Customizable email templates di dashboard

✅ **Auth Management**
- User registration & login
- Email verification
- Password reset workflow
- Session management

✅ **Database**
- PostgreSQL (lebih powerful than MySQL)
- Real-time subscriptions
- Row level security (RLS)

✅ **Free Tier**
- Unlimited auth users
- 500MB database
- 1GB file storage
- Unlimited real-time connections

## Langkah Berikutnya

1. **Test Email Reset**
   - Akses http://127.0.0.1:8000/test-supabase
   - Cek di Supabase dashboard → Authentication → Recent activity

2. **Configure Email Templates** (optional)
   - Supabase dashboard → Settings → Email Templates
   - Customize reset password email template

3. **Integrate ke Frontend**
   - Tambah forgot password form ke login page
   - Integrate reset password flow

4. **Deploy**
   - Setup Railway untuk backend
   - Setup Netlify untuk frontend
   - Update SUPABASE_URL di production .env

## Testing Commands

Test via browser:
```
http://127.0.0.1:8000/test-supabase
```

Test via code:
```php
$supabase = app(App\Services\SupabaseService::class);
$result = $supabase->sendPasswordResetEmail('tiaranafarma@gmail.com');
dd($result);
```

## Troubleshooting

**"Failed to send reset email"**
- Cek Supabase credentials di .env
- Verify email domain setup di Supabase dashboard

**"Invalid or expired reset token"**
- Token valid 24 jam dari saat request
- User harus check email dalam 24 jam

**"Failed to update password"**
- Ensure user exists di Supabase Auth
- Check Supabase logs untuk detailed error

## Production Checklist

- [ ] Enable Email Verification untuk new signups
- [ ] Configure custom email domain (optional)
- [ ] Setup rate limiting untuk password reset
- [ ] Configure SMTP relay jika perlu (optional)
- [ ] Enable 2FA untuk admin users
- [ ] Setup Row Level Security (RLS) untuk tables
- [ ] Configure backup schedule
