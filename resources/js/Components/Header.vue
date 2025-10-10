<template>
    <header class="main-header" :class="{ 'scrolled': isScrolled }">
        <nav class="nav-shortcut">
            <ul>
                <li><Link href="/" class="nav-link">Home</Link></li>
                <li><Link href="/about-us" class="nav-link">Tentang Kami</Link></li>
                <li><Link href="/contact" class="nav-link">Kontak</Link></li>
            </ul>
            <button class="chatbot-btn" disabled title="Segera hadir!">
                <i class="fa fa-robot"></i> AI Chatbot
            </button>
        </nav>
    </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<style lang="scss" scoped>
.main-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem 2rem;

    &.scrolled {
        padding: 1rem 2rem;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    .nav-shortcut {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;

        ul {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            margin: 0;
            padding: 0;

            li {
                .nav-link {
                    color: #333;
                    text-decoration: none;
                    font-size: 1.1rem;
                    font-weight: 500;
                    position: relative;
                    padding: 0.5rem 0;

                    &::after {
                        content: '';
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        width: 0;
                        height: 2px;
                        background: #1a237e;
                        transition: width 0.3s ease;
                    }

                    &:hover::after {
                        width: 100%;
                    }
                }
            }
        }

        .chatbot-btn {
            background: #1a237e;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-size: 1rem;
            cursor: not-allowed;
            opacity: 0.8;
            transition: all 0.3s ease;

            i {
                margin-right: 0.5rem;
            }

            &:hover {
                opacity: 0.9;
            }
        }
    }
}

@media (max-width: 768px) {
    .main-header {
        padding: 1rem;

        .nav-shortcut {
            ul {
                gap: 1.5rem;
            }

            .chatbot-btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
    }
}
</style>
