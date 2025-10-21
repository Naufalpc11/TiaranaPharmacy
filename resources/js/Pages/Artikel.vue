<template>
  <MainLayout>
    <div class="artikel-page">
      <header class="artikel-hero" role="banner">
        <div class="artikel-hero__overlay" ref="artikelHeroOverlay">
          <h1 class="home-title" ref="artikelHeroTitle">Artikel</h1>
          <p class="home-subtitle" ref="artikelHeroSubtitle">
            Edukasi farmasi, tips kesehatan, info obat, pengumuman, dan promo terbaru dari Tiarana Farma.
          </p>
          <label class="artikel-search" aria-label="Cari artikel" ref="artikelSearchBar">
            <i class="fa-solid fa-magnifying-glass artikel-search__icon" aria-hidden="true"></i>
            <input
              type="search"
              placeholder="Cari artikel..."
              autocomplete="off"
              class="artikel-search__input"
              v-model="query"
            />
          </label>
        </div>
      </header>

      <section class="artikel-section" aria-label="Daftar Artikel">
        <div class="artikel-grid" ref="artikelGrid">
          <ArticleCard
            v-for="article in filteredArticles"
            :key="article.id"
            v-bind="article"
          />
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, reactive, ref, onMounted } from 'vue'
import ArticleCard from '../Components/ArticleCard.vue'
import MainLayout from '../Layouts/MainLayout.vue'
import { initializeArtikelAnimations } from '../animations/artikelAnimations'

const articles = reactive([
  {
    id: 1,
    title: 'Panduan Swamedikasi yang Aman',
    excerpt:
      '5 langkah sederhana agar penggunaan obat bebas tetap aman: baca etiket, dosis tepat, cek interaksi, batasi durasi, dan konsultasi bila gejala tak membaik.',
    date: '11/08/2025',
    datetime: '2025-08-11',
    image: '/images/articles/swamedikasi.jpg',
    imageAlt: 'Seseorang menuang obat tablet ke tangan',
  },
  {
    id: 2,
    title: 'Cara Menyimpan Obat yang Benar di Iklim Tropis',
    excerpt:
      'Panas dan lembap bisa merusak obat. Simpan pada suhu yang dianjurkan, hindari kamar mandi/dapur, dan gunakan kotak obat tertutup.',
    date: '13/08/2025',
    datetime: '2025-08-13',
    image: '/images/WhatsApp Image 2024-07-29 at 20.05.38_c14c7704.jpg',
    imageAlt: 'Rak apotek dengan berbagai produk obat',
  },
  {
    id: 3,
    title: 'Amoksisilin: Kapan Perlu, Kapan Tidak',
    excerpt:
      'Antibiotik bukan untuk semua batuk-pilek. Pelajari indikasi, efek samping umum, dan mengapa harus dihabiskan sesuai resep.',
    date: '12/08/2025',
    datetime: '2025-08-12',
    image: '/images/articles/amoksisilin.jpg',
    imageAlt: 'Strip kapsul antibiotik amoksisilin',
    href: '/artikel/amoksisilin-kapan-perlu-kapan-tidak',
  },
])

const query = ref('')
const artikelHeroOverlay = ref(null)
const artikelHeroTitle = ref(null)
const artikelHeroSubtitle = ref(null)
const artikelSearchBar = ref(null)
const artikelGrid = ref(null)

const filteredArticles = computed(() => {
  if (!query.value.trim()) {
    return articles
  }

  const keyword = query.value.trim().toLowerCase()
  return articles.filter(
    (article) =>
      article.title.toLowerCase().includes(keyword) ||
      article.excerpt.toLowerCase().includes(keyword)
  )
})

onMounted(() => {
  initializeArtikelAnimations({
    heroOverlay: artikelHeroOverlay.value,
    heroTitle: artikelHeroTitle.value,
    heroSubtitle: artikelHeroSubtitle.value,
    searchBar: artikelSearchBar.value,
    artikelGrid: artikelGrid.value,
  })
})
</script>

<style lang="scss" scoped>
@import '../../css/Artikel.scss';
</style>
