<template>
    <MainLayout>
        <div class="artikel-container">
            <header class="artikel-header" role="banner">
                <nav class="breadcrumbs" aria-label="Breadcrumb">
                    <Link href="/" class="crumb">Beranda</Link>
                    <span class="divider" aria-hidden="true">/</span>
                    <span class="current" aria-current="page">Artikel</span>
                </nav>
                <h1>Artikel</h1>
                <p>Kumpulan artikel, tips kesehatan, dan informasi obat dari Tiarana Farma.</p>
            </header>

            <section class="artikel-list" aria-label="Daftar Artikel">
                <div class="grid">
                    <article class="card">
                        <h3>Amoksisilin: Kapan Perlu Kapan Tidak</h3>
                        <p>Ketahui kapan antibiotik benar-benar diperlukan dan risikonya bila tidak tepat.</p>
                        <a class="read-more" href="#">Baca selengkapnya</a>
                    </article>
                    <article class="card">
                        <h3>Tips Menyimpan Obat di Rumah</h3>
                        <p>Panduan singkat menyimpan obat agar kualitasnya tetap terjaga.</p>
                        <a class="read-more" href="#">Baca selengkapnya</a>
                    </article>
                    <article class="card">
                        <h3>Kenali Interaksi Obat Umum</h3>
                        <p>Beberapa kombinasi obat yang perlu dihindari untuk keamanan Anda.</p>
                        <a class="read-more" href="#">Baca selengkapnya</a>
                    </article>
                </div>
            </section>
        </div>
    </MainLayout>
</template>

<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<style lang="scss" scoped>
@import '../../css/Artikel.scss';
</style>
