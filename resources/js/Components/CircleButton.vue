<template>
  <button
    class="circle-button"
    type="button"
    :disabled="props.disabled"
    v-bind="$attrs"
  >
    <span class="circle-button__icon">
      <slot />
    </span>
  </button>
</template>

<script setup>
const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped lang="scss">
.circle-button {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.55);
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease;
  -webkit-tap-highlight-color: transparent;
}

.circle-button__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

.circle-button:not(:disabled):hover,
.circle-button:not(:disabled):focus-visible {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.9);
  transform: translateY(-1px);
  box-shadow: 0 16px 30px rgba(0, 0, 0, 0.26);
  outline: none;
}

.circle-button:disabled {
  opacity: 0.45;
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}
</style>
