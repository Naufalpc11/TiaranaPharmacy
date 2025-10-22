<template>
    <header class="main-header" :class="{ 'scrolled': isScrolled }">
        <nav class="nav-shortcut">
            <Link href="/" class="logo-link" aria-label="Beranda Tiarana Pharmacy" @click="closeMenu">
                <img
                    :src="logoWithText"
                    alt="Tiarana Pharmacy"
                    class="logo-image logo--desktop"
                />
                <img
                    :src="logoWithoutText"
                    alt="Tiarana Pharmacy"
                    class="logo-image logo--mobile"
                />
            </Link>
            <button
                type="button"
                class="menu-toggle"
                :aria-expanded="isMenuOpen"
                aria-controls="primary-navigation"
                aria-label="Toggle navigation menu"
                @click="toggleMenu"
            >
                <span class="menu-toggle__bar"></span>
                <span class="menu-toggle__bar"></span>
                <span class="menu-toggle__bar"></span>
            </button>
            <div
                class="nav-links"
                :class="{ 'is-open': isMenuOpen }"
                id="primary-navigation"
            >
                <ul>
                    <li><Link href="/" class="nav-link" @click="closeMenu">Home</Link></li>
                    <li><Link href="/about-us" class="nav-link" @click="closeMenu">Tentang Kami</Link></li>
                    <li><Link href="/artikel" class="nav-link" @click="closeMenu">Artikel</Link></li>
                    <li><Link href="/contact" class="nav-link" @click="closeMenu">Kontak</Link></li>
                </ul>
                <button class="chatbot-btn" disabled title="Segera hadir!" @click="closeMenu">
                    <i class="fa fa-robot"></i> AI Chatbot
                </button>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const isScrolled = ref(false);
const isMenuOpen = ref(false);
const logoWithText = new URL('../../images/Logo/Logo_Texed.png', import.meta.url).href;
const logoWithoutText = new URL('../../images/Logo/Logo_Fix.png', import.meta.url).href;

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const handleResize = () => {
    if (window.innerWidth > 768) {
        isMenuOpen.value = false;
    }
};

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

const closeMenu = () => {
    isMenuOpen.value = false;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleResize);
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
        position: relative;
        gap: 1.5rem;

        .menu-toggle {
            display: none;
            background: transparent;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;

            .menu-toggle__bar {
                display: block;
                width: 24px;
                height: 3px;
                background: #1a237e;
                margin: 4px 0;
                transition: transform 0.3s ease, opacity 0.3s ease;
                border-radius: 999px;
            }

            &[aria-expanded='true'] {
                .menu-toggle__bar:nth-child(1) {
                    transform: translateY(7px) rotate(45deg);
                }

                .menu-toggle__bar:nth-child(2) {
                    opacity: 0;
                }

                .menu-toggle__bar:nth-child(3) {
                    transform: translateY(-7px) rotate(-45deg);
                }
            }
        }

        .logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;

            .logo-image {
                height: 48px;
                width: auto;
                display: block;
            }

            .logo--mobile {
                display: none;
            }
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s ease;

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
                display: inline-flex;
                gap: 0.5rem;
                align-items: center;
                justify-content: center;
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
}

@media (max-width: 768px) {
    .main-header {
        padding: 1rem;

        .nav-shortcut {
            gap: 0.75rem;

            .logo-link {
                .logo-image {
                    height: 38px;
                }

                .logo--desktop {
                    display: none;
                }

                .logo--mobile {
                    display: block;
                }
            }

            .menu-toggle {
                display: inline-flex;
            }

            .nav-links {
                position: absolute;
                top: calc(100% + 0.75rem);
                right: 1rem;
                left: 1rem;
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
                background: rgba(255, 255, 255, 0.98);
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                padding: 1rem 1.25rem;
                border: 1px solid rgba(26, 35, 126, 0.08);
                transform: translateY(-12px);
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transform-origin: top;

                &.is-open {
                    transform: translateY(0);
                    opacity: 1;
                    visibility: visible;
                    pointer-events: auto;
                }

                ul {
                    flex-direction: column;
                    gap: 1rem;

                    li {
                        .nav-link {
                            width: 100%;
                            display: block;
                        }
                    }
                }

                .chatbot-btn {
                    width: 100%;
                    justify-content: center;
                }
            }

            .nav-links,
            .menu-toggle {
                z-index: 10;
            }
        }
    }
}
</style>


