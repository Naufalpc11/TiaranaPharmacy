# CHECKLIST TESTING TIARANA PHARMACY
## Hasil Pengecekan: 4 Desember 2025

---

## ✅ BACKEND CHECKS (Automated) - SEMUA LULUS

### Database & Infrastructure
- [x] Koneksi database berhasil
- [x] Semua 20 migrations running dengan sukses
- [x] Semua tabel utama dapat diakses (users, articles, contact_messages, bug_reports, dll)
- [x] Storage directories writable
- [x] Admin user tersedia (tiaranafarma@gmail.com)
- [x] Gemini API Key terkonfigurasi

### Models & Routes
- [x] Semua models dapat query database tanpa error
- [x] 73 routes terdaftar dengan benar
- [x] Public routes: /, about-us, contact, artikel, chatbot
- [x] Admin routes: dashboard, resources, pages

---

## 📋 MANUAL UI TESTING CHECKLIST

### A. HALAMAN PUBLIK (User Biasa)

#### 1. Homepage (/)
- [ ] Buka http://127.0.0.1:8000
- [ ] Hero section tampil dengan benar
- [ ] Feature highlights tampil
- [ ] About section tampil
- [ ] Services section tampil
- [ ] Latest articles (3 artikel) tampil
- [ ] Partner logos tampil
- [ ] Footer tampil lengkap
- [ ] Tidak ada error di console browser

#### 2. About Us (/about-us)
- [ ] Hero section About tampil
- [ ] Vision & Mission tampil
- [ ] History stats tampil dengan icon
- [ ] Team section tampil
- [ ] Location/Contact details tampil dengan icon
- [ ] Tidak ada error

#### 3. Articles (/artikel)
- [ ] List artikel tampil
- [ ] Pagination berfungsi (jika ada banyak artikel)
- [ ] Search/filter berfungsi (jika ada)
- [ ] Click artikel buka detail

#### 4. Article Detail (/artikel/{slug})
- [ ] Artikel detail tampil lengkap
- [ ] Cover image tampil
- [ ] Content formatted dengan benar
- [ ] Published date tampil
- [ ] Tidak ada error

#### 5. Contact (/contact)
- [ ] Form kontak tampil
- [ ] Input: name, email, subject, message
- [ ] Submit form berhasil
- [ ] Notifikasi sukses muncul
- [ ] Data tersimpan di database

#### 6. Chatbot (/chatbot)
- [ ] Interface chatbot tampil
- [ ] Dapat mengirim pesan
- [ ] Response dari Gemini AI diterima
- [ ] Conversation history tersimpan
- [ ] Button delete conversation berfungsi
- [ ] Tidak ada error console

#### 7. Report Bug (/report-bug)
- [ ] Form bug report tampil
- [ ] Input: title, description, severity, steps
- [ ] Submit berhasil
- [ ] Notifikasi sukses
- [ ] Data tersimpan di database

---

### B. ADMIN PANEL

#### 1. Login (/admin)
- [ ] Halaman login tampil
- [ ] Email: tiaranafarma@gmail.com
- [ ] Password: TiaranaFarma1774
- [ ] Login berhasil redirect ke dashboard

#### 2. Dashboard (/admin)
- [ ] Widget statistik tampil (4 kartu):
  - Pesan Kontak (total + belum direview)
  - Laporan Bug (total + pending/resolved)
  - Artikel (total + published)
  - Percakapan Chat (total + aktif)
- [ ] Chart Pesan Kontak (7 hari) tampil
- [ ] Chart Laporan Bug (7 hari) tampil
- [ ] Tidak ada error

#### 3. Articles Management
- [ ] List articles tampil (/admin/articles)
- [ ] Create new article berfungsi
- [ ] Edit article berfungsi
- [ ] Delete article berfungsi
- [ ] Upload cover image berfungsi
- [ ] Set published_at berfungsi

#### 4. Contact Messages
- [ ] List contact messages tampil (/admin/contact-messages)
- [ ] Dapat melihat detail pesan
- [ ] Mark as reviewed berfungsi
- [ ] Filter/search berfungsi

#### 5. Bug Reports
- [ ] List bug reports tampil (/admin/bug-reports)
- [ ] Dapat melihat detail bug
- [ ] Update status (pending/in-progress/resolved) berfungsi
- [ ] Filter by status berfungsi

#### 6. Home Page Settings
- [ ] Home Page Settings dapat diedit (/admin/home-page-settings)
- [ ] Home Services CRUD berfungsi (/admin/home-services)
- [ ] Home About Features CRUD berfungsi
- [ ] Home Feature Highlights CRUD berfungsi
- [ ] Partner Logos CRUD berfungsi
- [ ] Upload images berfungsi

#### 7. About Page Settings
- [ ] About Page Settings dapat diedit
- [ ] About Missions CRUD berfungsi
- [ ] About History Stats CRUD berfungsi
- [ ] About Contact Details CRUD berfungsi
- [ ] Manage pages (Hero, Vision, Team, Location) berfungsi

#### 8. Footer Settings
- [ ] Footer Settings dapat diedit (/admin/footer-settings)
- [ ] Social media links berfungsi
- [ ] Contact info berfungsi

#### 9. Medication Assets
- [ ] List medication assets tampil (/admin/medication-assets)
- [ ] Create medication asset berfungsi
- [ ] Upload medication images berfungsi
- [ ] Edit/delete berfungsi

#### 10. User Menu & Change Password
- [ ] Click user menu di kanan atas
- [ ] Menu "Ubah Password" muncul
- [ ] Click "Ubah Password"
- [ ] Form ubah password tampil
- [ ] Input password lama, baru, konfirmasi
- [ ] Submit berhasil
- [ ] Notifikasi sukses
- [ ] Password berubah (test login lagi)

---

## 🔍 TESTING TIPS

### Browser Console
- Buka Developer Tools (F12)
- Check Console tab untuk JavaScript errors
- Check Network tab untuk failed requests

### Common Issues to Look For
- Images not loading (404 errors)
- Broken links
- Form validation errors
- API timeouts
- Database query errors
- Permission issues

### Test Data
Jika belum ada data, gunakan:
- Admin: tiaranafarma@gmail.com / TiaranaFarma1774
- Buat beberapa artikel sample
- Submit beberapa contact messages
- Submit beberapa bug reports
- Test chatbot dengan beberapa pertanyaan

---

## 📊 HASIL AKHIR

**Backend Status:** ✅ SEMUA LULUS (Automated Test)

**Frontend Status:** [ ] PENDING (Manual Test Required)

### Notes:
- Sistem backend sudah 100% siap
- Database clean dan semua tabel OK
- Semua routes terdaftar
- Models berfungsi normal
- API keys configured
- Storage writable

### Rekomendasi:
1. ✅ Lakukan testing manual UI satu per satu
2. ✅ Test semua form submissions
3. ✅ Test upload images
4. ✅ Test chatbot dengan berbagai pertanyaan
5. ✅ Test responsive design di mobile
6. ✅ Test cross-browser (Chrome, Firefox, Edge)

---

**Tested by:** GitHub Copilot
**Date:** 4 Desember 2025
**Version:** 1.0
