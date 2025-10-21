<template>
  <section class="partners-section" :class="{ 'is-visible': isVisible }" ref="sectionRef">
    <h2 class="section-title">Bekerjasama dengan berbagai PBF</h2>

    <div class="partners-marquee" @mouseenter="paused = true" @mouseleave="paused = false">
      <div class="marquee-track" :class="{ paused }">
        <div class="marquee-group" v-for="n in 2" :key="n">
          <div
            v-for="(logo, i) in displayLogos"
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
import { computed, onMounted, onUnmounted, ref } from 'vue'

const defaultLogos = [
  { name: 'AJM', src: new URL('../../images/PBFLLogo/AJM.png', import.meta.url).href },
  { name: 'APL', src: new URL('../../images/PBFLLogo/APL.png', import.meta.url).href },
  { name: 'BSP', src: new URL('../../images/PBFLLogo/BSP.png', import.meta.url).href },
  { name: 'CSF', src: new URL('../../images/PBFLLogo/CSF.png', import.meta.url).href },
  { name: 'Edi Hari Syam', src: new URL('../../images/PBFLLogo/EdiHariSyam.png', import.meta.url).href },
  { name: 'Elka Alkesindo', src: new URL('../../images/PBFLLogo/ElkaAlkesindo.png', import.meta.url).href },
  { name: 'Ferto Mulia Pratama', src: new URL('../../images/PBFLLogo/FertoMuliaPratama.png', import.meta.url).href },
  { name: 'Hidup Bahagia', src: new URL('../../images/PBFLLogo/HidupBahagia.png', import.meta.url).href },
  { name: 'Kimia Farma', src: new URL('../../images/PBFLLogo/KimiaFarma.png', import.meta.url).href },
  { name: 'Lenko Surya Perkasa', src: new URL('../../images/PBFLLogo/LenkoSuryaPerkasa.png', import.meta.url).href },
  { name: 'Marga Nusantara Jaya', src: new URL('../../images/PBFLLogo/MargaNusantaraJaya.png', import.meta.url).href },
  { name: 'MPI', src: new URL('../../images/PBFLLogo/MPI.png', import.meta.url).href },
  { name: 'PIM', src: new URL('../../images/PBFLLogo/PIM.png', import.meta.url).href },
  { name: 'Sapta Sari', src: new URL('../../images/PBFLLogo/SaptaSari.png', import.meta.url).href },
  { name: 'Satrindo Multi Sukses', src: new URL('../../images/PBFLLogo/SatrindoMultiSukses.png', import.meta.url).href },
  { name: 'Tempo Scan', src: new URL('../../images/PBFLLogo/TempoScan.png', import.meta.url).href },
]

const props = defineProps({
  logos: {
    type: Array,
    default: () => [],
  },
})

const paused = ref(false)
const sectionRef = ref(null)
const isVisible = ref(false)
let observer = null

const displayLogos = computed(() => {
  const source = Array.isArray(props.logos) && props.logos.length ? props.logos : defaultLogos

  return source.map((logo) => ({
    name: logo?.name ?? '',
    src: logo?.src ?? null,
  }))
})

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
  max-width: 1450px;
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
  animation: marquee 60s linear infinite;
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
  padding: 1.1rem;
  height: 96px;
  width: 220px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

  img {
    max-height: 82px;
    max-width: 180px;
    width: 100%;
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
