<template>
  <MainLayout>
    <div class="artikel-page">
      <header class="artikel-hero" role="banner">
        <div class="artikel-hero__overlay" ref="artikelHeroOverlay">
          <h1 class="home-title" ref="artikelHeroTitle">Artikel</h1>
          <p class="home-subtitle" ref="artikelHeroSubtitle">
            Edukasi farmasi, tips kesehatan, info obat, pengumuman, dan promo terbaru dari Tiarana Farma.
          </p>
          <div class="artikel-controls" ref="artikelSearchBar">
            <label class="artikel-search" aria-label="Cari artikel">
              <i class="fa-solid fa-magnifying-glass artikel-search__icon" aria-hidden="true"></i>
              <input
                type="search"
                placeholder="Cari artikel..."
                autocomplete="off"
                class="artikel-search__input"
                v-model="query"
              />
            </label>

            <div class="artikel-sort">
              <label for="artikel-sort-select" class="artikel-sort__label">Urutkan</label>
              <select
                id="artikel-sort-select"
                class="artikel-sort__select"
                v-model="sortOption"
              >
                <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </header>

      <section class="artikel-section" aria-label="Daftar Artikel">
        <div class="artikel-grid" ref="artikelGrid">
          <template v-if="filteredArticles.length">
            <ArticleCard
              v-for="article in visibleArticles"
              :key="article.id"
              v-bind="article"
            />
          </template>
          <p v-else class="artikel-empty" role="status">
            tidak ada artikel terkait pencarian anda
          </p>
        </div>

        <div
          v-if="filteredArticles.length"
          class="artikel-load-more"
          ref="loadMoreSentinel"
          aria-live="polite"
        >
          <span v-if="hasMoreArticles">Gulir untuk menampilkan artikel berikutnya...</span>
          <span v-else>Anda sudah mencapai akhir daftar artikel.</span>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
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

const sortOptions = Object.freeze([
  { value: 'newest', label: 'Terbaru' },
  { value: 'oldest', label: 'Terlama' },
  { value: 'az', label: 'Judul A-Z' },
])

const LOAD_STEP = 6

const query = ref('')
const sortOption = ref(sortOptions[0].value)
const artikelHeroOverlay = ref(null)
const artikelHeroTitle = ref(null)
const artikelHeroSubtitle = ref(null)
const artikelSearchBar = ref(null)
const artikelGrid = ref(null)
const loadMoreSentinel = ref(null)
const visibleCount = ref(LOAD_STEP)

let loadObserver

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

const sortedArticles = computed(() => {
  const list = [...filteredArticles.value]

  if (sortOption.value === 'az') {
    return list.sort((a, b) => a.title.localeCompare(b.title, 'id-ID'))
  }

  if (sortOption.value === 'oldest') {
    return list.sort((a, b) => compareByDate(a, b))
  }

  return list.sort((a, b) => compareByDate(b, a))
})

const clampedVisibleCount = computed(() => Math.min(visibleCount.value, sortedArticles.value.length))
const visibleArticles = computed(() => sortedArticles.value.slice(0, clampedVisibleCount.value))
const hasMoreArticles = computed(() => clampedVisibleCount.value < sortedArticles.value.length)

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

function compareByDate(articleA, articleB) {
  const aTime = getTimeValue(articleA.datetime || articleA.date)
  const bTime = getTimeValue(articleB.datetime || articleB.date)
  return (aTime ?? 0) - (bTime ?? 0)
}

function getTimeValue(source) {
  if (!source) {
    return null
  }

  const date = new Date(source)
  return Number.isNaN(date.getTime()) ? null : date.getTime()
}

const increaseVisibleArticles = () => {
  if (!hasMoreArticles.value) {
    return
  }

  visibleCount.value += LOAD_STEP
}

const setupObserver = () => {
  if (!loadMoreSentinel.value || !hasMoreArticles.value) {
    return
  }

  if (loadObserver) {
    loadObserver.disconnect()
  }

  loadObserver = new IntersectionObserver(
    (entries) => {
      if (entries.some((entry) => entry.isIntersecting)) {
        increaseVisibleArticles()
      }
    },
    { rootMargin: '0px 0px 220px 0px' }
  )

  loadObserver.observe(loadMoreSentinel.value)
}

const resetInfiniteScroll = () => {
  visibleCount.value = LOAD_STEP
  if (loadObserver) {
    loadObserver.disconnect()
  }
  requestAnimationFrame(setupObserver)
}

watch(
  () => query.value,
  () => {
    resetInfiniteScroll()
  }
)

watch(
  () => sortOption.value,
  () => {
    resetInfiniteScroll()
  }
)

watch(
  () => filteredArticles.value.length,
  () => {
    visibleCount.value = LOAD_STEP
  }
)

watch(
  () => loadMoreSentinel.value,
  () => {
    setupObserver()
  }
)

watch(
  () => hasMoreArticles.value,
  (hasMore) => {
    if (!hasMore && loadObserver) {
      loadObserver.disconnect()
    } else if (hasMore) {
      setupObserver()
    }
  }
)

onMounted(() => {
  initializeArtikelAnimations({
    heroOverlay: artikelHeroOverlay.value,
    heroTitle: artikelHeroTitle.value,
    heroSubtitle: artikelHeroSubtitle.value,
    searchBar: artikelSearchBar.value,
    artikelGrid: artikelGrid.value,
  })

  setupObserver()
})

onBeforeUnmount(() => {
  if (loadObserver) {
    loadObserver.disconnect()
  }
})
</script>

<style lang="scss" scoped>
@import '../../css/Artikel.scss';
</style>
