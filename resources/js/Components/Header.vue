<template>
    <header class="main-header">
        <nav class="nav-shortcut">
        <ul>
            <li><Link href="/">Home</Link></li>
            <li><Link href="/about-us">Tentang Kami</Link></li>
            <li><Link href="/contact">Kontak</Link></li>
        </ul>
        <button class="chatbot-btn" disabled title="Segera hadir!">
            <i class="fa fa-robot"></i> AI Chatbot
        </button>
        </nav>
    </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style lang="scss" scoped>
@import '../../css/home.scss';
</style>
