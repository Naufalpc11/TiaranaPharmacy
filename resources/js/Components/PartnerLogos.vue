<template>
  <section class="partners-section">
    <h2 class="section-title">Bekerjasama dengan berbagai PBF</h2>
    <div class="partners-row">
      <div
        v-for="(logo, i) in itemsToRender"
        :key="i"
        class="partner-item"
        :title="logo.name"
      >
        <img v-if="logo.src" :src="logo.src" :alt="logo.name" />
        <div v-else class="partner-fallback">{{ logo.name }}</div>
      </div>
    </div>
  </section>
  
</template>

<script setup>
import { computed } from 'vue'

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

// Repeat once to create a longer row similar to the design
const itemsToRender = computed(() => {
  return [...props.logos]
})
</script>

<style scoped lang="scss">
.partners-section {
  background: #fff;
  max-width: 1400px;
  margin: 0 auto;
  padding: 3rem 1rem 0;

  .section-title {
    text-align: center;
    font-size: 1.8rem;
    color: #1a237e;
    margin-bottom: 1.5rem;
  }
}

.partners-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 1.25rem;
  padding: 0 1rem 3rem;
}

.partner-item {
  background: #f8f9fa;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  height: 72px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);

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
</style>

