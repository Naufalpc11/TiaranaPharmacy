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
          <template v-if="filteredArticles.length">
            <ArticleCard
              v-for="article in filteredArticles"
              :key="article.id"
              v-bind="article"
            />
          </template>
          <p v-else class="artikel-empty" role="status">
            tidak ada artikel terkait pencarian anda
          </p>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import ArticleCard from '../Components/ArticleCard.vue'
import MainLayout from '../Layouts/MainLayout.vue'
import { initializeArtikelAnimations } from '../animations/artikelAnimations'

const props = defineProps({
  articles: {
    type: Array,
    default: () => [],
  },
})

const fallbackImage = new URL('../../images/HeroSection/Article.jpg', import.meta.url).href

const articles = computed(() =>
  props.articles.map((article) => ({
    ...article,
    image: article.image || fallbackImage,
    imageAlt: article.imageAlt || article.title,
    date: article.date || formatDate(article.datetime),
    datetime: article.datetime || article.date,
    href: article.href || `/artikel/${article.slug}`,
  }))
)

const query = ref('')
const artikelHeroOverlay = ref(null)
const artikelHeroTitle = ref(null)
const artikelHeroSubtitle = ref(null)
const artikelSearchBar = ref(null)
const artikelGrid = ref(null)

const filteredArticles = computed(() => {
  const list = articles.value

  if (!query.value.trim()) {
    return list
  }

  const keyword = query.value.trim().toLowerCase()
  return list.filter((article) => {
    const title = (article.title || '').toLowerCase()
    const excerpt = (article.excerpt || '').toLowerCase()
    return title.includes(keyword) || excerpt.includes(keyword)
  })
})

function formatDate(input) {
  if (!input) {
    return ''
  }

  const date = new Date(input)
  if (Number.isNaN(date.getTime())) {
    return input
  }

  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(date)
}

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
