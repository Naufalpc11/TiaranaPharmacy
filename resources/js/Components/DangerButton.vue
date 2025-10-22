<template>
  <Button variant="danger" v-bind="$attrs">
    <template v-if="$slots.icon" #icon>
      <slot name="icon" />
    </template>
    <slot />
  </Button>
</template>

<script setup>
import Button from './Button.vue';
</script>
