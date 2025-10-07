<template>
    <div class="layout-container">
        <Header />
        <main class="main-content">
            <slot />
        </main>
        <SiteFooter />
    </div>
</template>

<script setup>
import Header from '../Components/Header.vue';
import SiteFooter from '../Components/SiteFooter.vue';
</script>

<style lang="scss">
/* Remove default margin and padding from body and html */
:root {
    margin: 0;
    padding: 0;
    height: 100%;
}

html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
}

/* Layout container styles */
.layout-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    width: 100vw;
    margin: 0;
    padding: 0;
    position: relative;
    overflow-x: hidden;
}

/* Main content styles */
.main-content {
    flex: 1;
    width: 100vw;
    margin: 0;
    padding: 0;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: stretch;

    /* Reset min-height for regular pages */
    > * {
        width: 100vw;
        margin: 0;
        padding: 0;
        min-width: 100%;
    }
}/* Ensure images and media are responsive */
img, video, iframe {
    max-width: 100%;
    height: auto;
}

/* Utility class for full viewport sections */
.full-viewport {
    min-height: calc(100vh - var(--header-height, 60px));
    width: 100vw;
    margin: 0;
    padding: 0;
    position: relative;
}

/* Reset box-sizing */
*, *::before, *::after {
    box-sizing: border-box;
}
</style>
