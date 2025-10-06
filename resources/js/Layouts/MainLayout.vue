<template>
    <div>
        <Header />
        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import Header from '../Components/Header.vue';
</script>

<style lang="scss" scoped>
main {
    margin-top: 0;
}
</style>
