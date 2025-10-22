<template>
  <article class="feature-card">
    <div class="feature-card__icon" aria-hidden="true">
      <i :class="icon" />
    </div>
    <h3 class="feature-card__title">{{ title }}</h3>
    <p class="feature-card__description">{{ description }}</p>
  </article>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    required: true
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
    font-size: 3rem;
    color: #1a237e;
    display: grid;

    i {
      line-height: 1;
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
