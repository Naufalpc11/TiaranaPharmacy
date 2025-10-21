<template>
  <MainLayout>
    <div class="about-page">
      <!-- HERO -->
      <header class="about-header" role="banner" aria-label="Hero Tentang Kami">
        <div class="about-header-overlay">
          <h1 class="home-title">Tentang Kami</h1>
          <p class="home-subtitle">
            Apotek Tiarana Farma adalah apotek terpercaya yang menyediakan layanan kesehatan berkualitas
            dengan dukungan tim profesional dan fasilitas modern.
          </p>

          <div class="hero-actions">
            <Button size="lg" icon-position="left" @click="scrollTo('#lokasi')">
              <template #icon><i class="fa-solid fa-location-dot" /></template>
              Lokasi
            </Button>
            <Button href="/contact" variant="white" size="lg" icon-position="left">
              <template #icon><i class="fa-solid fa-comments" /></template>
              Hubungi Kami
            </Button>
          </div>
        </div>
      </header>

      <!-- VISI -->
      <section class="vision-section" aria-labelledby="visi">
        <div class="section-container">
          <h2 id="visi" class="section-title">Visi</h2>
          <p class="vision-text">
            "Menjadi apotek terpercaya berbasis komunitas yang mengutamakan pelayanan tatap muka yang aman,
            ramah, dan akurat bagi keluarga Indonesia."
          </p>
        </div>
      </section>

      <!-- MISI -->
      <section class="values-section" aria-labelledby="misi">
        <div class="section-container">
          <h2 id="misi" class="section-title">Misi</h2>
          <div class="values-grid">
            <MissionCard
              v-for="mission in missionItems"
              :key="mission.title"
              :title="mission.title"
              :description="mission.description"
            />
          </div>
        </div>
      </section>

      <!-- SEJARAH -->
      <section class="history-section" aria-labelledby="sejarah">
        <div class="section-container history-layout">
          <div class="history-content">
            <h2 id="sejarah" class="section-title">Sejarah Kami</h2>
            <p>
              Berdiri pada 2021 di Balikpapan, Tiarana Farma lahir dari misi sederhana: memudahkan akses obat
              yang aman dan terjangkau. Dari sebuah apotek kecil kami bertumbuh menjadi layanan farmasi modern
              yang mengedepankan konsultasi tatap muka, stok terkurasi, dan proses pembelian yang mudah.
              Hingga kini, kami telah melayani lebih dari 5.000 pelanggan dan terus berinovasi demi kesehatan
              keluarga Indonesia.
            </p>

            <div class="history-stats" aria-label="Statistik Singkat">
              <div class="stats-grid">
                <HistoryStatCard
                  v-for="highlight in historyStats"
                  :key="highlight.label"
                  :icon="highlight.icon"
                  :value="highlight.value"
                  :label="highlight.label"
                />
            </div>
          </div>
        </div>
            <div
                class="history-media"
                role="img">
            </div>
        </div>
      </section>

      <section class="team-section" aria-labelledby="apoteker">
        <div class="section-container">
          <h2 id="apoteker" class="section-title">Apoteker Kami</h2>
          <p class="section-intro">Kenali apoteker penanggung jawab kami di balik layanan Tiarana Farma.</p>

          <div class="pharmacist-card">
            <div class="photo">
              <div class="img-container">
                <img
                  :src="pharmacist.photo"
                  :alt="pharmacist.alt"
                  loading="lazy"
                  @error="onImgError($event)"
                />
              </div>
            </div>

            <div class="info">
              <div class="identity">
                <h3>{{ pharmacist.name }}</h3>
                <p class="role" v-if="pharmacist.role">{{ pharmacist.role }}</p>
                <div class="badges">
                  <span class="badge" v-for="badge in pharmacist.badges" :key="badge">
                    <i class="fa-solid fa-circle-check" aria-hidden="true" />
                    {{ badge }}
                  </span>
                </div>
              </div>

              <div class="credentials">
                <div class="credential">
                  <span class="label">STRA</span>
                  <span class="value">{{ pharmacist.stra }}</span>
                </div>
                <div class="credential">
                  <span class="label">SIPA</span>
                  <span class="value">{{ pharmacist.sipa }}</span>
                </div>
              </div>

              <div class="schedule" v-if="pharmacist.schedule">
                <span class="label">Jadwal</span>
                <span class="value">{{ pharmacist.schedule }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- LOKASI -->
      <section class="location-section" id="lokasi" aria-labelledby="lokasi-title">
        <div class="section-container location-layout">
          <div class="location-info">
            <h2 id="lokasi-title" class="section-title">Lokasi Kami</h2>

            <div class="contact-items">
              <article v-for="contact in contactDetails" :key="contact.title" class="contact-item">
                <div class="icon"><i :class="contact.icon" aria-hidden="true" /></div>
                <div class="content">
                  <h3>{{ contact.title }}</h3>
                  <p v-for="line in contact.lines" :key="line">{{ line }}</p>
                </div>
                <button
                  v-if="contact.copyText"
                  type="button"
                  class="copy-btn"
                  :aria-label="`Salin ${contact.title}`"
                  @click="copyContact(contact.copyText)"
                >
                  <i class="fa-regular fa-copy" aria-hidden="true" />
                </button>
              </article>
            </div>
          </div>

          <div class="map-container">
            <iframe
              title="Peta lokasi Apotek Tiarana Farma"
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8714503220267!2d116.9010663745559!3d-1.2482881355849063!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df145cac72eb3bf%3A0x16844957779a9566!2sApotek%20Tiarana%20Farma!5e0!3m2!1sid!2sid!4v1759721919148!5m2!1sid!2sid"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            />
          </div>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { initializeAboutUsAnimations } from '../animations/aboutUsAnimations'
import Button from '../Components/Button.vue'
import MissionCard from '../Components/MissionCard.vue'
import HistoryStatCard from '../Components/HistoryStatCard.vue'
import MainLayout from '../Layouts/MainLayout.vue'

const missionItems = [
  {
    title: 'Pelayanan tatap muka yang ramah',
    description:
      'Memprioritaskan konsultasi langsung dengan apoteker, memastikan kebutuhan pasien, dan pendampingan penggunaan obat di lokasi apotek.'
  },
  {
    title: 'Mutu, keaslian, dan kepatuhan',
    description:
      'Mengelola obat sesuai standar praktik kefarmasian dan regulasi BPOM untuk menjamin keamanan pasien.'
  },
  {
    title: 'Pengadaan obat melalui PBF resmi',
    description:
      'Seluruh pengadaan dilakukan dari Pedagang Besar Farmasi (PBF) resmi dengan kepatuhan CDOB.'
  }
]

const historyStats = [
  { icon: 'fa-regular fa-calendar-check', value: '2021', label: 'Mulai beroperasi' },
  { icon: 'fa-solid fa-people-group', value: '5.000+', label: 'Pelanggan dilayani' },
  { icon: 'fa-solid fa-pills', value: '200+', label: 'Produk tersedia' }
]

const contactDetails = [
  {
    icon: 'fa-solid fa-map-location-dot',
    title: 'Alamat',
    lines: [
      'Gg. 21, Jl. Sepinggan Baru RT.18/RW.10 No.12',
      'Sepinggan, Balikpapan, Kalimantan Timur 76116'
    ],
    copyText: 'Gg. 21, Jl. Sepinggan Baru RT.18/RW.10 No.12, Sepinggan, Balikpapan, Kalimantan Timur 76116'
  },
  {
    icon: 'fa-solid fa-phone',
    title: 'Telepon',
    lines: ['0821-2000-3948'],
    copyText: '0821-2000-3948'
  },
  {
    icon: 'fa-solid fa-clock',
    title: 'Jam Operasional',
    lines: ['Senin-Sabtu: 08.00-21.00 WITA', 'Minggu: 09.00-20.00 WITA'],
    copyText: 'Senin-Sabtu: 08.00-21.00 WITA\nMinggu: 09.00-20.00 WITA'
  }
]

const pharmacist = ref({
  name: 'apt. Titik Tresnowati, S. Si',
  role: 'Apoteker Penanggung Jawab',
  stra: '19880824/STRA-YYYY/2023',
  sipa: '19880824/SIPA-XX/2023',
  schedule: 'Senin-Sabtu, 17.00-22.00 WITA',
  badges: ['STRA & SIPA terverifikasi', 'On-the-job pengalaman'],
  photo: '/images/team/pharmacist1.jpg',
  alt: 'Foto Apoteker Penanggung Jawab'
})

const onImgError = (e) => {
  e.target.style.opacity = '0'
  e.target.parentElement.classList.add('no-image')
}

const scrollTo = (selector) => {
  const el = document.querySelector(selector)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const copyContact = async (text) => {
  if (!text) return

  const copyWithFallback = (value) => {
    const textarea = document.createElement('textarea')
    textarea.value = value
    textarea.setAttribute('readonly', '')
    textarea.style.position = 'absolute'
    textarea.style.left = '-9999px'
    document.body.appendChild(textarea)
    textarea.select()
    document.execCommand('copy')
    document.body.removeChild(textarea)
  }

  try {
    if (navigator?.clipboard?.writeText) {
      await navigator.clipboard.writeText(text)
    } else {
      copyWithFallback(text)
    }
  } catch (error) {
    console.warn('Gagal menyalin ke clipboard', error)
    copyWithFallback(text)
  }
}

onMounted(() => initializeAboutUsAnimations())
</script>

<style lang="scss" scoped>
@import '../../css/AboutUs.scss';
</style>
