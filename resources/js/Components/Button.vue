<template>
  <component
    :is="componentTag"
    :href="href"
    :type="componentTag === 'button' ? type : undefined"
    :disabled="componentTag === 'button' ? disabled : undefined"
    :aria-disabled="componentTag !== 'button' && disabled ? 'true' : undefined"
    :tabindex="componentTag !== 'button' && disabled ? -1 : undefined"
    :class="buttonClasses"
    v-bind="$attrs"
  >
    <span v-if="hasIcon && iconPosition === 'left'" class="ui-button__icon">
      <slot name="icon" />
    </span>
    <span class="ui-button__label">
      <slot />
    </span>
    <span v-if="hasIcon && iconPosition === 'right'" class="ui-button__icon">
      <slot name="icon" />
    </span>
  </component>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed, useSlots } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'danger', 'white', 'success'].includes(value),
  },
  href: {
    type: String,
    default: null,
  },
  type: {
    type: String,
    default: 'button',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
  iconPosition: {
    type: String,
    default: 'right',
    validator: (value) => ['left', 'right'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

const slots = useSlots()
const hasIcon = computed(() => Boolean(slots.icon))

const componentTag = computed(() => (props.href ? Link : 'button'))

const buttonClasses = computed(() => [
  'ui-button',
  `ui-button--${props.variant}`,
  `ui-button--${props.size}`,
  {
    'is-block': props.block,
    'is-disabled': props.disabled,
    'ui-button--with-icon': hasIcon.value,
    'ui-button--icon-left': hasIcon.value && props.iconPosition === 'left',
    'ui-button--icon-right': hasIcon.value && props.iconPosition === 'right',
  },
])
</script>

<style scoped lang="scss">
.ui-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
  border-radius: 999px;
  font-weight: 600;
  font-size: 1rem;
  line-height: 1.1;
  border: none;
  text-decoration: none;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
  user-select: none;
  -webkit-tap-highlight-color: transparent;
  padding: 0.7rem 1.9rem;
}

.ui-button--sm {
  padding: 0.45rem 1.25rem;
  font-size: 0.9rem;
}

.ui-button--lg {
  padding: 0.85rem 2.1rem;
  font-size: 1.05rem;
}

.ui-button.is-block {
  width: 100%;
}

.ui-button__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1em;
}

.ui-button--primary {
  background: linear-gradient(135deg, #0d19a3 0%, #2f3df5 100%);
  color: #ffffff;
}

.ui-button--primary:hover:not(.is-disabled),
.ui-button--primary:focus-visible:not(.is-disabled) {
  box-shadow: 0 18px 32px rgba(15, 30, 180, 0.35);
  transform: translateY(-1px);
}

.ui-button--danger {
  background: linear-gradient(135deg, #c62828 0%, #ef5350 100%);
  color: #ffffff;
}

.ui-button--danger:hover:not(.is-disabled),
.ui-button--danger:focus-visible:not(.is-disabled) {
  box-shadow: 0 18px 32px rgba(198, 40, 40, 0.35);
  transform: translateY(-1px);
}

.ui-button--white {
  background: rgba(255, 255, 255, 0.14);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.9);
  box-shadow: none;
  backdrop-filter: blur(6px);
}

.ui-button--white:hover:not(.is-disabled),
.ui-button--white:focus-visible:not(.is-disabled) {
  background: rgba(255, 255, 255, 0.22);
}

.ui-button--success {
  background: linear-gradient(135deg, #0b7a1d 0%, #27b033 100%);
  color: #ffffff;
}

.ui-button--success:hover:not(.is-disabled),
.ui-button--success:focus-visible:not(.is-disabled) {
  box-shadow: 0 18px 32px rgba(11, 122, 29, 0.35);
  transform: translateY(-1px);
}

.ui-button.is-disabled,
.ui-button[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
  pointer-events: none;
}

.ui-button:focus-visible {
  outline: 2px solid rgba(47, 61, 245, 0.35);
  outline-offset: 3px;
}
</style>
