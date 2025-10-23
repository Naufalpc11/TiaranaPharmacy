<template>
  <MainLayout>
    <div class="article-detail-page">
      <template v-if="article">
        <section class="article-detail-hero" :style="heroStyle">
        <div class="article-detail-hero__overlay" ref="heroOverlay">
          <div class="article-detail-hero__content">
            <Link
              href="/artikel"
              class="article-detail-back"
              ref="heroBackButton"
            >
              <i class="fa-solid fa-arrow-left"></i>
              <span>Kembali</span>
            </Link>
            <h1 class="article-detail-title" ref="heroTitle">
              {{ article.title }}
            </h1>
            <p class="article-detail-date" ref="heroDate">
              {{ formattedDate }}
            </p>
          </div>
        </div>
      </section>

      <section class="article-detail-content">
        <article class="article-detail-card" ref="contentCard">
          <div v-if="article.excerpt" class="article-detail-intro">
            <p class="article-detail-paragraph">
              {{ article.excerpt }}
            </p>
          </div>

          <div
            v-if="bodyHtml"
            class="article-detail-body"
            v-html="bodyHtml"
          ></div>
        </article>
      </section>
      </template>
      <section v-else class="article-detail-missing">
        <div class="article-detail-missing__card">
          <h1>Artikel tidak ditemukan</h1>
          <p>
            Maaf, artikel yang Anda cari belum tersedia. Silakan kembali ke daftar artikel untuk membaca materi lainnya.
          </p>
          <Link href="/artikel" class="article-detail-back">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Artikel</span>
          </Link>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed, nextTick, onMounted, ref } from 'vue'
import MainLayout from '../Layouts/MainLayout.vue'
import { initializeArticleDetailAnimations } from '../animations/articleDetailAnimations'

const props = defineProps({
  article: {
    type: Object,
    default: null,
  },
})

const heroOverlay = ref(null)
const heroBackButton = ref(null)
const heroTitle = ref(null)
const heroDate = ref(null)
const contentCard = ref(null)

const fallbackHeroImage = new URL('../../images/HeroSection/Article.jpg', import.meta.url).href

const article = computed(() => {
  if (!props.article) {
    return null
  }

  return {
    ...props.article,
    cover_image: props.article.cover_image || fallbackHeroImage,
  }
})

const formattedDate = computed(() => {
  if (!article.value) {
    return ''
  }

  if (article.value.formatted_published_at) {
    return article.value.formatted_published_at
  }

  const source = article.value.published_at
  if (!source) {
    return ''
  }

  const date = new Date(source)
  if (Number.isNaN(date.getTime())) {
    return source
  }

  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  }).format(date)
})

const heroStyle = computed(() => ({
  '--hero-image': `url(${article.value?.cover_image || fallbackHeroImage})`,
}))

const bodyHtml = computed(() => article.value?.body ?? '')

onMounted(() => {
  if (!article.value) {
    return
  }

  nextTick(() => {
    initializeArticleDetailAnimations({
      heroOverlay: heroOverlay.value,
      heroTitle: heroTitle.value,
      heroDate: heroDate.value,
      heroBackButton: heroBackButton.value,
      contentCard: contentCard.value,
    })
  })
})
</script>

<style lang="scss" scoped>
@import '../../css/ArticleDetail.scss';
</style>
