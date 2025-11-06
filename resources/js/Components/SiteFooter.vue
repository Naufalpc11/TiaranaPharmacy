<template>
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Apotek Tiarana Farma</h3>
                <p>{{ tagline }}</p>
            </div>
            <div class="footer-section footer-section--contact">
                <h3>Kontak</h3>
                <ul>
                    <li><i class="fas fa-phone"></i> {{ contact.phone }}</li>
                    <li><i class="fas fa-envelope"></i> {{ contact.email }}</li>
                    <li><i class="fas fa-map-marker-alt"></i> {{ contact.address }}</li>
                    <li class="contact-link">
                        <i class="fas fa-bug"></i>
                        <a href="/report-bug">Laporkan Bug</a>
                    </li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Jam Operasional</h3>
                <ul>
                    <li>{{ hours.weekday }}</li>
                    <li>{{ hours.weekend }}</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Ikuti Kami</h3>
                <div class="social-links">
                    <a
                        :href="socialLinks.facebook || '#'"
                        class="social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Facebook Tiarana Pharmacy"
                    >
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a
                        :href="socialLinks.instagram || '#'"
                        class="social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Instagram Tiarana Pharmacy"
                    >
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a
                        :href="socialLinks.whatsapp || '#'"
                        class="social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="WhatsApp Tiarana Pharmacy"
                    >
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Ac {{ currentYear }} Apotek Tiarana Farma. All rights reserved.</p>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const currentYear = new Date().getFullYear();
const page = usePage();

const footer = computed(() => page.props.footer ?? {});
const tagline = computed(() => footer.value.tagline ?? '');
const contact = computed(() => footer.value.contact ?? {});
const hours = computed(() => footer.value.hours ?? {});
const socialLinks = computed(() => footer.value.social_links ?? {});
</script>

<style lang="scss" scoped>
.main-footer {
    background: #1a237e;
    color: white;
    padding: 3rem 0 0;
    margin-top: auto;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.footer-section {
    h3 {
        color: #fff;
        font-size: 1.2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;

        li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;

            i {
                width: 20px;
                color: #90caf9;
            }
        }
    }

    p {
        margin: 0;
        line-height: 1.6;
    }
}

.footer-section--contact .contact-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;

    a {
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;

        &:hover {
            color: #90caf9;
            text-decoration: underline;
        }
        &:focus-visible {
            outline: 2px solid rgba(255, 255, 255, 0.65);
            outline-offset: 2px;
        }
    }

    i {
        color: #ffffff;
        transition: color 0.2s ease;
    }

    &:hover i,
    &:focus-within i {
        color: #90caf9;
    }
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;

    .social-link {
        color: white;
        font-size: 1.5rem;
        transition: color 0.3s;

        &:hover {
            color: #90caf9;
        }
    }
}

.footer-bottom {
    margin-top: 2rem;
    padding: 1.5rem;
    text-align: center;
    background: rgba(0, 0, 0, 0.2);

    p {
        margin: 0;
        font-size: 0.9rem;
    }
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .footer-section {
        ul li {
            justify-content: center;
        }
    }

    .social-links {
        justify-content: center;
    }
}
</style>
