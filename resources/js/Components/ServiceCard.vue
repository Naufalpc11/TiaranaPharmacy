<template>
    <div class="service-row service-card" :class="{ reverse }" ref="root">
        <div class="service-image" :class="imageClass" :style="imageStyles"></div>
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
import { computed, ref } from 'vue';

const props = defineProps({
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
        default: ''
    },
    imageUrl: {
        type: String,
        default: null
    },
    reverse: {
        type: Boolean,
        default: false
    }
});

const imageStyles = computed(() => {
    if (!props.imageUrl) {
        return {};
    }

    return {
        backgroundImage: `url(${props.imageUrl})`,
    };
});

const root = ref(null);

defineExpose({
    root
});
</script>
<style lang="scss" scoped>
.service-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    min-height: 500px;
    &.reverse {
        direction: rtl;
        background-color: #f8f9fa;
        .service-content {
            direction: ltr;
        }
    }
}
.service-image {
    height: 100%;
    min-height: 500px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    &.service-image-resep {
        background-image: url('../../images/Interior.jpg');
    }
    &.service-image-konsultasi {
        background-image: url('../../images/Interior.jpg');
    }
    &.service-image-pemeriksaan {
        background-image: url('../../images/Interior.jpg');
    }
}
.service-content {
    padding: 4rem;
    max-width: 700px;
    margin: 0 auto;
    .service-icon {
        width: 80px;
        height: 80px;
        background: #1a237e;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        i {
            font-size: 2rem;
            color: white;
        }
    }
    h3 {
        font-size: 2rem;
        color: #1a237e;
        margin-bottom: 1.5rem;
    }
    p {
        font-size: 1.1rem;
        color: #666;
        line-height: 1.8;
        margin-bottom: 2rem;
    }
    ul {
        list-style: none;
        padding: 0;
        margin: 0;
        li {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1rem;
            color: #444;
            font-size: 1.1rem;
            &:before {
                content: "\2714";
                position: absolute;
                left: 0;
                color: #1a237e;
                font-weight: bold;
            }
        }
    }
}
@media (max-width: 1024px) {
    .service-row {
        grid-template-columns: 1fr;
        min-height: auto;
        &.reverse {
            direction: ltr;
        }
    }
    .service-image {
        height: 300px;
        min-height: 300px;
    }
    .service-content {
        padding: 3rem 2rem;
    }
}
</style>
