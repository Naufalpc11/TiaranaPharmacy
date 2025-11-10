<template>
  <MainLayout>
    <div class="about-page">
      <!-- HERO -->
      <header
        class="about-header"
        role="banner"
        aria-label="Hero Tentang Kami"
        :style="heroBackgroundStyle"
      >
        <div class="about-header-overlay">
          <h1 class="home-title">{{ hero.title }}</h1>
          <p v-if="hero.subtitle" class="home-subtitle">
            {{ hero.subtitle }}
          </p>

          <div class="hero-actions" v-if="hero.primaryButtonText || hero.secondaryButtonText">
            <template v-if="hero.primaryButtonText && hero.primaryButtonUrl">
              <Button
                v-if="primaryButtonIsAnchor"
                size="lg"
                icon-position="left"
                @click="handlePrimaryAction"
              >
                <template #icon><i class="fa-solid fa-location-dot" /></template>
                {{ hero.primaryButtonText }}
              </Button>
              <Button
                v-else
                :href="hero.primaryButtonUrl"
                size="lg"
                icon-position="left"
              >
                <template #icon><i class="fa-solid fa-location-dot" /></template>
                {{ hero.primaryButtonText }}
              </Button>
            </template>

            <template v-if="hero.secondaryButtonText && hero.secondaryButtonUrl">
              <Button
                v-if="secondaryButtonIsAnchor"
                variant="white"
                size="lg"
                icon-position="left"
                @click="handleSecondaryAction"
              >
                <template #icon><i class="fa-solid fa-comments" /></template>
                {{ hero.secondaryButtonText }}
              </Button>
              <Button
                v-else
                :href="hero.secondaryButtonUrl"
                variant="white"
                size="lg"
                icon-position="left"
              >
                <template #icon><i class="fa-solid fa-comments" /></template>
                {{ hero.secondaryButtonText }}
              </Button>
            </template>
          </div>
        </div>
      </header>

      <!-- VISI -->
      <section class="vision-section" aria-labelledby="visi">
        <div class="section-container">
          <h2 id="visi" class="section-title">{{ vision.title }}</h2>
          <p v-if="vision.text" class="vision-text">
            {{ vision.text }}
          </p>
        </div>
      </section>

      <!-- MISI -->
      <section class="values-section" aria-labelledby="misi">
        <div class="section-container">
          <h2 id="misi" class="section-title">{{ mission.title }}</h2>
          <div class="values-grid">
            <div
              v-for="(rowItems, rowIndex) in missionRows"
              :key="`mission-row-${rowIndex}`"
              class="values-grid__row"
              :class="`values-grid__row--${rowItems.length}`"
            >
              <MissionCard
                v-for="missionItem in rowItems"
                :key="`${missionItem.title}-${missionItem.description}`"
                :title="missionItem.title"
                :description="missionItem.description"
              />
            </div>
          </div>
        </div>
      </section>

      <!-- SEJARAH -->
        <section class="history-section" aria-labelledby="sejarah">
        <div class="section-container">
            <div class="history-layout">
            <div class="history-content">
                <h2 id="sejarah" class="section-title">{{ history.title }}</h2>
                <p v-for="(paragraph, index) in historyBodyParagraphs" :key="index">
                {{ paragraph }}
                </p>

                <div class="history-stats" aria-label="Statistik Singkat">
                <div class="stats-grid">
                    <HistoryStatCard
                    v-for="highlight in history.stats"
                    :key="highlight.label"
                    :icon="highlight.icon"
                    :icon-image-url="highlight.iconImageUrl"
                    :value="highlight.value"
                    :label="highlight.label"
                    />
                </div>
                </div>
            </div>

            <!-- WAJIB: tetap di dalam .history-layout sebagai kolom ke-2 -->
            <div
              class="history-media"
              role="img"
              aria-label="Etalase apotek"
              :style="historyImageStyle"
            ></div>
            </div>
        </div>
        </section>


      <section class="team-section" aria-labelledby="apoteker">
        <div class="section-container">
          <h2 id="apoteker" class="section-title">{{ team.title }}</h2>
          <p v-if="team.intro" class="section-intro">{{ team.intro }}</p>

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
                <div class="badges" v-if="pharmacist.badges.length">
                  <span class="badge" v-for="badge in pharmacist.badges" :key="badge">
                    <i class="fa-solid fa-circle-check" aria-hidden="true" />
                    {{ badge }}
                  </span>
                </div>
              </div>

              <div class="credentials" v-if="pharmacist.stra || pharmacist.sipa">
                <div class="credential" v-if="pharmacist.stra">
                  <span class="label">STRA</span>
                  <span class="value">{{ pharmacist.stra }}</span>
                </div>
                <div class="credential" v-if="pharmacist.sipa">
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
            <h2 id="lokasi-title" class="section-title">{{ location.title }}</h2>
            <p v-if="location.intro" class="section-intro">{{ location.intro }}</p>

            <div class="contact-items">
              <ContactInfoCard
                v-for="contact in locationContactDetails"
                :key="`${contact.title}-${contact.copyText}`"
                :icon="contact.icon"
                :icon-image-url="contact.iconImageUrl"
                :title="contact.title"
                :lines="contact.lines"
                :copy-text="contact.copyText"
                :copyable="contact.copyable"
                @copy="copyContact"
              />
            </div>
          </div>

          <div class="map-container">
            <iframe
              title="Peta lokasi Apotek Tiarana Farma"
              :src="location.mapEmbedUrl"
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
import { computed, onMounted } from 'vue'
import { initializeAboutUsAnimations } from '../animations/aboutUsAnimations'
import Button from '../Components/Button.vue'
import ContactInfoCard from '../Components/ContactInfoCard.vue'
import HistoryStatCard from '../Components/HistoryStatCard.vue'
import MissionCard from '../Components/MissionCard.vue'
import MainLayout from '../Layouts/MainLayout.vue'

const props = defineProps({
  aboutContent: {
    type: Object,
    default: () => ({})
  }
})

const asset = (path) => new URL(path, import.meta.url).href

const fallbackAssets = {
  heroBackground: asset('../../images/Interior.jpg'),
  historyImage: asset('../../images/WhatsApp Image 2024-07-29 at 20.05.38_c14c7704.jpg'),
  pharmacistPhoto: asset('../../images/HeroSection/AboutUs.jpg')
}

const defaultHero = {
  title: 'Tentang Kami',
  subtitle:
    'Apotek Tiarana Farma adalah apotek terpercaya yang menyediakan layanan kesehatan berkualitas dengan dukungan tim profesional dan fasilitas modern.',
  primaryButtonText: 'Lokasi',
  primaryButtonUrl: '#lokasi',
  secondaryButtonText: 'Hubungi Kami',
  secondaryButtonUrl: '/contact'
}

const defaultVision = {
  title: 'Visi',
  text:
    '"Menjadi apotek terpercaya berbasis komunitas yang mengutamakan pelayanan tatap muka yang aman, ramah, dan akurat bagi keluarga Indonesia."'
}

const defaultMissionItems = [
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

const defaultHistory = {
  title: 'Sejarah Kami',
  description:
    'Berdiri pada 2021 di Balikpapan, Tiarana Farma lahir dari misi sederhana: memudahkan akses obat yang aman dan terjangkau. Dari sebuah apotek kecil kami bertumbuh menjadi layanan farmasi modern yang mengedepankan konsultasi tatap muka, stok terkurasi, dan proses pembelian yang mudah. Hingga kini, kami telah melayani lebih dari 5.000 pelanggan dan terus berinovasi demi kesehatan keluarga Indonesia.',
  stats: [
    { icon: 'fa-regular fa-calendar-check', iconImageUrl: null, value: '2021', label: 'Mulai beroperasi' },
    { icon: 'fa-solid fa-people-group', iconImageUrl: null, value: '5.000+', label: 'Pelanggan dilayani' },
    { icon: 'fa-solid fa-pills', iconImageUrl: null, value: '200+', label: 'Produk tersedia' }
  ]
}

const defaultTeam = {
  title: 'Apoteker Kami',
  intro: 'Kenali apoteker penanggung jawab kami di balik layanan Tiarana Farma.',
  pharmacist: {
    name: 'apt. Titik Tresnowati, S. Si',
    role: 'Apoteker Penanggung Jawab',
    stra: '19880824/STRA-YYYY/2023',
    sipa: '19880824/SIPA-XX/2023',
    schedule: 'Senin-Sabtu, 17.00-22.00 WITA',
    badges: ['STRA & SIPA terverifikasi', 'On-the-job pengalaman'],
    photoUrl: null,
    photoAlt: 'Foto Apoteker Penanggung Jawab'
  }
}

const defaultLocation = {
  title: 'Lokasi Kami',
  intro: null,
  mapEmbedUrl:
    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8714503220267!2d116.9010663745559!3d-1.2482881355849063!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df145cac72eb3bf%3A0x16844957779a9566!2sApotek%20Tiarana%20Farma!5e0!3m2!1sid!2sid!4v1759721919148!5m2!1sid!2sid',
  contactDetails: [
    {
      icon: 'fa-solid fa-map-location-dot',
      iconImageUrl: null,
      title: 'Alamat',
      lines: [
        'Gg. 21, Jl. Sepinggan Baru RT.18/RW.10 No.12',
        'Sepinggan, Balikpapan, Kalimantan Timur 76116'
      ],
      copyText: 'Gg. 21, Jl. Sepinggan Baru RT.18/RW.10 No.12, Sepinggan, Balikpapan, Kalimantan Timur 76116'
    },
    {
      icon: 'fa-solid fa-phone',
      iconImageUrl: null,
      title: 'Telepon',
      lines: ['0821-2000-3948'],
      copyText: '0821-2000-3948'
    },
    {
      icon: 'fa-solid fa-clock',
      iconImageUrl: null,
      title: 'Jam Operasional',
      lines: ['Senin-Sabtu: 08.00-21.00 WITA', 'Minggu: 09.00-20.00 WITA'],
      copyText: '',
      copyable: false
    }
  ]
}

const hero = computed(() => {
  const data = props.aboutContent?.hero ?? {}
  return {
    title: data.title ?? defaultHero.title,
    subtitle: data.subtitle ?? defaultHero.subtitle,
    backgroundImageUrl: data.backgroundImageUrl ?? null,
    primaryButtonText: data.primaryButtonText ?? defaultHero.primaryButtonText,
    primaryButtonUrl: data.primaryButtonUrl ?? defaultHero.primaryButtonUrl,
    secondaryButtonText: data.secondaryButtonText ?? defaultHero.secondaryButtonText,
    secondaryButtonUrl: data.secondaryButtonUrl ?? defaultHero.secondaryButtonUrl
  }
})

const heroBackgroundStyle = computed(() => {
  const image = hero.value.backgroundImageUrl || fallbackAssets.heroBackground
  return {
    '--hero-bg-url': `url("${image}")`
  }
})

const primaryButtonIsAnchor = computed(
  () => Boolean(hero.value.primaryButtonUrl && hero.value.primaryButtonUrl.startsWith('#'))
)

const secondaryButtonIsAnchor = computed(
  () => Boolean(hero.value.secondaryButtonUrl && hero.value.secondaryButtonUrl.startsWith('#'))
)

const vision = computed(() => {
  const data = props.aboutContent?.vision ?? {}
  return {
    title: data.title ?? defaultVision.title,
    text: data.text ?? defaultVision.text
  }
})

const mission = computed(() => {
  const data = props.aboutContent?.mission ?? {}
  const items = Array.isArray(data.items) ? data.items : defaultMissionItems
  return {
    title: data.title ?? 'Misi',
    items
  }
})

const missionRows = computed(() => {
  const items = mission.value.items ?? []
  const total = items.length

  if (!total) {
    return []
  }

  if (total === 4) {
    return [items.slice(0, 2), items.slice(2, 4)]
  }

  if (total === 5) {
    return [items.slice(0, 3), items.slice(3, 5)]
  }

  if (total === 7) {
    return [items.slice(0, 3), items.slice(3, 6), items.slice(6, 7)]
  }

  const rows = []
  for (let index = 0; index < total; index += 3) {
    rows.push(items.slice(index, index + 3))
  }

  return rows
})

const history = computed(() => {
  const data = props.aboutContent?.history ?? {}
  const statsSource =
    Array.isArray(data.stats) && data.stats.length ? data.stats : defaultHistory.stats
  const stats = statsSource.map((stat, index) => ({
    icon: stat.icon ?? '',
    iconImageUrl: stat.iconImageUrl ?? stat.icon_image_url ?? '',
    value: stat.value ?? '',
    label: stat.label ?? `Statistik ${index + 1}`
  }))
  return {
    title: data.title ?? defaultHistory.title,
    description: data.description ?? defaultHistory.description,
    imageUrl: data.imageUrl ?? null,
    stats
  }
})

const historyParagraphs = computed(() =>
  history.value.description
    ? history.value.description.split(/\r?\n/).map((paragraph) => paragraph.trim()).filter(Boolean)
    : []
)

const historyBodyParagraphs = computed(() => {
  if (historyParagraphs.value.length) {
    return historyParagraphs.value
  }
  const fallback = history.value.description?.trim()
  return fallback ? [fallback] : []
})

const historyImageStyle = computed(() => {
  const image = history.value.imageUrl || fallbackAssets.historyImage
  return {
    '--history-image-url': `url("${image}")`
  }
})

const team = computed(() => {
  const data = props.aboutContent?.team ?? {}
  const pharmacistData = data.pharmacist ?? {}
  const badges = Array.isArray(pharmacistData.badges)
    ? pharmacistData.badges.filter(Boolean)
    : defaultTeam.pharmacist.badges

  return {
    title: data.title ?? defaultTeam.title,
    intro: data.intro ?? defaultTeam.intro,
    pharmacist: {
      name: pharmacistData.name ?? defaultTeam.pharmacist.name,
      role: pharmacistData.role ?? defaultTeam.pharmacist.role,
      stra: pharmacistData.stra ?? defaultTeam.pharmacist.stra,
      sipa: pharmacistData.sipa ?? defaultTeam.pharmacist.sipa,
      schedule: pharmacistData.schedule ?? defaultTeam.pharmacist.schedule,
      badges,
      photo: pharmacistData.photoUrl || fallbackAssets.pharmacistPhoto,
      alt: pharmacistData.photoAlt ?? defaultTeam.pharmacist.photoAlt
    }
  }
})

const pharmacist = computed(() => team.value.pharmacist)

const location = computed(() => {
  const data = props.aboutContent?.location ?? {}
  const contactSource =
    Array.isArray(data.contactDetails) && data.contactDetails.length
      ? data.contactDetails
      : defaultLocation.contactDetails
  const contactDetails = contactSource.map((detail, index) => {
    const title = detail.title ?? `Kontak ${index + 1}`
    const lines = Array.isArray(detail.lines) ? detail.lines : []
    const titleSuggestsSchedule = title.toLowerCase().includes('operasional')
    const hasExplicitCopyable = Object.prototype.hasOwnProperty.call(detail, 'copyable')
    const copyable = hasExplicitCopyable ? Boolean(detail.copyable) : !titleSuggestsSchedule

    return {
      icon: detail.icon ?? '',
      iconImageUrl: detail.iconImageUrl ?? detail.icon_image_url ?? '',
      title,
      lines,
      copyText: copyable ? detail.copyText ?? '' : '',
      copyable
    }
  })

  return {
    title: data.title ?? defaultLocation.title,
    intro: data.intro ?? defaultLocation.intro,
    mapEmbedUrl: data.mapEmbedUrl ?? defaultLocation.mapEmbedUrl,
    contactDetails
  }
})

const locationContactDetails = computed(() => location.value.contactDetails)

const handlePrimaryAction = () => {
  if (!primaryButtonIsAnchor.value) {
    return
  }
  scrollTo(hero.value.primaryButtonUrl)
}

const handleSecondaryAction = () => {
  if (!secondaryButtonIsAnchor.value) {
    return
  }
  scrollTo(hero.value.secondaryButtonUrl)
}

const onImgError = (event) => {
  event.target.src = fallbackAssets.pharmacistPhoto
  event.target.style.opacity = '1'
  event.target.parentElement?.classList.remove('no-image')
}

const scrollTo = (selector) => {
  if (!selector) return
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
