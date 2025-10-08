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
        <p>Buka setiap hari dari pukul 08:00 - 21:00 WIB</p>
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
          <img src="/images/pharmacy-interior.jpg" alt="Interior Apotek Tiarana Farma">
        </div>
      </div>
    </section>

    <section class="services-section" ref="servicesSection">
      <h2 ref="servicesTitle">Layanan Kami</h2>
      <div class="services-zigzag" ref="servicesContainer">
        <div class="service-row service-card" ref="serviceRow1">
          <div class="service-image">
            <img src="/images/services/prescription.jpg" alt="Layanan Resep">
          </div>
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
          <div class="service-image">
            <img src="/images/services/consultation.jpg" alt="Konsultasi Kesehatan">
          </div>
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
          <div class="service-image">
            <img src="/images/services/health-check.jpg" alt="Pemeriksaan Kesehatan">
          </div>
          <div class="service-content">
            <div class="service-icon">
              <i class="fas fa-heartbeat"></i>
            </div>
            <h3>Pemeriksaan Kesehatan</h3>
            <p>Kami menyediakan layanan pemeriksaan kesehatan dasar untuk membantu Anda memantau kondisi kesehatan secara rutin. Dengan peralatan modern dan tenaga terlatih, kami siap memberikan pelayanan terbaik.</p>
            <ul>
              <li>Cek tekanan darah</li>
              <li>Pemeriksaan gula darah</li>
              <li>Pengukuran berat dan tinggi badan</li>
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
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { onMounted, ref } from 'vue';
import MainLayout from '../Layouts/MainLayout.vue';

gsap.registerPlugin(ScrollTrigger);

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
  // Set initial states and animations
  // Hero section animation with popup effect
  gsap.set(heroContent.value, {
    autoAlpha: 0,
    scale: 0.9
  });

  gsap.set([heroTitle.value, heroSubtitle1.value, heroSubtitle2.value], {
    autoAlpha: 0,
    y: 30
  });

  const heroTimeline = gsap.timeline({
    defaults: {
      ease: 'power3.out'
    }
  });

  heroTimeline
    .to(heroContent.value, {
      autoAlpha: 1,
      scale: 1,
      duration: 1,
      ease: 'power2.out'
    })
    .to(heroTitle.value, {
      autoAlpha: 1,
      y: 0,
      duration: 0.8
    }, '-=0.3')
    .to(heroSubtitle1.value, {
      autoAlpha: 1,
      y: 0,
      duration: 0.6
    }, '-=0.4')
    .to(heroSubtitle2.value, {
      autoAlpha: 1,
      y: 0,
      duration: 0.6
    }, '-=0.4');

  // Features grid animation
  gsap.set(featuresGrid.value.children, {
    autoAlpha: 0,
    y: 50
  });

  gsap.to(featuresGrid.value.children, {
    scrollTrigger: {
      trigger: featuresGrid.value,
      start: 'top 90%',
      end: 'top 60%',
      toggleActions: 'play reverse play reverse',
      scrub: 0.5
    },
    y: 0,
    autoAlpha: 1,
    duration: 0.5,
    stagger: 0.1
  });

  // About section animations
  gsap.set(aboutText.value, {
    autoAlpha: 0,
    x: -50
  });

  gsap.set(aboutImage.value, {
    autoAlpha: 0,
    x: 50
  });

  gsap.to(aboutText.value, {
    scrollTrigger: {
      trigger: aboutSection.value,
      start: 'top 85%',
      end: 'top 50%',
      toggleActions: 'play reverse play reverse',
      scrub: 0.5
    },
    x: 0,
    autoAlpha: 1,
    duration: 0.5
  });

  gsap.to(aboutImage.value, {
    scrollTrigger: {
      trigger: aboutSection.value,
      start: 'top 85%',
      end: 'top 50%',
      toggleActions: 'play reverse play reverse',
      scrub: 0.5
    },
    x: 0,
    autoAlpha: 1,
    duration: 0.5
  });

  // About features animation
  gsap.set(aboutFeatures.value.children, {
    autoAlpha: 0,
    y: 30
  });

  gsap.to(aboutFeatures.value.children, {
    scrollTrigger: {
      trigger: aboutFeatures.value,
      start: 'top 85%',
      end: 'top 50%',
      toggleActions: 'play reverse play reverse',
      scrub: 0.5
    },
    y: 0,
    autoAlpha: 1,
    duration: 0.4,
    stagger: {
      each: 0.1,
      from: "start"
    }
  });

  // Services section animations
  gsap.set(servicesTitle.value, {
    y: 30,
    opacity: 0
  });

  gsap.to(servicesTitle.value, {
    scrollTrigger: {
      trigger: servicesSection.value,
      start: 'top 80%',
      end: 'top 20%',
      toggleActions: 'play reverse play reverse',
      scrub: 1
    },
    y: 0,
    opacity: 1,
    duration: 0.8
  });

  // Animate each service card
  const serviceCards = document.querySelectorAll('.service-card');
  serviceCards.forEach((card, index) => {
    const direction = index % 2 === 0 ? -1 : 1;

    // Set initial state
    gsap.set(card, {
      x: 100 * direction,
      opacity: 0,
      scale: 0.8
    });

    // Create scroll trigger animation
    gsap.to(card, {
      scrollTrigger: {
        trigger: card,
        start: 'top 80%',
        end: 'top 20%',
        scrub: 1,
        toggleActions: 'play reverse play reverse'
      },
      x: 0,
      opacity: 1,
      scale: 1,
      duration: 1.5,
      ease: 'power2.out'
    });
  });
});
</script>

<style lang="scss" scoped>
@import '../../css/home.scss';

.service-card {
  transform-origin: center;
  will-change: transform, opacity;
  background: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: box-shadow 0.3s ease;

  &:hover {
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
  }

  .service-image {
    overflow: hidden;
    img {
      transition: transform 0.3s ease;
    }
    &:hover img {
      transform: scale(1.05);
    }
  }
}

.services-zigzag {
  perspective: 1000px;
  .service-row {
    margin-bottom: 3rem;
    &:last-child {
      margin-bottom: 0;
    }
  }
}
</style>
