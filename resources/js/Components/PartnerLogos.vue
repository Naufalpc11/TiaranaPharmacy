<template>
  <section class="partners-section" :class="{ 'is-visible': isVisible }" ref="sectionRef">
    <h2 class="section-title">Bekerjasama dengan berbagai PBF</h2>

    <div class="partners-marquee" @mouseenter="paused = true" @mouseleave="paused = false">
      <div class="marquee-track" :class="{ paused }">
        <div class="marquee-group" v-for="n in 2" :key="n">
          <div
            v-for="(logo, i) in logos"
            :key="`${n}-${i}`"
            class="partner-item"
            :title="logo.name"
          >
            <img v-if="logo.src" :src="logo.src" :alt="logo.name" />
            <div v-else class="partner-fallback">{{ logo.name }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
  logos: {
    type: Array,
    default: () => [
      { name: 'Einsfal' },
      { name: 'BSP' },
      { name: 'Merapi' },
      { name: 'Einsfal' },
      { name: 'BSP' },
      { name: 'Merapi' },
    ],
  },
})

const paused = ref(false)
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
  }, { threshold: 0.2 })

  observer.observe(el)
})

onUnmounted(() => {
  if (observer) {
    observer.disconnect()
    observer = null
  }
})
</script>

<style scoped lang="scss">
.partners-section {
  background: #fff;
  max-width: 1400px;
  margin: 0 auto;
  padding: 3rem 1rem 0;
  opacity: 0;
  transform: translateY(40px);
  transition: opacity 0.6s ease, transform 0.6s ease;

  .section-title {
    text-align: center;
    font-size: 1.8rem;
    color: #1a237e;
    margin-bottom: 1.5rem;
  }
}

.partners-section.is-visible {
  opacity: 1;
  transform: translateY(0);
}

.partners-marquee {
  overflow: hidden;
  padding: 0 0 3rem;
}

.marquee-track {
  display: flex;
  width: max-content;
  flex-wrap: nowrap;
  gap: 1.25rem;
  animation: marquee 30s linear infinite;
}

.marquee-track.paused {
  animation-play-state: paused;
}

.marquee-group {
  display: flex;
  gap: 1.25rem;
}

.partner-item {
  background: #f8f9fa;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  height: 72px;
  width: 180px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

  img {
    max-height: 48px;
    object-fit: contain;
    filter: grayscale(15%);
  }

  .partner-fallback {
    font-weight: 700;
    color: #1a237e;
    letter-spacing: 0.5px;
  }
}

@keyframes marquee {
  0% {
    transform: translateX(0);
  }

  100% {
    transform: translateX(-50%);
  }
}

@media (prefers-reduced-motion: reduce) {
  .marquee-track {
    animation: none;
  }
}
</style>
