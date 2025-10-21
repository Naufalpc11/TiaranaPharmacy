<template>
  <section class="articles-highlight" :class="{ 'is-visible': isVisible }" ref="sectionRef">
    <div class="articles-grid">
      <article class="highlight-card" :style="bgStyle">
        <div class="media-overlay"></div>
        <div class="content">
          <div class="card-date">{{ date }}</div>

          <div class="text">
            <h3>{{ title }}</h3>
            <p>{{ excerpt }}</p>
          </div>

          <div class="card-actions">
            <Button :href="primaryHref" size="lg">
              Baca Artikel
            </Button>
            <div class="nav-controls">
              <CircleButton disabled aria-label="Artikel sebelumnya">
                <i class="fa-solid fa-arrow-left"></i>
              </CircleButton>
              <CircleButton disabled aria-label="Artikel selanjutnya">
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
import { computed, onMounted, onUnmounted, ref } from 'vue'
import Button from './Button.vue'
import CircleButton from './CircleButton.vue'

const sectionRef = ref(null)
const isVisible = ref(false)
let observer = null

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
})

const props = defineProps({
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

const bgStyle = computed(() => {
  if (props.image) {
    return {
      backgroundImage: `url(${props.image})`,
    }
  }

  return {
    backgroundImage: 'linear-gradient(135deg, #1a237e, #3949ab)',
  }
})
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
  gap: 4rem;
  align-items: center;
}

.highlight-card {
  position: relative;
  min-height: 440px;
  border-radius: 36px;
  overflow: hidden;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  box-shadow: 0 26px 48px rgba(0, 0, 0, 0.22);
}

.media-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.55));
}

.content {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  gap: 2rem;
  padding: 3.25rem 3.25rem 2.75rem;
  color: #ffffff;
  height: 100%;
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
