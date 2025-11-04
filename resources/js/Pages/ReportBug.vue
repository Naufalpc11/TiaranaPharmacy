<template>
  <MainLayout>
    <div class="bug-report-page">
      <section class="bug-report-hero" aria-labelledby="bug-report-title">
        <div class="bug-report-hero__content">
          <h1 id="bug-report-title">Laporkan Bug</h1>
          <p>
            Temukan kendala saat menggunakan aplikasi? Laporkan detailnya agar tim kami dapat segera memperbaikinya.
          </p>
        </div>
      </section>

      <section class="bug-report-section" aria-labelledby="bug-report-form-title">
        <div class="bug-report-card">
          <h2 id="bug-report-form-title">Formulir Laporan Bug</h2>
          <form class="bug-report-form" @submit.prevent="handleSubmitIntent">
            <div class="bug-report-form__grid">
              <div class="bug-report-form__field bug-report-form__field--full">
                <InputField
                  label="Subjek"
                  name="subject"
                  placeholder="Tuliskan ringkasan bug"
                  v-model="form.subject"
                  :error="form.errors.subject"
                  required
                />
              </div>

              <div class="bug-report-form__field bug-report-form__field--full">
                <InputField
                  label="Email (opsional)"
                  name="email"
                  type="email"
                  autocomplete="email"
                  placeholder="contoh@email.com"
                  v-model="form.email"
                  :error="form.errors.email"
                />
              </div>

              <div class="bug-report-form__field bug-report-form__field--full">
                <InputField
                  label="Deskripsi Bug"
                  name="description"
                  placeholder="Jelaskan apa yang terjadi, langkah-langkah untuk mengulanginya, dan harapan Anda"
                  v-model="form.description"
                  :rows="8"
                  textarea
                  :error="form.errors.description"
                  required
                />
              </div>

              <div class="bug-report-form__field bug-report-form__field--full">
                <label class="bug-report-upload">
                  <span class="bug-report-upload__label">
                    Gambar Bukti
                    <span aria-hidden="true" class="bug-report-upload__required">*</span>
                  </span>
                  <p class="bug-report-upload__hint">
                    Unggah tangkapan layar atau foto bagian yang bermasalah (PNG, JPG, JPEG, WEBP - maks. 4 MB).
                  </p>

                  <div class="bug-report-upload__dropzone" @dragover.prevent @drop.prevent="handleDrop">
                    <div class="bug-report-upload__info">
                      <span class="bug-report-upload__icon" aria-hidden="true">&#128247;</span>
                      <div class="bug-report-upload__text">
                        <strong>{{ screenshotName || 'Pilih atau seret gambar ke sini' }}</strong>
                        <span v-if="!screenshotName">Klik untuk memilih file</span>
                      </div>
                    </div>
                    <input
                      ref="screenshotInput"
                      class="bug-report-upload__input"
                      type="file"
                      accept="image/png,image/jpeg,image/jpg,image/webp"
                      @change="handleFileChange"
                    />
                  </div>

                  <transition name="bug-report-fade">
                    <div v-if="screenshotPreview" class="bug-report-preview">
                      <img :src="screenshotPreview" alt="Pratinjau gambar laporan bug" />
                      <Button type="button" variant="danger" size="sm" @click="clearScreenshot">
                        Hapus Gambar
                      </Button>
                    </div>
                  </transition>

                  <p v-if="screenshotError" class="bug-report-upload__error" role="alert">{{ screenshotError }}</p>
                  <p v-else class="bug-report-upload__hint bug-report-upload__hint--footer">
                    Pastikan informasi sensitif tidak terlihat pada gambar.
                  </p>
                </label>
              </div>
            </div>

            <div class="bug-report-form__actions">
              <Button type="submit" size="lg" :disabled="form.processing">
                <template v-if="form.processing">
                  Mengirim laporan...
                </template>
                <template v-else>
                  Kirim Laporan Bug
                </template>
              </Button>
            </div>
          </form>
        </div>
      </section>
    </div>

    <Teleport to="body">
      <transition name="bug-report-dialog-fade">
        <div
          v-if="isDialogVisible"
          class="bug-report-dialog-backdrop"
          role="dialog"
          aria-modal="true"
          @click.self="handleBackdropClick"
        >
          <div class="bug-report-dialog__panel">
            <FeedbackDialog
              v-if="isConfirmDialogOpen"
              variant="confirm"
              title="Kirim laporan bug sekarang?"
              message="Laporan Anda akan dikirim ke panel admin untuk ditindaklanjuti. Pastikan data sudah sesuai sebelum dikirim."
              primary-label="Kirim Sekarang"
              secondary-label="Periksa Lagi"
              @primary="confirmSubmission"
              @secondary="closeConfirmDialog"
            />

            <FeedbackDialog
              v-else-if="isSuccessDialogOpen"
              variant="success"
              title="Laporan bug berhasil dikirim"
              message="Terima kasih atas laporannya! Tim kami akan segera meninjau dan memperbaiki masalah tersebut."
              primary-label="Selesai"
              @primary="closeSuccessDialog"
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

const form = useForm({
  email: '',
  subject: '',
  description: '',
  screenshot: null,
})

const page = usePage()

const screenshotPreview = ref(null)
const screenshotName = ref('')
const screenshotError = ref('')
const screenshotInput = ref(null)

const isConfirmDialogOpen = ref(false)
const isSuccessDialogOpen = ref(false)
const isDialogVisible = computed(() => isConfirmDialogOpen.value || isSuccessDialogOpen.value)

if (page.props.flash?.bug_report_submitted) {
  isSuccessDialogOpen.value = true
}

watch(
  () => page.props.flash?.bug_report_submitted,
  (newValue) => {
    if (newValue) {
      isSuccessDialogOpen.value = true
    }
  }
)

const resetScreenshot = () => {
  if (screenshotPreview.value) {
    URL.revokeObjectURL(screenshotPreview.value)
  }

  screenshotPreview.value = null
  screenshotName.value = ''
  form.screenshot = null

  if (screenshotInput.value) {
    screenshotInput.value.value = ''
  }
}

const clearScreenshot = () => {
  resetScreenshot()
  screenshotError.value = ''
}

const handleFileChange = (event) => {
  const target = event?.target ?? {}
  const files = target.files ?? []
  const [file] = files

  if (!file) {
    clearScreenshot()
    return
  }

  if (!file.type.startsWith('image/')) {
    screenshotError.value = 'File harus berupa gambar.'
    if ('value' in target) {
      target.value = ''
    }
    form.screenshot = null
    return
  }

  if (file.size > 4 * 1024 * 1024) {
    screenshotError.value = 'Ukuran gambar maksimal 4 MB.'
    if ('value' in target) {
      target.value = ''
    }
    form.screenshot = null
    return
  }

  screenshotError.value = ''
  form.screenshot = file
  screenshotName.value = file.name

  if (screenshotPreview.value) {
    URL.revokeObjectURL(screenshotPreview.value)
  }

  screenshotPreview.value = URL.createObjectURL(file)
}

const handleDrop = (event) => {
  const dataTransferFile = event.dataTransfer?.files?.[0]

  if (!dataTransferFile) {
    return
  }

  if (screenshotInput.value && typeof DataTransfer !== 'undefined') {
    const dataTransfer = new DataTransfer()
    dataTransfer.items.add(dataTransferFile)
    screenshotInput.value.files = dataTransfer.files
    handleFileChange({ target: screenshotInput.value })
  } else {
    const changeEvent = {
      target: {
        files: [dataTransferFile],
      },
    }
    handleFileChange(changeEvent)
  }
}

const handleSubmitIntent = () => {
  form.clearErrors()
  screenshotError.value = ''

  const trimmedSubject = form.subject?.toString().trim() ?? ''
  const trimmedDescription = form.description?.toString().trim() ?? ''
  const trimmedEmail = form.email?.toString().trim() ?? ''

  form.subject = trimmedSubject
  form.description = trimmedDescription
  form.email = trimmedEmail

  let hasClientErrors = false

  if (!trimmedSubject) {
    form.setError('subject', 'Subjek wajib diisi.')
    hasClientErrors = true
  }

  if (!trimmedDescription) {
    form.setError('description', 'Deskripsi wajib diisi.')
    hasClientErrors = true
  }

  if (trimmedEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
    form.setError('email', 'Format email tidak valid.')
    hasClientErrors = true
  }

  if (!form.screenshot) {
    screenshotError.value = 'Gambar bukti wajib diunggah.'
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

  form.post('/report-bug', {
    preserveScroll: true,
    forceFormData: true,
    onStart: () => {
      isConfirmDialogOpen.value = false
    },
    onSuccess: () => {
      form.reset()
      resetScreenshot()
      isSuccessDialogOpen.value = true
    },
    onError: () => {
      if (form.errors.screenshot) {
        screenshotError.value = form.errors.screenshot
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
})

onBeforeUnmount(() => {
  if (screenshotPreview.value) {
    URL.revokeObjectURL(screenshotPreview.value)
  }
  window.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped lang="scss">
@import '../../css/BugReport.scss';
</style>
