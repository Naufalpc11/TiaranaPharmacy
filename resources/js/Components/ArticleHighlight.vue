<template>
  <section class="articles-highlight" :class="{ 'is-visible': isVisible }" ref="sectionRef">
    <div class="articles-grid">
      <article class="highlight-card">
        <transition-group name="highlight-image" tag="div" class="media-background-wrapper">
          <div
            v-for="layer in backgroundLayers"
            :key="layer.key"
            class="media-background"
            :style="layer.style"
          ></div>
        </transition-group>
        <div class="media-overlay"></div>
        <div class="content">
          <div class="card-date">{{ displayDate }}</div>

          <div class="text">
            <h3>{{ displayTitle }}</h3>
            <p>{{ displayExcerpt }}</p>
          </div>

          <div class="card-actions">
            <Button :href="primaryArticleHref" size="lg">
              Baca Artikel
            </Button>
            <div class="nav-controls" v-if="showNavControls">
              <CircleButton
                :disabled="isPrevDisabled"
                aria-label="Artikel sebelumnya"
                @click="goPrev"
              >
                <i class="fa-solid fa-arrow-left"></i>
              </CircleButton>
              <CircleButton
                :disabled="isNextDisabled"
                aria-label="Artikel selanjutnya"
                @click="goNext"
              >
                <i class="fa-solid fa-arrow-right"></i>
              </CircleButton>
            </div>
          </div>
        </div>
      </article>

      <aside class="cta">
        <div class="cta-heading">
          <h2>
            <span>Dapatkan</span>
            <span>Berita dan</span>
            <span>Artikel</span>
            <span>Terbaru</span>
          </h2>
          <p>Jelajahi informasi kesehatan terkurasi langsung dari apoteker kami.</p>
        </div>
        <Button :href="secondaryHref" size="lg" icon-position="right">
          Selengkapnya
          <template #icon>
            <i class="fa-solid fa-arrow-right"></i>
          </template>
        </Button>
      </aside>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import Button from './Button.vue'
import CircleButton from './CircleButton.vue'

const sectionRef = ref(null)
const isVisible = ref(false)
let observer = null
const backgroundLayers = ref([])
const backgroundTimeout = ref(null)

onMounted(() => {
  const el = sectionRef.value
  if (!el) return

  observer = new IntersectionObserver((entries) => {
    const entry = entries[0]
    if (entry && entry.isIntersecting) {
      isVisible.value = true
      if (observer) {
        observer.disconnect()
        observer = null
      }
    }
  }, { threshold: 0.25 })

  observer.observe(el)
})

onUnmounted(() => {
  if (observer) {
    observer.disconnect()
    observer = null
  }

  if (backgroundTimeout.value) {
    clearTimeout(backgroundTimeout.value)
    backgroundTimeout.value = null
  }
})

const props = defineProps({
  articles: {
    type: Array,
    default: () => [],
  },
  title: { type: String, default: 'Amoksisilin: Kapan Perlu Kapan Tidak' },
  excerpt: {
    type: String,
    default:
      'Ketahui kapan antibiotik benar-benar diperlukan, bagaimana penggunaannya yang aman, dan risiko resistensi bila digunakan tidak tepat.',
  },
  date: { type: String, default: '2025/09/26' },
  image: { type: String, default: null },
  primaryHref: { type: String, default: '#' },
  secondaryHref: { type: String, default: '#' },
})

const activeIndex = ref(0)
const availableArticles = computed(() => props.articles ?? [])
const hasArticles = computed(() => availableArticles.value.length > 0)

watch(
  () => availableArticles.value.length,
  (length) => {
    if (length === 0) {
      activeIndex.value = 0
      return
    }

    if (activeIndex.value >= length) {
      activeIndex.value = length - 1
    }

    if (activeIndex.value < 0) {
      activeIndex.value = 0
    }
  }
)

const currentArticle = computed(() => {
  if (!hasArticles.value) {
    return {
      title: props.title,
      excerpt: props.excerpt,
      published_at: props.date,
      cover_image_url: props.image,
      url: props.primaryHref,
    }
  }

  return availableArticles.value[activeIndex.value] ?? availableArticles.value[0]
})

const articleKey = computed(() => {
  const article = currentArticle.value
  if (!article) {
    return `fallback-${activeIndex.value}`
  }

  return (
    article.id ??
    article.slug ??
    (article.title ? article.title.toString() : `article-${activeIndex.value}`)
  )
})

const backgroundKey = computed(() => `bg-${articleKey.value}`)

const displayTitle = computed(() => currentArticle.value?.title ?? props.title)
const displayExcerpt = computed(() => currentArticle.value?.excerpt ?? props.excerpt)
const displayDate = computed(() => currentArticle.value?.published_at ?? props.date)

const primaryArticleHref = computed(() => {
  if (hasArticles.value) {
    const article = currentArticle.value
    if (!article) {
      return props.primaryHref
    }

    if (article.url) {
      return article.url
    }

    if (article.slug) {
      return `/artikel/${article.slug}`
    }
  }

  return props.primaryHref
})

const showNavControls = computed(() => availableArticles.value.length > 1)
const isPrevDisabled = computed(() => !showNavControls.value)
const isNextDisabled = computed(() => !showNavControls.value)

const goPrev = () => {
  if (!showNavControls.value) return
  const total = availableArticles.value.length
  activeIndex.value = (activeIndex.value - 1 + total) % total
}

const goNext = () => {
  if (!showNavControls.value) return
  const total = availableArticles.value.length
  activeIndex.value = (activeIndex.value + 1) % total
}

const bgStyle = computed(() => {
  const coverImage = currentArticle.value?.cover_image_url ?? props.image

  if (coverImage) {
    return {
      backgroundImage: `url(${coverImage})`,
    }
  }

  return {
    backgroundColor: '#000',
    backgroundImage: 'none',
  }
})

const applyBackgroundLayer = () => {
  const style = { ...bgStyle.value }
  const layerKey = `${backgroundKey.value}-${Date.now()}`

  backgroundLayers.value = [...backgroundLayers.value.slice(-1), { key: layerKey, style }]

  if (backgroundTimeout.value) {
    clearTimeout(backgroundTimeout.value)
  }

  backgroundTimeout.value = setTimeout(() => {
    backgroundLayers.value = backgroundLayers.value.slice(-1)
    backgroundTimeout.value = null
  }, 350)
}

watch(
  () => backgroundKey.value,
  () => {
    applyBackgroundLayer()
  },
  { immediate: true }
)
</script>

<style scoped lang="scss">
.articles-highlight {
  max-width: 1500px;
  margin: 0 auto 4rem;
  padding: 2.5rem 2rem 4rem;
  opacity: 0;
  transform: translateY(48px);
  transition: opacity 0.6s ease, transform 0.6s ease;
}

.articles-highlight.is-visible {
  opacity: 1;
  transform: translateY(0);
}

.articles-grid {
  display: grid;
  grid-template-columns: minmax(0, 2.15fr) minmax(0, 1fr);
  gap: clamp(2.5rem, 3vw, 4rem);
  align-items: stretch;
}

.media-background-wrapper {
  position: absolute;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  overflow: hidden;
}

.highlight-image-enter-active,
.highlight-image-leave-active {
  transition: opacity 0.35s ease;
}

.highlight-image-enter-from,
.highlight-image-leave-to {
  opacity: 0;
}

.highlight-card {
  position: relative;
  border-radius: 36px;
  overflow: hidden;
  min-height: 540px;
  background-color: #000;
  box-shadow:
    0 28px 55px rgba(21, 31, 110, 0.33),
    0 16px 32px rgba(21, 31, 110, 0.22);
  display: flex;
}

.media-background {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  z-index: 0;
  background-color: #000;
}

.media-overlay {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(135deg, rgba(0, 0, 0, 0.62), rgba(0, 0, 0, 0.52)),
    rgba(0, 0, 0, 0.45);
  z-index: 1;
}

.content {
  position: relative;
  z-index: 2;
  padding: clamp(2rem, 2rem + 1.6vw, 3rem) clamp(2rem, 2.2rem + 2vw, 3.5rem);
  color: #fff;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  flex: 1;
}

.card-date {
  position: absolute;
  top: 2.2rem;
  right: 2.4rem;
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.02em;
  opacity: 0.92;
}

.text {
  max-width: 820px;
  margin-top: 10rem;

  h3 {
    font-size: 2.4rem;
    line-height: 1.2;
    margin: 0 0 1rem;
    font-weight: 700;
  }

  p {
    margin: 0;
    font-size: 1.1rem;
    line-height: 1.7;
    opacity: 0.9;
  }
}

.card-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.nav-controls {
  display: inline-flex;
  gap: 0.75rem;
}

.cta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
  gap: 2.25rem;
  text-align: right;
  max-width: 360px;
  margin: 0 auto;
}

.cta-heading h2 {
  margin: 0 0 1.25rem;
  font-size: clamp(2.6rem, 2.4rem + 2vw, 4rem);
  font-weight: 800;
  line-height: 1.08;
  background: linear-gradient(180deg, #0d19a3 0%, #2f3df5 100%);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.cta-heading h2 span {
  display: block;
}

.cta-heading p {
  margin: 0;
  font-size: 1.05rem;
  color: #384059;
}

.cta :deep(.ui-button) {
  align-self: flex-end;
}

@media (max-width: 1024px) {
  .articles-grid {
    grid-template-columns: 1fr;
    gap: 3rem;
  }

  .content {
    padding: 2.5rem 2.25rem 2.5rem;
  }

  .cta {
    align-items: center;
    text-align: center;
    max-width: none;
  }

  .cta :deep(.ui-button) {
    align-self: center;
  }
}

@media (max-width: 600px) {
  .articles-highlight {
    padding: 2rem 1.25rem 3.25rem;
  }

  .highlight-card {
    min-height: 820px;
    border-radius: 28px;
  }

  .content {
    padding: 2.2rem 1.85rem 2.35rem;
  }

  .card-date {
    top: 1.6rem;
    right: 1.6rem;
  }

  .text h3 {
    font-size: 1.85rem;
  }

  .text p {
    font-size: 0.98rem;
  }
}
</style>
