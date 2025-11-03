<template>
  <article class="contact-info-card">
    <div class="icon">
      <img v-if="iconImageUrl" :src="iconImageUrl" alt="" loading="lazy" />
      <i v-else :class="icon" aria-hidden="true" />
    </div>

    <div class="content">
      <h3>{{ title }}</h3>
      <p v-for="line in lines" :key="line">{{ line }}</p>
    </div>

    <button
      v-if="copyText"
      type="button"
      class="copy-btn"
      :aria-label="`Salin ${title}`"
      @click="$emit('copy', copyText)"
    >
      <i class="fa-regular fa-copy" aria-hidden="true" />
    </button>
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
  title: {
    type: String,
    required: true
  },
  lines: {
    type: Array,
    default: () => []
  },
  copyText: {
    type: String,
    default: ''
  }
})

defineEmits(['copy'])
</script>

<style scoped lang="scss">
.contact-info-card {
  position: relative;
  display: grid;
  grid-template-columns: 52px 1fr auto;
  gap: 1rem;
  background: #fff;
  border-radius: 20px;
  padding: 1.35rem 1.4rem;
  box-shadow: 0 16px 35px rgba(19, 43, 133, 0.08);
  border: 1px solid rgba(19, 43, 133, 0.08);
  align-items: center;

  .icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: rgba(47, 61, 245, 0.12);
    display: grid;
    place-items: center;

    img {
      width: 70%;
      height: 70%;
      object-fit: contain;
    }

    i {
      color: #1b3cc3;
      font-size: 1.15rem;
    }
  }

  .content {
    h3 {
      margin: 0 0 0.35rem;
      font-size: 1.08rem;
      color: #142873;
    }

    p {
      margin: 0;
      color: #4a5a7a;
      line-height: 1.5;
      font-size: 0.95rem;
    }
  }

  .copy-btn {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    border: 1px solid rgba(19, 43, 133, 0.12);
    background: rgba(19, 43, 133, 0.06);
    display: grid;
    place-items: center;
    color: #1b3cc3;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;

    &:hover,
    &:focus-visible {
      background: rgba(19, 43, 133, 0.12);
      transform: translateY(-1px);
      outline: none;
    }
  }
}

@media (max-width: 640px) {
  .contact-info-card {
    grid-template-columns: 1fr auto;
    grid-template-areas:
      'icon copy'
      'content content';
    gap: 0.85rem;
    text-align: left;

    .icon {
      grid-area: icon;
      margin-right: auto;
    }

    .content {
      grid-area: content;
    }

    .copy-btn {
      grid-area: copy;
      justify-self: end;
    }
  }
}
</style>
