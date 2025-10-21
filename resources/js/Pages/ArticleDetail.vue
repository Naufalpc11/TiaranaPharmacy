<template>
  <MainLayout>
    <div class="article-detail-page">
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
          <div class="article-detail-intro">
            <p
              v-for="(paragraph, index) in article.introduction"
              :key="`intro-${index}`"
              class="article-detail-paragraph"
            >
              {{ paragraph }}
            </p>
          </div>

          <div
            v-for="(section, index) in article.sections"
            :key="section.title"
            class="article-detail-section"
          >
            <h2 class="article-detail-section__title">
              {{ section.title }}
            </h2>
            <div class="article-detail-section__body">
              <template
                v-for="(block, blockIndex) in section.body"
                :key="`block-${index}-${blockIndex}`"
              >
                <p
                  v-if="block.type === 'paragraph'"
                  class="article-detail-paragraph"
                >
                  {{ block.text }}
                </p>

                <ul
                  v-else-if="block.type === 'list' && !block.ordered"
                  class="article-detail-list"
                >
                  <li
                    v-for="(item, itemIndex) in block.items"
                    :key="`list-${index}-${blockIndex}-${itemIndex}`"
                    class="article-detail-list__item"
                  >
                    {{ item }}
                  </li>
                </ul>

                <ol
                  v-else-if="block.type === 'list' && block.ordered"
                  class="article-detail-list article-detail-list--ordered"
                >
                  <li
                    v-for="(item, itemIndex) in block.items"
                    :key="`olist-${index}-${blockIndex}-${itemIndex}`"
                    class="article-detail-list__item"
                  >
                    {{ item }}
                  </li>
                </ol>
              </template>
            </div>
          </div>
        </article>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import MainLayout from '../Layouts/MainLayout.vue'
import { initializeArticleDetailAnimations } from '../animations/articleDetailAnimations'

const props = defineProps({
  article: {
    type: Object,
    required: true,
  },
})

const heroOverlay = ref(null)
const heroBackButton = ref(null)
const heroTitle = ref(null)
const heroDate = ref(null)
const contentCard = ref(null)

const formattedDate = computed(() => {
  if (!props.article?.date) {
    return ''
  }

  const date = new Date(props.article.date)
  if (Number.isNaN(date.getTime())) {
    return props.article.date
  }

  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(date)
})

const heroStyle = computed(() => {
  if (!props.article?.heroImage) {
    return {}
  }

  return {
    '--hero-image': `url(${props.article.heroImage})`,
  }
})

onMounted(() => {
  initializeArticleDetailAnimations({
    heroOverlay: heroOverlay.value,
    heroTitle: heroTitle.value,
    heroDate: heroDate.value,
    heroBackButton: heroBackButton.value,
    contentCard: contentCard.value,
  })
})
</script>

<style lang="scss" scoped>
@import '../../css/ArticleDetail.scss';
</style>
