<template>
  <MainLayout>
    <div class="contact-page">
      <section class="contact-hero" aria-labelledby="contact-title">
        <div class="contact-hero__overlay" ref="contactHeroOverlay">
          <h1 id="contact-title" class="home-title" ref="contactHeroTitle">Kontak</h1>
          <p class="home-subtitle" ref="contactHeroSubtitle">
            Butuh bantuan cek ketersediaan obat atau konsultasi? Hubungi kami melalui WhatsApp atau isi formulir di
            bawah.
          </p>
        </div>
      </section>

      <section class="contact-section" aria-labelledby="contact-form-title">
        <div class="contact-form-card" ref="contactFormCard">
          <h2 id="contact-form-title" class="section-title contact-form__title">Kirim Pesan</h2>
          <form class="contact-form" @submit.prevent="handleSubmitIntent">
            <div class="contact-form__grid">
              <div class="contact-form__field">
                <InputField
                  label="Nama"
                  name="name"
                  autocomplete="name"
                  placeholder="Masukkan nama Anda"
                  v-model="form.name"
                  :error="form.errors.name"
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
                  :error="form.errors.email"
                  required
                />
              </div>
              <div class="contact-form__field">
                <InputField
                  label="Subjek"
                  name="subject"
                  placeholder="Tuliskan subjek pesan"
                  v-model="form.subject"
                  :error="form.errors.subject"
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
                  :error="form.errors.message"
                  required
                />
              </div>
            </div>
            <div class="contact-form__actions">
              <Button type="submit" size="lg" :disabled="form.processing">
                <template v-if="form.processing">
                  Mengirim...
                </template>
                <template v-else>
                  Kirim
                </template>
              </Button>
            </div>
          </form>
        </div>
      </section>
    </div>

    <Teleport to="body">
      <transition name="contact-dialog-fade">
        <div
          v-if="isDialogVisible"
          class="contact-dialog-backdrop"
          role="dialog"
          aria-modal="true"
          @click.self="handleBackdropClick"
        >
          <div class="contact-dialog__panel">
            <FeedbackDialog
              v-if="isConfirmDialogOpen"
              variant="confirm"
              title="Kirim pesan sekarang?"
              message="Pesan Anda akan langsung diteruskan ke tim apotek kami. Pastikan data sudah benar sebelum melanjutkan."
              primary-label="Kirim Sekarang"
              secondary-label="Periksa Lagi"
              @primary="confirmSubmission"
              @secondary="closeConfirmDialog"
            />
            <FeedbackDialog
              v-else-if="isSuccessDialogOpen"
              variant="success"
              title="Pesan berhasil dikirim"
              message="Terima kasih! Tim kami akan segera menghubungi Anda melalui kontak yang diberikan."
              primary-label="Selesai"
              @primary="closeSuccessDialog"
            />
            <FeedbackDialog
              v-else-if="isErrorDialogOpen"
              variant="error"
              :title="errorDialog.title"
              :message="errorDialog.message"
              primary-label="Tutup"
              @primary="closeErrorDialog"
            />
          </div>
        </div>
      </transition>
    </Teleport>
  </MainLayout>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import Button from '../Components/Button.vue'
import FeedbackDialog from '../Components/FeedbackDialog.vue'
import InputField from '../Components/InputField.vue'
import MainLayout from '../Layouts/MainLayout.vue'
import { initializeContactAnimations } from '../animations/contactAnimations'

const form = useForm({
  name: '',
  email: '',
  subject: '',
  message: '',
})

const contactHeroOverlay = ref(null)
const contactHeroTitle = ref(null)
const contactHeroSubtitle = ref(null)
const contactFormCard = ref(null)
const page = usePage()

const isConfirmDialogOpen = ref(false)
const isSuccessDialogOpen = ref(false)
const errorDialog = ref({
  title: '',
  message: '',
})
const isErrorDialogOpen = ref(false)

const isDialogVisible = computed(
  () => isConfirmDialogOpen.value || isSuccessDialogOpen.value || isErrorDialogOpen.value
)

if (page.props.flash?.contact_submitted) {
  isSuccessDialogOpen.value = true
}

const showErrorDialog = (message) => {
  errorDialog.value = {
    title: 'Gagal mengirim pesan',
    message: message || 'Sistem sedang mengalami kendala. Silakan coba kembali dalam beberapa saat.',
  }
  isErrorDialogOpen.value = true
}

const closeErrorDialog = () => {
  isErrorDialogOpen.value = false
  form.clearErrors('form')
}

watch(
  () => page.props.flash?.contact_error,
  (message) => {
    if (message) {
      showErrorDialog(message)
    }
  },
  { immediate: true }
)

const handleSubmitIntent = () => {
  form.clearErrors()

  const trimmedName = form.name?.toString().trim() ?? ''
  const trimmedEmail = form.email?.toString().trim() ?? ''
  const trimmedSubject = form.subject?.toString().trim() ?? ''
  const trimmedMessage = form.message?.toString().trim() ?? ''

  form.name = trimmedName
  form.email = trimmedEmail
  form.subject = trimmedSubject
  form.message = trimmedMessage

  let hasClientErrors = false

  if (!trimmedName) {
    form.setError('name', 'Nama wajib diisi.')
    hasClientErrors = true
  }

  if (!trimmedEmail) {
    form.setError('email', 'Email wajib diisi.')
    hasClientErrors = true
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
    form.setError('email', 'Format email tidak valid.')
    hasClientErrors = true
  }

  if (!trimmedMessage) {
    form.setError('message', 'Pesan wajib diisi.')
    hasClientErrors = true
  }

  if (!hasClientErrors) {
    isConfirmDialogOpen.value = true
  }
}

const closeConfirmDialog = () => {
  isConfirmDialogOpen.value = false
}

const confirmSubmission = () => {
  if (form.processing) {
    return
  }

  form.post('/contact', {
    preserveScroll: true,
    onStart: () => {
      isConfirmDialogOpen.value = false
    },
    onSuccess: () => {
      form.reset()
      isSuccessDialogOpen.value = true
    },
    onError: (errors) => {
      if (errors?.form) {
        showErrorDialog(errors.form)
      }
    },
  })
}

const closeSuccessDialog = () => {
  isSuccessDialogOpen.value = false
}

const handleBackdropClick = () => {
  if (isSuccessDialogOpen.value) {
    closeSuccessDialog()
  } else if (isErrorDialogOpen.value) {
    closeErrorDialog()
  } else if (isConfirmDialogOpen.value) {
    closeConfirmDialog()
  }
}

const handleKeydown = (event) => {
  if (event.key === 'Escape' && isDialogVisible.value) {
    handleBackdropClick()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
  initializeContactAnimations({
    heroOverlay: contactHeroOverlay.value,
    heroTitle: contactHeroTitle.value,
    heroSubtitle: contactHeroSubtitle.value,
    formCard: contactFormCard.value,
  })
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown)
})
</script>

<style lang="scss" scoped>
@import '../../css/Contact.scss';
</style>
