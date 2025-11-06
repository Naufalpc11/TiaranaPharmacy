<template>
  <label :for="fieldId" :class="rootClasses">
    <span v-if="label" class="form-input__label">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="form-input__required">*</span>
    </span>

    <component
      :is="isTextarea ? 'textarea' : 'input'"
      :id="fieldId"
      class="form-input__control"
      :type="isTextarea ? undefined : type"
      :placeholder="placeholder"
      :rows="isTextarea ? rows : undefined"
      :disabled="disabled"
      :autocomplete="autocomplete"
      :name="name"
      :aria-required="required ? 'true' : undefined"
      :aria-invalid="error ? 'true' : 'false'"
      :value="stringifiedValue"
      @input="handleInput"
      @focus="forwardFocus"
      @blur="forwardBlur"
      v-bind="$attrs"
    />

    <span v-if="hint && !error" class="form-input__hint">{{ hint }}</span>
    <span v-if="error" class="form-input__error" role="alert">{{ error }}</span>
  </label>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  textarea: {
    type: Boolean,
    default: false,
  },
  rows: {
    type: Number,
    default: 5,
  },
  required: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  hint: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  id: {
    type: String,
    default: null,
  },
  autocomplete: {
    type: String,
    default: 'off',
  },
  name: {
    type: String,
    default: null,
  },
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur', 'input'])

let autoId = 0
const generateId = () => `input-field-${++autoId}`

const internalId = ref(props.id ?? generateId())
watch(
  () => props.id,
  (newId) => {
    if (newId) {
      internalId.value = newId
    }
  }
)

const isTextarea = computed(() => props.textarea || props.type === 'textarea')
const fieldId = computed(() => internalId.value)
const stringifiedValue = computed(() =>
  props.modelValue === null || props.modelValue === undefined ? '' : String(props.modelValue)
)

const rootClasses = computed(() => ({
  'form-input': true,
  'form-input--textarea': isTextarea.value,
  'form-input--disabled': props.disabled,
  'form-input--error': Boolean(props.error),
}))

const handleInput = (event) => {
  const rawValue = event.target.value
  const nextValue =
    props.type === 'number' && rawValue !== '' ? Number.isNaN(Number(rawValue)) ? rawValue : Number(rawValue) : rawValue

  emit('update:modelValue', nextValue)
  emit('input', event)
}

const forwardFocus = (event) => emit('focus', event)
const forwardBlur = (event) => emit('blur', event)
</script>

<style scoped lang="scss">
@import '../../css/_variables.scss';

.form-input {
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
  color: #1f2a44;

  &__label {
    font-weight: 600;
    font-size: 0.95rem;
    letter-spacing: 0.2px;
    color: #142873;
  }

  &__required {
    color: #c62828;
    margin-left: 0.25rem;
  }

  &__control {
    font-size: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 12px;
    border: 1px solid rgba(19, 43, 133, 0.18);
    background: #ffffff;
    color: inherit;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    resize: vertical;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

    &:focus {
      outline: none;
      border-color: rgba(47, 61, 245, 0.6);
      box-shadow: 0 0 0 4px rgba(47, 61, 245, 0.12);
    }

    &:disabled {
      cursor: not-allowed;
      opacity: 0.6;
      background: #f5f7fb;
    }

    &::placeholder {
      color: rgba(31, 42, 68, 0.35);
    }
  }

  &__hint {
    font-size: 0.85rem;
    color: rgba(31, 42, 68, 0.65);
  }

  &__error {
    font-size: 0.85rem;
    color: #c62828;
  }
}

.form-input--textarea .form-input__control {
  min-height: 180px;
}

.form-input--disabled {
  opacity: 0.7;
  pointer-events: none;
}

.form-input--error .form-input__control {
  border-color: rgba(198, 40, 40, 0.6);
}
</style>
