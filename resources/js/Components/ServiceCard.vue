<template>
    <div class="service-row service-card" :class="{ reverse }" ref="root">
        <div class="service-image" :class="imageClass"></div>
        <div class="service-content">
            <div class="service-icon">
                <i :class="icon"></i>
            </div>
            <h3>{{ title }}</h3>
            <p>{{ description }}</p>
            <ul>
                <li v-for="item in items" :key="item">{{ item }}</li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    title: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    items: {
        type: Array,
        default: () => []
    },
    imageClass: {
        type: String,
        required: true
    },
    reverse: {
        type: Boolean,
        default: false
    }
});

const root = ref(null);

defineExpose({
    root
});
</script>
