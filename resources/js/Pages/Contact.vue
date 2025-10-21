<template>
  <MainLayout>
    <div class="contact-page">
      <section class="contact-hero" aria-labelledby="contact-title">
        <div class="contact-hero__overlay">
          <h1 id="contact-title" class="home-title">Kontak</h1>
          <p class="home-subtitle">
            Butuh bantuan cek ketersediaan obat atau konsultasi? Hubungi kami melalui WhatsApp atau isi formulir di
            bawah.
          </p>
        </div>
      </section>

      <section class="contact-section" aria-labelledby="contact-form-title">
        <div class="contact-form-card">
          <h2 id="contact-form-title" class="section-title contact-form__title">Kirim Pesan</h2>
          <form class="contact-form" @submit.prevent="submitForm">
            <div class="contact-form__grid">
              <div class="contact-form__field">
                <InputField
                  label="Nama"
                  name="name"
                  autocomplete="name"
                  placeholder="Masukkan nama Anda"
                  v-model="form.name"
                  required
                />
              </div>
              <div class="contact-form__field">
                <InputField
                  label="Email"
                  name="email"
                  type="email"
                  autocomplete="email"
                  placeholder="contoh@email.com"
                  v-model="form.email"
                  required
                />
              </div>
              <div class="contact-form__field">
                <InputField
                  label="Subjek"
                  name="subject"
                  placeholder="Tuliskan subjek pesan"
                  v-model="form.subject"
                />
              </div>
              <div class="contact-form__field contact-form__field--full">
                <InputField
                  label="Pesan"
                  name="message"
                  placeholder="Ceritakan kebutuhan atau pertanyaan Anda di sini"
                  v-model="form.message"
                  :rows="7"
                  textarea
                  required
                />
              </div>
            </div>
            <div class="contact-form__actions">
              <Button type="submit" size="lg">
                Kirim
              </Button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { reactive } from 'vue'
import Button from '../Components/Button.vue'
import InputField from '../Components/InputField.vue'
import MainLayout from '../Layouts/MainLayout.vue'

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: '',
})

const submitForm = () => {
  // Placeholder for submission handler or integration.
}
</script>

<style lang="scss" scoped>
@import '../../css/Contact.scss';
</style>
