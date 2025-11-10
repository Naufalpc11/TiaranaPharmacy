<template>
  <MainLayout>
    <section class="not-found">
      <div class="not-found__content">
        <p class="not-found__code">404</p>
        <h1>Halaman tidak ditemukan</h1>
        <p class="not-found__description">
          Jalur yang Anda buka tidak tersedia atau sudah dipindahkan. Pastikan alamat sudah benar atau kembali ke halaman
          utama untuk melanjutkan navigasi.
        </p>
        <div class="not-found__actions">
          <Button href="/" size="lg">
            Kembali ke Beranda
          </Button>
          <Button variant="white" size="lg" @click="handleBack">
            Halaman Sebelumnya
          </Button>
        </div>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import Button from '../Components/Button.vue'
import MainLayout from '../Layouts/MainLayout.vue'

const handleBack = () => {
  if (window.history.length > 1) {
    window.history.back()
    return
  }
  router.visit('/')
}
</script>

<style scoped lang="scss">
.not-found {
  min-height: calc(100vh - 120px);
  background: linear-gradient(135deg, #f1f4ff 0%, #ffffff 60%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: clamp(2rem, 6vw, 4rem) 1.5rem;
  text-align: center;
}

.not-found__content {
  max-width: 640px;
  background: #ffffff;
  border-radius: 32px;
  padding: clamp(2rem, 5vw, 3.5rem);
  box-shadow: 0 40px 90px rgba(19, 43, 133, 0.12);
  border: 1px solid rgba(19, 43, 133, 0.08);
  display: grid;
  gap: 1.5rem;
}

.not-found__code {
  font-size: clamp(3rem, 12vw, 5.5rem);
  font-weight: 800;
  color: #2f3df5;
  margin: 0;
}

.not-found__content h1 {
  font-size: clamp(1.5rem, 4vw, 2.5rem);
  margin: 0;
  color: #132b85;
}

.not-found__description {
  font-size: 1rem;
  margin: 0;
  color: rgba(31, 42, 68, 0.8);
  line-height: 1.6;
}

.not-found__actions {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: center;
}

@media (max-width: 640px) {
  .not-found__content {
    padding: 2rem 1.5rem;
  }

  .not-found__actions :deep(.ui-button) {
    width: 100%;
  }
}
</style>
