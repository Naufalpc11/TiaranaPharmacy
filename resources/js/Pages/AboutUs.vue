<template>
    <MainLayout>
        <div class="about-us-container">
            <header class="about-header" role="banner">
                <div class="about-header-overlay">
                    <nav class="breadcrumbs" aria-label="Breadcrumb">
                        <Link href="/" class="crumb">Beranda</Link>
                        <span class="divider" aria-hidden="true">/</span>
                        <span class="current" aria-current="page">Tentang Kami</span>
                    </nav>
                    <h1>Tentang Kami</h1>
                    <p>
                        Apotek Tiarana Farma adalah apotek terpercaya yang menyediakan layanan kesehatan
                        berkualitas dengan dukungan tim profesional dan fasilitas modern.
                    </p>
                    <div class="cta-group">
                        <button class="cta-btn primary" @click="scrollTo('#lokasi')">
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i> Lihat Lokasi
                        </button>
                        <Link href="/contact" class="cta-btn outline">
                            <i class="fas fa-comments" aria-hidden="true"></i> Hubungi Kami
                        </Link>
                    </div>
                </div>
            </header>

            <section class="about-content" aria-labelledby="tentang">
                <div class="about-section">
                    <h2 id="tentang" class="visually-hidden">Tentang Tiarana Farma</h2>
                    <p>
                        Apotek Tiarana Farma berkomitmen memberi pelayanan ramah, cepat, dan akurat.
                        Tim kami terdiri dari tenaga profesional yang siap membantu kebutuhan obat,
                        konsultasi, dan informasi kesehatan harian Anda.
                    </p>
                </div>
            </section>

            <section class="stats-section" aria-label="Statistik ringkas">
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i>
                        <div class="value">2021</div>
                        <div class="label">Mulai Beroperasi</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-user-friends" aria-hidden="true"></i>
                        <div class="value">5K+</div>
                        <div class="label">Pelanggan Dilayani</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-pills" aria-hidden="true"></i>
                        <div class="value">1000+</div>
                        <div class="label">Produk Tersedia</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <div class="value">4.9/5</div>
                        <div class="label">Kepuasan Pelanggan</div>
                    </div>
                </div>
            </section>

            <section class="history-section" aria-labelledby="sejarah">
                <div class="history-content">
                    <h2 id="sejarah">Sejarah Kami</h2>
                    <p>
                        Berdiri sejak tahun 2021, Apotek Tiarana Farma telah melayani ribuan pelanggan
                        dengan dedikasi tinggi dalam memberikan layanan kesehatan. Berawal dari sebuah
                        apotek kecil, kini kami berkembang menjadi apotek modern dengan fasilitas lengkap
                        untuk mendukung kebutuhan kesehatan masyarakat.
                    </p>
                </div>
            </section>

            <section class="values-section" aria-labelledby="nilai">
                <h2 id="nilai" class="section-title">Nilai Kami</h2>
                <div class="values-grid">
                    <article class="value-card">
                        <i class="fas fa-heartbeat" aria-hidden="true"></i>
                        <h3>Empati</h3>
                        <p>Kami mendengarkan dan memahami kebutuhan Anda dengan tulus.</p>
                    </article>
                    <article class="value-card">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        <h3>Keamanan</h3>
                        <p>Produk terjamin keaslian dan disimpan sesuai standar mutu.</p>
                    </article>
                    <article class="value-card">
                        <i class="fas fa-bolt" aria-hidden="true"></i>
                        <h3>Responsif</h3>
                        <p>Layanan cepat dengan informasi yang jelas dan akurat.</p>
                    </article>
                    <article class="value-card">
                        <i class="fas fa-user-md" aria-hidden="true"></i>
                        <h3>Profesional</h3>
                        <p>Ditangani apoteker berpengalaman dan tim yang kompeten.</p>
                    </article>
                </div>
            </section>

            <section class="team-section" aria-labelledby="team">
                <h2 id="team">Apoteker Kami</h2>
                <div class="pharmacist-card">
                    <div class="photo">
                        <div class="img-container">
                            <img :src="pharmacist.photo" :alt="pharmacist.alt" loading="lazy" @error="onImgError($event)" />
                        </div>
                    </div>
                    <div class="info">
                        <h3>{{ pharmacist.name }}</h3>
                        <p class="role" v-if="pharmacist.role">{{ pharmacist.role }}</p>
                        <div class="pharmacist-info">
                            <p v-if="pharmacist.stra">STRA: {{ pharmacist.stra }}</p>
                            <p v-if="pharmacist.sipa">SIPA: {{ pharmacist.sipa }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="location-section" id="lokasi" aria-labelledby="lokasi-title">
                <h2 id="lokasi-title">Lokasi Kami</h2>
                <div class="location-content">
                    <div class="location-info">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            <div>
                                <h3>Alamat</h3>
                                <p>Jl. Sepinggan Baru</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone-alt" aria-hidden="true"></i>
                            <div>
                                <h3>Telepon</h3>
                                <p>0812-3456-7890</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            <div>
                                <h3>Jam Buka</h3>
                                <p>Senin - Sabtu: 08:00 - 21:00</p>
                                <p>Minggu: 09:00 - 20:00</p>
                            </div>
                        </div>
                    </div>
                    <div class="map-container">
                        <iframe
                            title="Peta lokasi Apotek Tiarana Farma"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8714503220267!2d116.9010663745559!3d-1.2482881355849063!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df145cac72eb3bf%3A0x16844957779a9566!2sApotek%20Tiarana%20Farma!5e0!3m2!1sid!2sid!4v1759721919148!5m2!1sid!2sid"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </section>
        </div>
    </MainLayout>
    </template>

<script setup>
import { onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { initializeAboutUsAnimations } from '../animations/aboutUsAnimations';
import MainLayout from '../Layouts/MainLayout.vue';

const pharmacist = ref({
    name: 'apt. Titik Tresnowati, S. Si',
    role: 'Apoteker Penanggung Jawab',
    stra: '19880824/STRA-YYYY/2023',
    sipa: '19880824/SIPA-XX/2023',
    photo: '/images/team/pharmacist1.jpg',
    alt: 'Foto Apoteker Penanggung Jawab',
});

const onImgError = (e) => {
    // Fallback styling when image not found
    e.target.style.opacity = '0';
    e.target.parentElement.classList.add('no-image');
};

const scrollTo = (selector) => {
    const el = document.querySelector(selector);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

onMounted(() => {
    initializeAboutUsAnimations();
});
</script>

<style lang="scss" scoped>
@import '../../css/AboutUs.scss';
</style>
