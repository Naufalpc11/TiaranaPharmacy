<template>
  <div class="feedback-dialog" :class="`feedback-dialog--${variant}`">
    <div class="feedback-dialog__icon">
      <svg
        v-if="variant === 'success'"
        width="88"
        height="88"
        viewBox="0 0 88 88"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
      >
        <circle cx="44" cy="44" r="41.5" stroke="#0B7A1D" stroke-width="5" />
        <path
          d="M61.3333 33.3334L38.5 56.1667L26.6666 44.3334"
          stroke="#0B7A1D"
          stroke-width="6"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>

      <svg
        v-else
        width="88"
        height="88"
        viewBox="0 0 88 88"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
      >
        <circle cx="44" cy="44" r="41.5" stroke="#132B85" stroke-width="5" />
        <path
          d="M48.2666 53.8667V51.1334C48.2666 48.5334 50.1333 47.0667 51.9999 45.6C53.8666 44.1334 55.7333 42.6667 55.7333 39.7334C55.7333 36.2667 52.9333 33.4667 49.4666 33.4667H43.4666C40.1333 33.4667 37.4666 36.1334 37.4666 39.4667M44 62.3334H44.0666"
          stroke="#132B85"
          stroke-width="6"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </div>

    <div class="feedback-dialog__content">
      <h3 class="feedback-dialog__title">{{ title }}</h3>
      <p class="feedback-dialog__message">{{ message }}</p>
    </div>

    <div class="feedback-dialog__actions" :class="`feedback-dialog__actions--${variant}`">
      <Button
        :variant="variant === 'success' ? 'success' : 'success'"
        size="lg"
        @click="$emit('primary')"
      >
        {{ primaryLabel }}
      </Button>
      <Button
        v-if="variant === 'confirm'"
        variant="danger"
        size="lg"
        @click="$emit('secondary')"
      >
        {{ secondaryLabel }}
      </Button>
    </div>
  </div>
</template>

<script setup>
import Button from './Button.vue';

defineProps({
  variant: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'confirm'].includes(value),
  },
  title: {
    type: String,
    required: true,
  },
  message: {
    type: String,
    required: true,
  },
  primaryLabel: {
    type: String,
    default: 'Oke',
  },
  secondaryLabel: {
    type: String,
    default: 'Batal',
  },
})

defineEmits(['primary', 'secondary'])
</script>

<style scoped lang="scss">
@import '../../css/_typography.scss';

.feedback-dialog {
  width: 784px;
  height: 298px;
  background: #ffffff;
  border-radius: 36px;
  box-shadow: 0 35px 80px rgba(19, 43, 133, 0.18);
  border: 1px solid rgba(19, 43, 133, 0.12);
  display: grid;
  grid-template-columns: 120px 1fr auto;
  align-items: center;
  padding: 0 48px;
  gap: 36px;
  font-family: $font-family-base;
}

.feedback-dialog__icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.feedback-dialog__content {
  display: grid;
  gap: 0.85rem;
}

.feedback-dialog__title {
  font-size: 2rem;
  font-weight: 800;
  color: #132b85;
  margin: 0;
}

.feedback-dialog__message {
  font-size: 1.28rem;
  line-height: 1.55;
  color: #1f2a44;
  margin: 0;
  max-width: 460px;
}

.feedback-dialog__actions {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 18px;

  :deep(.ui-button) {
    min-width: 138px;
    border-radius: 999px;
    font-size: 1.15rem;
    font-weight: 700;
    padding: 0.9rem 2.4rem;
  }
}

.feedback-dialog__actions--success {
  justify-content: flex-end;
}

.feedback-dialog__actions--confirm {
  justify-content: flex-end;
}

.feedback-dialog--confirm .feedback-dialog__message {
  max-width: 520px;
}

@media (max-width: 820px) {
  .feedback-dialog {
    width: 100%;
    max-width: 784px;
  }
}
</style>
