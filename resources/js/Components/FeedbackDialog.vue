<template>
  <div class="feedback-dialog" :class="`feedback-dialog--${variant}`">
    <div
      class="feedback-dialog__icon"
      :class="`feedback-dialog__icon--${variant}`"
      aria-hidden="true"
    >
      <template v-if="variant === 'success'">
        <svg
          width="64"
          height="64"
          viewBox="0 0 88 88"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
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
      </template>
      <template v-else-if="variant === 'error'">
        <svg
          width="64"
          height="64"
          viewBox="0 0 88 88"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <circle cx="44" cy="44" r="41.5" stroke="#C62828" stroke-width="5" />
          <path
            d="M55 33L33 55M33 33L55 55"
            stroke="#C62828"
            stroke-width="6"
            stroke-linecap="round"
          />
        </svg>
      </template>
      <template v-else>
        <svg
          width="64"
          height="64"
          viewBox="0 0 640 640"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill="#132B85"
            d="M224 224C224 171 267 128 320 128C373 128 416 171 416 224C416 266.7 388.1 302.9 349.5 315.4C321.1 324.6 288 350.7 288 392V416C288 433.7 302.3 448 320 448C337.7 448 352 433.7 352 416V392C352 390.3 352.6 387.9 355.5 384.7C358.5 381.4 363.4 378.2 369.2 376.3C433.5 355.6 480 295.3 480 224C480 135.6 408.4 64 320 64C231.6 64 160 135.6 160 224C160 241.7 174.3 256 192 256C209.7 256 224 241.7 224 224zM320 576C342.1 576 360 558.1 360 536C360 513.9 342.1 496 320 496C297.9 496 280 513.9 280 536C280 558.1 297.9 576 320 576z"
          />
        </svg>
      </template>
    </div>

    <div class="feedback-dialog__content">
      <h3 class="feedback-dialog__title">{{ title }}</h3>
      <p class="feedback-dialog__message">{{ message }}</p>
    </div>

    <div class="feedback-dialog__actions" :class="`feedback-dialog__actions--${variant}`">
      <Button
        :variant="primaryButtonVariant"
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
import { computed } from 'vue'
import Button from './Button.vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'confirm', 'error'].includes(value),
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

const primaryButtonVariant = computed(() => {
  if (props.variant === 'error') {
    return 'danger'
  }
  if (props.variant === 'success') {
    return 'success'
  }
  return 'primary'
})

defineEmits(['primary', 'secondary'])
</script>

<style scoped lang="scss">
@import '../../css/_typography.scss';

.feedback-dialog {
  width: min(784px, calc(100vw - 2.5rem));
  background: #ffffff;
  border-radius: 36px;
  box-shadow: 0 35px 80px rgba(19, 43, 133, 0.18);
  border: 1px solid rgba(19, 43, 133, 0.12);
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: clamp(1.5rem, 3vw, 2.75rem);
  padding: clamp(1.75rem, 3vw, 2.5rem) clamp(2rem, 4vw, 3rem);
  font-family: $font-family-base;
  flex-wrap: wrap;
}

.feedback-dialog__icon {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

.feedback-dialog__icon--success {
  width: 64px;
  height: 64px;
}

.feedback-dialog__icon--confirm,
.feedback-dialog__icon--error {
  width: 92px;
  height: 92px;
  border-radius: 50%;
  border: 3px solid rgba(19, 43, 133, 0.8);
  padding: 12px;
  box-sizing: border-box;
}

.feedback-dialog__icon--confirm svg,
.feedback-dialog__icon--error svg {
  width: 64px;
  height: 64px;
}

.feedback-dialog__icon--error {
  border-color: rgba(198, 40, 40, 0.75);
}

.feedback-dialog__content {
  flex: 1 1 280px;
  display: grid;
  gap: 0.85rem;
  min-width: 0;
}

.feedback-dialog__title {
  font-size: clamp(1.6rem, 2vw, 2rem);
  font-weight: 800;
  color: #132b85;
  margin: 0;
}

.feedback-dialog__message {
  font-size: clamp(1.05rem, 1.8vw, 1.28rem);
  line-height: 1.55;
  color: #1f2a44;
  margin: 0;
}

.feedback-dialog__actions {
  flex: 0 0 auto;
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 18px;
  margin-left: auto;

  :deep(.ui-button) {
    min-width: 138px;
    border-radius: 999px;
    font-size: 1.1rem;
    font-weight: 700;
    padding: 0.85rem 2.2rem;
  }
}

.feedback-dialog__actions--success,
.feedback-dialog__actions--confirm,
.feedback-dialog__actions--error {
  justify-content: flex-end;
}

.feedback-dialog--error {
  border-color: rgba(198, 40, 40, 0.2);
  box-shadow: 0 35px 80px rgba(198, 40, 40, 0.18);
}

@media (max-width: 900px) {
  .feedback-dialog {
    width: min(720px, calc(100vw - 2rem));
  }

  .feedback-dialog__actions {
    width: 100%;
    justify-content: flex-start;
    margin-left: 0;
  }
}

@media (max-width: 620px) {
  .feedback-dialog {
    flex-direction: column;
    text-align: center;
    align-items: center;
  }

  .feedback-dialog__content {
    justify-items: center;
  }

  .feedback-dialog__actions {
    justify-content: center;
    flex-wrap: wrap;

    :deep(.ui-button) {
      width: min(260px, 100%);
    }
  }
}
</style>
