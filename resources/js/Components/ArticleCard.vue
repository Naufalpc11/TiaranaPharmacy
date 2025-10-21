<template>
  <article class="article-card">
    <component
      :is="href ? 'a' : 'div'"
      class="article-card__link"
      :href="href"
      :target="target"
      :rel="relAttribute"
    >
      <figure class="article-card__media">
        <img
          :src="image"
          :alt="imageAlt"
          loading="lazy"
          class="article-card__image"
        />
      </figure>

      <div class="article-card__body">
        <h3 class="article-card__title">{{ title }}</h3>
        <p class="article-card__excerpt">{{ excerpt }}</p>
      </div>

      <footer class="article-card__footer">
        <time :datetime="datetime">{{ date }}</time>
      </footer>
    </component>
  </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  excerpt: {
    type: String,
    required: true,
  },
  date: {
    type: String,
    required: true,
  },
  datetime: {
    type: String,
    default: null,
  },
  image: {
    type: String,
    required: true,
  },
  imageAlt: {
    type: String,
    default: '',
  },
  href: {
    type: String,
    default: null,
  },
  target: {
    type: String,
    default: '_self',
  },
})

const relAttribute = computed(() => {
  if (props.target === '_blank') {
    return 'noopener noreferrer'
  }
  return undefined
})
</script>

<style scoped lang="scss">
@import '../../css/_variables.scss';

.article-card {
  width: 100%;
  display: block;
  border-radius: 28px;
  background: #ffffff;
  box-shadow: 0 26px 60px rgba(15, 30, 80, 0.12);
  border: 1px solid rgba(19, 43, 133, 0.08);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;

  &:hover {
    transform: translateY(-6px);
    box-shadow: 0 32px 72px rgba(15, 30, 80, 0.16);
  }
}

.article-card__link {
  display: grid;
  grid-template-rows: auto 1fr auto;
  height: 100%;
  color: inherit;
  text-decoration: none;
}

.article-card__media {
  margin: 0;
  position: relative;
  overflow: hidden;
  aspect-ratio: 4 / 3;
  background: #f1f3f8;
}

.article-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.35s ease;
}

.article-card:hover .article-card__image {
  transform: scale(1.05);
}

.article-card__body {
  padding: 1.6rem 1.8rem 1.2rem;
  display: grid;
  gap: 0.8rem;
}

.article-card__title {
  margin: 0;
  font-size: 1.15rem;
  font-weight: 700;
  color: #101c5d;
  line-height: 1.45;
}

.article-card__excerpt {
  margin: 0;
  font-size: 0.98rem;
  line-height: 1.6;
  color: #4a5a7a;
}

.article-card__footer {
  padding: 0 1.8rem 1.6rem;
  font-size: 0.9rem;
  color: rgba(16, 28, 93, 0.7);

  time {
    letter-spacing: 0.3px;
  }
}
</style>
