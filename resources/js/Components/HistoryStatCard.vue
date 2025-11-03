<template>
  <article class="history-stat-card" tabindex="0">
    <div class="icon">
      <img v-if="iconImageUrl" :src="iconImageUrl" alt="" loading="lazy" />
      <i v-else :class="icon" aria-hidden="true" />
    </div>
    <div class="value">{{ value }}</div>
    <div class="label">{{ label }}</div>
  </article>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    default: ''
  },
  iconImageUrl: {
    type: String,
    default: ''
  },
  value: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  }
})
</script>

<style scoped lang="scss">
.history-stat-card {
  background: linear-gradient(180deg, #f6f8ff 0%, #f0f3ff 100%);
  border-radius: 24px;
  padding: 1.65rem 1.5rem;
  text-align: center;
  border: 1px solid rgba(19, 43, 133, 0.08);
  box-shadow: 0 16px 40px rgba(19, 43, 133, 0.12);
  display: grid;
  gap: 0.7rem;
  justify-items: center;
  transition: transform 0.25s ease, box-shadow 0.25s ease;

  .icon {
    width: 60px;
    height: 60px;
    border-radius: 18px;
    background: rgba(47, 61, 245, 0.12);
    display: grid;
    place-items: center;

    img {
      width: 70%;
      height: 70%;
      object-fit: contain;
    }

    i {
      font-size: 1.55rem;
      color: #1b3cc3;
    }
  }

  .value {
    font-size: 1.4rem;
    font-weight: 700;
    color: #101c5d;
  }

  .label {
    font-size: 0.98rem;
    color: #5c6789;
  }

  &:hover,
  &:focus-visible {
    transform: translateY(-6px);
    box-shadow: 0 22px 46px rgba(16, 35, 120, 0.22);
    outline: none;
  }
}

@media (max-width: 620px) {
  .history-stat-card {
    border-radius: 20px;
  }
}
</style>
