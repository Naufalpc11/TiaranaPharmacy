<template>
  <article class="feature-card">
    <div
      class="feature-card__icon"
      :class="{ 'has-image': Boolean(iconImageUrl) }"
      aria-hidden="true"
    >
      <img
        v-if="iconImageUrl"
        :src="iconImageUrl"
        :alt="`${title} icon`"
      />
      <i v-else :class="fallbackIcon" />
    </div>
    <h3 class="feature-card__title">{{ title }}</h3>
    <p class="feature-card__description">{{ description }}</p>
  </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  icon: {
    type: String,
    default: ''
  },
  iconImageUrl: {
    type: String,
    default: null
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  }
})

const fallbackIcon = computed(
  () => props.icon || (props.iconImageUrl ? '' : 'fas fa-circle')
)
</script>

<style scoped lang="scss">
.feature-card {
  text-align: center;
  padding: 2rem;
  border-radius: 10px;
  transition: transform 0.3s ease;
  display: grid;
  justify-items: center;
  gap: 1.2rem;

  &:hover,
  &:focus-visible {
    transform: translateY(-5px);
    outline: none;
  }

  &__icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #eef2ff;
    color: #1a237e;
    display: grid;
    place-items: center;
    font-size: 3rem;

    i {
      line-height: 1;
    }

    img {
      width: 48px;
      height: 48px;
      object-fit: contain;
    }

    &.has-image {
      background: #ffffff;
      border: 2px solid #1a237e;
      font-size: 0;
    }
  }

  &__title {
    font-size: 1.5rem;
    margin: 0;
    color: #333;
  }

  &__description {
    margin: 0;
    color: #666;
    line-height: 1.6;
  }
}
</style>
