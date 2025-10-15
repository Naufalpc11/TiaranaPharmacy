<template>
    <MainLayout>
        <div class="page-container">
            <section class="hero-bg-image full-viewport">
                <div class="hero-overlay" ref="heroContent">
                    <h1 class="home-title" ref="heroTitle">TIARANA FARMA</h1>
                    <p class="home-subtitle" ref="heroSubtitle1">Melayani Dengan Sepenuh Hati</p>
                    <p class="home-subtitle" ref="heroSubtitle2">Berdiri Sejak 2021</p>
                </div>
            </section>

            <section class="features-grid" ref="featuresGrid">
                <div class="feature-item">
                    <i class="fas fa-pills"></i>
                    <h3>Resep & Non-Resep</h3>
                    <p>Layanan obat resep dan non-resep dengan konsultasi farmasi profesional</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <h3>Jam Operasional</h3>
                    <p>Buka setiap hari dari pukul 08:00 - 22:00 WITA</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Produk Terjamin</h3>
                    <p>Keaslian dan kualitas produk terjamin dengan izin resmi BPOM</p>
                </div>
            </section>

            <section class="about-section" id="tentang-kami" ref="aboutSection">
                <div class="about-content">
                    <div class="about-text" ref="aboutText">
                        <h2>Tentang Kami</h2>
                        <p>Apotek Tiarana Farma telah melayani masyarakat sejak tahun 2021 dengan komitmen untuk menyediakan layanan kesehatan terbaik dan produk berkualitas. Dengan tim apoteker profesional, kami siap membantu Anda dengan konsultasi kesehatan dan informasi penggunaan obat yang tepat.</p>
                        <div class="about-features" ref="aboutFeatures">
                            <div class="about-feature">
                                <i class="fas fa-certificate"></i>
                                <h4>Apoteker Berpengalaman</h4>
                            </div>
                            <div class="about-feature">
                                <i class="fas fa-check-circle"></i>
                                <h4>Produk Berkualitas</h4>
                            </div>
                            <div class="about-feature">
                                <i class="fas fa-heart"></i>
                                <h4>Pelayanan Ramah</h4>
                            </div>
                        </div>
                    </div>
                    <div class="about-image" ref="aboutImage">
                        <div class="image-container"></div>
                    </div>
                </div>
            </section>

            <section class="services-section" ref="servicesSection">
                <h2 ref="servicesTitle">Layanan Kami</h2>
                <div class="services-zigzag" ref="servicesContainer">
                    <div class="service-row service-card" ref="serviceRow1">
                        <div class="service-image service-image-resep"></div>
                        <div class="service-content">
                            <div class="service-icon">
                                <i class="fas fa-prescription-bottle-alt"></i>
                            </div>
                            <h3>Layanan Resep</h3>
                            <p>Kami menyediakan layanan resep dokter dengan standar tinggi dan penuh ketelitian. Apoteker profesional kami akan memastikan setiap resep diproses dengan tepat dan aman, disertai dengan konsultasi mengenai penggunaan obat yang benar.</p>
                            <ul>
                                <li>Pelayanan resep dokter cepat dan akurat</li>
                                <li>Konsultasi penggunaan obat dengan apoteker</li>
                                <li>Pemeriksaan interaksi obat</li>
                                <li>Informasi efek samping dan cara penggunaan</li>
                            </ul>
                        </div>
                    </div>

                    <div class="service-row service-card reverse">
                        <div class="service-image service-image-konsultasi"></div>
                        <div class="service-content">
                            <div class="service-icon">
                                <i class="fas fa-notes-medical"></i>
                            </div>
                            <h3>Konsultasi Kesehatan</h3>
                            <p>Dapatkan konsultasi kesehatan gratis dengan apoteker berpengalaman kami. Kami siap membantu Anda dengan berbagai pertanyaan seputar kesehatan dan penggunaan obat yang tepat.</p>
                            <ul>
                                <li>Konsultasi gratis dengan apoteker</li>
                                <li>Informasi penggunaan obat yang aman</li>
                                <li>Pemeriksaan kesehatan dasar</li>
                                <li>Edukasi kesehatan</li>
                            </ul>
                        </div>
                    </div>

                    <div class="service-row service-card">
                        <div class="service-image service-image-pemeriksaan"></div>
                        <div class="service-content">
                            <div class="service-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <h3>Pemeriksaan Kesehatan</h3>
                            <p>Kami menyediakan layanan pemeriksaan kesehatan dasar untuk membantu Anda memantau kondisi kesehatan secara rutin. Dengan peralatan modern dan tenaga terlatih, kami siap memberikan pelayanan terbaik.</p>
                            <ul>
                                <li>Cek tekanan darah</li>
                                <li>Pemeriksaan gula darah</li>
                                <li>Pemeriksaan Kolestrol dan Asam Urat</li>
                                <li>Konsultasi hasil pemeriksaan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import MainLayout from '../Layouts/MainLayout.vue';
import { initializeHomeAnimations } from '../animations/homeAnimations';

// Refs for animations
const heroContent = ref(null);
const heroTitle = ref(null);
const heroSubtitle1 = ref(null);
const heroSubtitle2 = ref(null);
const featuresGrid = ref(null);
const aboutSection = ref(null);
const aboutText = ref(null);
const aboutImage = ref(null);
const aboutFeatures = ref(null);
const servicesSection = ref(null);
const servicesTitle = ref(null);
const serviceRow1 = ref(null);

onMounted(() => {
    // Initialize animations with refs
    initializeHomeAnimations({
        heroContent: heroContent.value,
        heroTitle: heroTitle.value,
        heroSubtitle1: heroSubtitle1.value,
        heroSubtitle2: heroSubtitle2.value,
        featuresGrid: featuresGrid.value,
        aboutSection: aboutSection.value,
        aboutText: aboutText.value,
        aboutImage: aboutImage.value,
        aboutFeatures: aboutFeatures.value,
        servicesSection: servicesSection.value,
        servicesTitle: servicesTitle.value,
        serviceRow1: serviceRow1.value
    });
});
</script>

<style lang="scss" scoped>
@import '../../css/home.scss';
</style>
