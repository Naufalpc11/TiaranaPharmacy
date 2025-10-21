<template>
  <article class="mission-card" role="article" tabindex="0">
    <h3>{{ title }}</h3>
    <p>{{ description }}</p>
  </article>
</template>

<script setup>
defineProps({
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
.mission-card {
  background: #fff;
  border-radius: 22px;
  padding: 2.25rem 2rem;
  box-shadow: 0 18px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(19, 43, 133, 0.08);
  text-align: center;
  min-height: 210px;
  display: grid;
  gap: 1.75rem;
  align-items: start;
  color: #1f2a44;
  transform: translateY(0);
  will-change: transform, box-shadow, background, color;
  transform: translate3d(0,0,0);
  transition: background .3s ease, color .3s ease, box-shadow .25s ease,
        transform .28s cubic-bezier(.2,.8,.2,1);

  h3 {
    font-size: 1.18rem;
    font-weight: 700;
    margin: 0;
    color: #142873;
    transition: inherit;
  }

  p {
    margin: 0;
    font-size: 1.02rem;
    line-height: 1.6;
    transition: inherit;
  }

  &:hover,
  &:focus-visible {
    background: #0f214f;
    color: #fff;
    box-shadow: 0 20px 46px rgba(19, 43, 133, 0.35);
    transform: translateY(-10px);
    outline: none;

    h3,
    p {
      color: #fff;
    }
  }
}
@media (hover: none) {
  .mission-card:active { transform: translate3d(0,-10px,0); }
}

@media (prefers-reduced-motion: reduce) {
  .mission-card {
    transition: none;
  }
  .mission-card:hover,
  .mission-card:focus-visible {
    transform: none;
  }
}
@media (max-width: 560px) {
  .mission-card {
    padding: 1.35rem;
  }
}
</style>
