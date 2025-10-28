<template>
    <MainLayout>
        <div class="page-container">
            <section class="hero-bg-image full-viewport" :style="heroBackgroundStyle">
                <div class="hero-overlay" ref="heroContent">
                    <h1 class="home-title" ref="heroTitle">{{ hero.title }}</h1>
                    <p
                        v-if="hero.subtitlePrimary"
                        class="home-subtitle"
                        ref="heroSubtitle1"
                    >
                        {{ hero.subtitlePrimary }}
                    </p>
                    <p
                        v-if="hero.subtitleSecondary"
                        class="home-subtitle"
                        ref="heroSubtitle2"
                    >
                        {{ hero.subtitleSecondary }}
                    </p>
                </div>
            </section>

            <section class="features-grid" ref="featuresGrid">
                <FeatureHighlightCard
                    v-for="feature in featureHighlights"
                    :key="feature.title"
                    :icon="feature.icon"
                    :icon-image-url="feature.iconImageUrl"
                    :title="feature.title"
                    :description="feature.description"
                />
            </section>

            <section class="about-section" id="tentang-kami" ref="aboutSection">
                <div class="about-content">
                    <div class="about-text" ref="aboutText">
                        <h2 class="section-title">{{ about.title }}</h2>
                        <p>{{ about.description }}</p>
                        <div class="about-features" ref="aboutFeatures">
                            <div
                                v-for="feature in about.features"
                                :key="feature.title"
                                class="about-feature"
                            >
                                <div
                                    class="about-feature__icon"
                                    :class="{ 'has-image': Boolean(feature.iconImageUrl) }"
                                >
                                    <img
                                        v-if="feature.iconImageUrl"
                                        :src="feature.iconImageUrl"
                                        :alt="`${feature.title} icon`"
                                    />
                                    <i v-else :class="feature.icon"></i>
                                </div>
                                <h4>{{ feature.title }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="about-image" ref="aboutImage">
                        <div class="image-container" :style="aboutImageStyle"></div>
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
                        :icon-image-url="service.iconImageUrl"
                        :description="service.description"
                        :items="service.items"
                        :image-class="service.imageClass"
                        :image-url="service.imageUrl"
                        :reverse="service.reverse"
                        :ref="index === 0 ? setServiceRow1 : null"
                    />
                </div>
            </section>

            <!-- Partners -->
            <PartnerLogos :logos="partnerLogos" />

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
    homeContent: {
        type: Object,
        default: () => ({}),
    },
});

const defaultHero = {
    title: 'TIARANA FARMA',
    subtitlePrimary: 'Melayani Dengan Sepenuh Hati',
    subtitleSecondary: 'Berdiri Sejak 2021',
};

const defaultFeatureHighlights = [
    {
        icon: 'fas fa-pills',
        iconImageUrl: null,
        title: 'Resep & Non-Resep',
        description: 'Layanan obat resep dan non-resep dengan konsultasi farmasi profesional',
    },
    {
        icon: 'fas fa-clock',
        iconImageUrl: null,
        title: 'Jam Operasional',
        description: 'Buka setiap hari dari pukul 08:00 - 22:00 WITA',
    },
    {
        icon: 'fas fa-shield-alt',
        iconImageUrl: null,
        title: 'Produk Terjamin',
        description: 'Keaslian dan kualitas produk terjamin dengan izin resmi BPOM',
    },
];

const defaultAbout = {
    title: 'Tentang Kami',
    description: 'Apotek Tiarana Farma telah melayani masyarakat sejak tahun 2021 dengan komitmen untuk menyediakan layanan kesehatan terbaik dan produk berkualitas. Dengan tim apoteker profesional, kami siap membantu Anda dengan konsultasi kesehatan dan informasi penggunaan obat yang tepat.',
    features: [
        {
            icon: 'fas fa-certificate',
            iconImageUrl: null,
            title: 'Apoteker Berpengalaman',
        },
        {
            icon: 'fas fa-check-circle',
            iconImageUrl: null,
            title: 'Produk Berkualitas',
        },
        {
            icon: 'fas fa-heart',
            iconImageUrl: null,
            title: 'Pelayanan Ramah',
        },
    ],
};

const defaultServices = [
    {
        icon: 'fas fa-prescription-bottle-alt',
        iconImageUrl: null,
        title: 'Layanan Resep',
        description: 'Kami menyediakan layanan resep dokter dengan standar tinggi dan penuh ketelitian. Apoteker profesional kami akan memastikan setiap resep diproses dengan tepat dan aman, disertai dengan konsultasi mengenai penggunaan obat yang benar.',
        items: [
            'Pelayanan resep dokter cepat dan akurat',
            'Konsultasi penggunaan obat dengan apoteker',
            'Pemeriksaan interaksi obat',
            'Informasi efek samping dan cara penggunaan',
        ],
        imageClass: 'service-image-resep',
        reverse: false,
    },
    {
        icon: 'fas fa-notes-medical',
        iconImageUrl: null,
        title: 'Konsultasi Kesehatan',
        description: 'Dapatkan konsultasi kesehatan gratis dengan apoteker berpengalaman kami. Kami siap membantu Anda dengan berbagai pertanyaan seputar kesehatan dan penggunaan obat yang tepat.',
        items: [
            'Konsultasi gratis dengan apoteker',
            'Informasi penggunaan obat yang aman',
            'Pemeriksaan kesehatan dasar',
            'Edukasi kesehatan',
        ],
        imageClass: 'service-image-konsultasi',
        reverse: true,
    },
    {
        icon: 'fas fa-heartbeat',
        iconImageUrl: null,
        title: 'Pemeriksaan Kesehatan',
        description: 'Kami menyediakan layanan pemeriksaan kesehatan dasar untuk membantu Anda memantau kondisi kesehatan secara rutin. Dengan peralatan modern dan tenaga terlatih, kami siap memberikan pelayanan terbaik.',
        items: [
            'Cek tekanan darah',
            'Pemeriksaan gula darah',
            'Pemeriksaan Kolestrol dan Asam Urat',
            'Konsultasi hasil pemeriksaan',
        ],
        imageClass: 'service-image-pemeriksaan',
        reverse: false,
    },
];

const homeContent = computed(() => props.homeContent ?? {});

const hero = computed(() => {
    const heroContent = homeContent.value.hero ?? {};

    return {
        title: heroContent.title ?? defaultHero.title,
        subtitlePrimary:
            heroContent.subtitle_primary ??
            heroContent.subtitlePrimary ??
            defaultHero.subtitlePrimary,
        subtitleSecondary:
            heroContent.subtitle_secondary ??
            heroContent.subtitleSecondary ??
            defaultHero.subtitleSecondary,
        backgroundImageUrl:
            heroContent.background_image_url ??
            heroContent.backgroundImageUrl ??
            null,
    };
});

const heroBackgroundStyle = computed(() => {
    const url = hero.value.backgroundImageUrl;
    return url ? { backgroundImage: `url(${url})` } : {};
});

const featureHighlights = computed(() => {
    const highlights = homeContent.value.featureHighlights ?? [];
    if (Array.isArray(highlights) && highlights.length) {
        return highlights.map((highlight) => {
            const iconImageUrl =
                highlight.icon_image_url ??
                highlight.iconImageUrl ??
                null;
            const iconClass = highlight.icon ?? null;

            return {
                title: highlight.title ?? '',
                description: highlight.description ?? '',
                icon: iconClass || (iconImageUrl ? '' : 'fas fa-circle'),
                iconImageUrl,
            };
        });
    }

    return defaultFeatureHighlights.map((feature) => ({ ...feature }));
});

const about = computed(() => {
    const aboutContent = homeContent.value.about ?? {};

    const features =
        Array.isArray(aboutContent.features) && aboutContent.features.length
            ? aboutContent.features.map((feature) => {
                  const iconImageUrl =
                      feature.icon_image_url ??
                      feature.iconImageUrl ??
                      null;
                  const iconClass = feature.icon ?? null;

                  return {
                      title: feature.title ?? '',
                      icon: iconClass || (iconImageUrl ? '' : 'fas fa-circle'),
                      iconImageUrl,
                  };
              })
            : defaultAbout.features.map((feature) => ({ ...feature }));

    return {
        title: aboutContent.title ?? defaultAbout.title,
        description: aboutContent.description ?? defaultAbout.description,
        imageUrl:
            aboutContent.image_url ??
            aboutContent.imageUrl ??
            null,
        features,
    };
});

const aboutImageStyle = computed(() => {
    const url = about.value.imageUrl;
    return url ? { backgroundImage: `url(${url})` } : {};
});

const services = computed(() => {
    const source =
        Array.isArray(homeContent.value.services) && homeContent.value.services.length
            ? homeContent.value.services
            : defaultServices;

    return source.map((service, index) => {
        const items = Array.isArray(service.items) ? service.items : [];
        const iconImageUrl =
            service.icon_image_url ??
            service.iconImageUrl ??
            null;
        const iconClass = service.icon ?? '';

        return {
            title: service.title ?? '',
            icon: iconClass || (iconImageUrl ? '' : 'fas fa-circle'),
            iconImageUrl,
            description: service.description ?? '',
            items,
            imageUrl:
                service.image_url ??
                service.imageUrl ??
                null,
            imageClass: service.image_class ?? service.imageClass ?? '',
            reverse: Object.prototype.hasOwnProperty.call(service, 'reverse')
                ? Boolean(service.reverse)
                : index % 2 === 1,
        };
    });
});

const partnerLogos = computed(() => {
    const logos = homeContent.value.partnerLogos ?? homeContent.value.logos ?? [];
    if (!Array.isArray(logos)) {
        return [];
    }

    return logos
        .map((logo) => ({
            name: logo?.name ?? '',
            src: logo?.src ?? null,
        }))
        .filter((logo) => logo.name || logo.src);
});

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

const setServiceRow1 = (component) => {
    if (!component) {
        serviceRow1.value = null;
        return;
    }

    serviceRow1.value = component.root?.value ?? component.$el ?? component;
};

// Article highlight data
const articleImageFallback = null;
const articleExcerptFallback =
    'Antibiotik bukan untuk semua batuk-pilek. Pelajari indikasi, efek samping umum, dan mengapa harus dihabiskan sesuai resep.';
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
        serviceRow1: serviceRow1.value,
    });
});
</script>

<style lang="scss" scoped>
@import '../../css/home.scss';
</style>
