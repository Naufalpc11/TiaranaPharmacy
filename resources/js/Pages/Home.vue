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
                <FeatureHighlightCard
                    v-for="feature in featureHighlights"
                    :key="feature.title"
                    :icon="feature.icon"
                    :title="feature.title"
                    :description="feature.description"
                />
            </section>

            <section class="about-section" id="tentang-kami" ref="aboutSection">
                <div class="about-content">
                    <div class="about-text" ref="aboutText">
                        <h2 class="section-title">Tentang Kami</h2>
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
                <h2 class="section-title" ref="servicesTitle">Layanan Kami</h2>
                <div class="services-zigzag" ref="servicesContainer">
                    <ServiceCard
                        v-for="(service, index) in services"
                        :key="service.title"
                        :title="service.title"
                        :icon="service.icon"
                        :description="service.description"
                        :items="service.items"
                        :image-class="service.imageClass"
                        :reverse="service.reverse"
                        :ref="index === 0 ? setServiceRow1 : null"
                    />
                </div>
            </section>

            <!-- Partners -->
            <PartnerLogos />

            <!-- Articles highlight / CTA -->
            <ArticleHighlight
                :articles="latestArticles"
                :title="highlightTitle"
                :excerpt="highlightExcerpt"
                :date="highlightDate"
                :image="highlightImage"
                :primary-href="highlightPrimaryHref"
                :secondary-href="articlesIndexHref"
            />
        </div>
    </MainLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { initializeHomeAnimations } from '../animations/homeAnimations';
import ArticleHighlight from '../Components/ArticleHighlight.vue';
import FeatureHighlightCard from '../Components/FeatureHighlightCard.vue';
import PartnerLogos from '../Components/PartnerLogos.vue';
import ServiceCard from '../Components/ServiceCard.vue';
import MainLayout from '../Layouts/MainLayout.vue';

const props = defineProps({
    articles: {
        type: Array,
        default: () => [],
    },
    articlesIndexUrl: {
        type: String,
        default: '/artikel',
    },
});

// Refs for animations
const heroContent = ref(null);
const heroTitle = ref(null);
const heroSubtitle1 = ref(null);
const heroSubtitle2 = ref(null);
const featuresGrid = ref(null);
const featureHighlights = [
    {
        icon: 'fas fa-pills',
        title: 'Resep & Non-Resep',
        description: 'Layanan obat resep dan non-resep dengan konsultasi farmasi profesional'
    },
    {
        icon: 'fas fa-clock',
        title: 'Jam Operasional',
        description: 'Buka setiap hari dari pukul 08:00 - 22:00 WITA'
    },
    {
        icon: 'fas fa-shield-alt',
        title: 'Produk Terjamin',
        description: 'Keaslian dan kualitas produk terjamin dengan izin resmi BPOM'
    }
];
const aboutSection = ref(null);
const aboutText = ref(null);
const aboutImage = ref(null);
const aboutFeatures = ref(null);
const servicesSection = ref(null);
const servicesTitle = ref(null);
const serviceRow1 = ref(null);
const services = [
    {
        icon: 'fas fa-prescription-bottle-alt',
        title: 'Layanan Resep',
        description: 'Kami menyediakan layanan resep dokter dengan standar tinggi dan penuh ketelitian. Apoteker profesional kami akan memastikan setiap resep diproses dengan tepat dan aman, disertai dengan konsultasi mengenai penggunaan obat yang benar.',
        items: [
            'Pelayanan resep dokter cepat dan akurat',
            'Konsultasi penggunaan obat dengan apoteker',
            'Pemeriksaan interaksi obat',
            'Informasi efek samping dan cara penggunaan'
        ],
        imageClass: 'service-image-resep',
        reverse: false
    },
    {
        icon: 'fas fa-notes-medical',
        title: 'Konsultasi Kesehatan',
        description: 'Dapatkan konsultasi kesehatan gratis dengan apoteker berpengalaman kami. Kami siap membantu Anda dengan berbagai pertanyaan seputar kesehatan dan penggunaan obat yang tepat.',
        items: [
            'Konsultasi gratis dengan apoteker',
            'Informasi penggunaan obat yang aman',
            'Pemeriksaan kesehatan dasar',
            'Edukasi kesehatan'
        ],
        imageClass: 'service-image-konsultasi',
        reverse: true
    },
    {
        icon: 'fas fa-heartbeat',
        title: 'Pemeriksaan Kesehatan',
        description: 'Kami menyediakan layanan pemeriksaan kesehatan dasar untuk membantu Anda memantau kondisi kesehatan secara rutin. Dengan peralatan modern dan tenaga terlatih, kami siap memberikan pelayanan terbaik.',
        items: [
            'Cek tekanan darah',
            'Pemeriksaan gula darah',
            'Pemeriksaan Kolestrol dan Asam Urat',
            'Konsultasi hasil pemeriksaan'
        ],
        imageClass: 'service-image-pemeriksaan',
        reverse: false
    }
];

const setServiceRow1 = (component) => {
    if (!component) {
        serviceRow1.value = null;
        return;
    }

    serviceRow1.value = component.root?.value ?? component.$el ?? component;
};
// Article highlight data
const articleImageFallback = null;
const articleExcerptFallback = 'Antibiotik bukan untuk semua batuk-pilek. Pelajari indikasi, efek samping umum, dan mengapa harus dihabiskan sesuai resep.';
const articleTitleFallback = 'Amoksisilin: Kapan Perlu Kapan Tidak';
const articleDateFallback = '12/08/2025';

const latestArticles = computed(() => props.articles ?? []);
const firstArticle = computed(() => latestArticles.value[0] ?? null);
const highlightTitle = computed(() => firstArticle.value?.title ?? articleTitleFallback);
const highlightExcerpt = computed(() => firstArticle.value?.excerpt ?? articleExcerptFallback);
const highlightDate = computed(() => firstArticle.value?.published_at ?? articleDateFallback);
const highlightImage = computed(() => firstArticle.value?.cover_image_url ?? articleImageFallback);
const highlightPrimaryHref = computed(() => firstArticle.value?.url ?? props.articlesIndexUrl);
const articlesIndexHref = computed(() => props.articlesIndexUrl ?? '/artikel');

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
