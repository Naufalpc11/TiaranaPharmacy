<template>
  <section class="articles-highlight">
    <div class="articles-grid">
      <article class="highlight-card" :style="bgStyle">
        <div class="overlay"></div>
        <div class="content">
          <div class="meta">{{ date }}</div>
          <h3>{{ title }}</h3>
          <p>{{ excerpt }}</p>
          <Link :href="primaryHref" class="btn-primary">
            <i class="fa-solid fa-book-open"></i>
            Baca Artikel
          </Link>
        </div>
      </article>
      <aside class="cta">
        <h2>Dapatkan Berita dan Artikel Terbaru</h2>
        <Link :href="secondaryHref" class="btn-outline">
          Selengkapnya
          <i class="fa-solid fa-arrow-right"></i>
        </Link>
      </aside>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  title: { type: String, default: 'Amoksisilin: Kapan Perlu Kapan Tidak' },
  excerpt: { type: String, default: 'Ketahui kapan antibiotik benar-benar diperlukan, bagaimana penggunaannya yang aman, dan risiko resistensi bila digunakan tidak tepat.' },
  date: { type: String, default: '2025/09/26' },
  image: { type: String, default: null },
  primaryHref: { type: String, default: '#' },
  secondaryHref: { type: String, default: '#' },
})

const bgStyle = computed(() => ({
  backgroundImage: props.image ? `url(${props.image})` : 'linear-gradient(135deg, #1a237e, #3949ab)'
}))
</script>

<style scoped lang="scss">
.articles-highlight {
  max-width: 1400px;
  margin: 0 auto 2rem;
  padding: 2rem 1rem 3rem;
}

.articles-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.highlight-card {
  position: relative;
  min-height: 280px;
  border-radius: 16px;
  overflow: hidden;
  background-size: cover;
  background-position: center;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);

  .overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(0,0,0,.55), rgba(0,0,0,.35));
  }

  .content {
    position: relative;
    color: #fff;
    padding: 1.5rem 1.5rem 1.75rem;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    height: 100%;

    .meta {
      font-size: .85rem;
      opacity: .85;
      margin-bottom: .5rem;
    }

    h3 {
      font-size: 1.5rem;
      margin: 0 0 .5rem;
    }

    p {
      margin: 0 0 1rem;
      opacity: .95;
      max-width: 60ch;
    }
  }
}

.cta {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.06);
  padding: 2rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;

  h2 {
    color: #1a237e;
    margin: 0 0 1rem;
  }
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  color: #fff;
  background: #1a237e;
  padding: .6rem 1rem;
  border-radius: 10px;
  text-decoration: none;
}

.btn-outline {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  color: #1a237e;
  border: 2px solid #1a237e;
  padding: .6rem 1rem;
  border-radius: 10px;
  text-decoration: none;
}

@media (max-width: 900px) {
  .articles-grid { grid-template-columns: 1fr; }
  .cta { align-items: center; text-align: center; }
}
</style>

