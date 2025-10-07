<template>
  <MainLayout>
    <div class="page-container">
    <section class="hero-bg-image full-viewport">
      <div class="hero-overlay">
        <h1 class="home-title">Apotek Tiarana Farma</h1>
        <p class="home-subtitle">Melayani Dengan Sepenuh Hati</p>
        <p class="home-subtitle">Berdiri Sejak 2010</p>
      </div>
    </section>

    <section class="company-info" id="tentang-kami">
      <h2>Tentang Kami</h2>
      <p>
        <strong>Apotek Tiarana Farma</strong> berdiri sejak tahun <b>2010</b> di Kota <b>[Nama Kota]</b>. Kami berkomitmen memberikan pelayanan kesehatan terbaik untuk masyarakat dengan produk berkualitas dan tenaga profesional.
      </p>
    </section>

    <section class="vision-mission" id="visi-misi">
      <h2>Visi</h2>
      <p>
        Menjadi apotek pilihan utama masyarakat dengan pelayanan profesional dan produk berkualitas.
      </p>
      <h2>Misi</h2>
      <ul>
        <li>Menyediakan obat dan produk kesehatan berkualitas.</li>
        <li>Memberikan edukasi kesehatan kepada masyarakat.</li>
        <li>Mengutamakan pelayanan ramah dan profesional.</li>
      </ul>
    </section>

    <section class="partnership" id="kerjasama">
      <h2>Kerjasama</h2>
      <p>
        Kami terbuka untuk kerjasama dengan berbagai pihak demi meningkatkan layanan kesehatan masyarakat.
      </p>
    </section>

    <section class="location-contact" id="lokasi">
      <h2>Lokasi Apotek</h2>
      <div class="gmaps-container">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8714503220267!2d116.9010663745559!3d-1.2482881355849063!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df145cac72eb3bf%3A0x16844957779a9566!2sApotek%20Tiarana%20Farma!5e0!3m2!1sid!2sid!4v1759721919148!5m2!1sid!2sid"
          width="800"
          height="400"
          style="border:0;"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
      </div>
      <h2 id="kontak">Kontak</h2>
      <ul>
        <li><strong>Telepon:</strong> 0812-3456-7890</li>
        <li><strong>Email:</strong> info@tiaranafarma.com</li>
        <li><strong>Alamat:</strong> Jl. Sehat No. 10, [Nama Kota]</li>
      </ul>
    </section>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
</script>

<style lang="scss" scoped>
@import '../../css/home.scss';
</style>
