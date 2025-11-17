<template>
  <MainLayout>
    <div class="chatbot-page">
      <section class="chatbot-hero" aria-labelledby="chatbot-title">
        <div class="chatbot-hero__content">
          <h1 id="chatbot-title">Tiarana Health Assistant</h1>
          <p>
            Konsultasikan pertanyaan seputar kesehatan, obat-obatan, dan layanan apotek secara instan.
          </p>
        </div>
      </section>

      <section class="chatbot-shell" :class="{ 'chatbot-shell--full': !isAuthenticated }">
        <aside
          v-if="isAuthenticated"
          class="chatbot-sidebar"
          aria-label="Riwayat percakapan"
        >
          <header class="chatbot-sidebar__header">
            <h2>Riwayat Anda</h2>
            <Button
              size="sm"
              variant="primary"
              :disabled="isLoading || isLoadingConversation"
              @click="startNewConversation"
            >
              <template #icon>
                <i class="fa fa-plus" aria-hidden="true"></i>
              </template>
              Percakapan Baru
            </Button>
          </header>

          <div class="chatbot-sidebar__list" role="list">
            <button
              v-for="conversation in conversations"
              :key="conversation.id"
              type="button"
              class="chatbot-sidebar__item"
              :class="{ 'chatbot-sidebar__item--active': conversation.id === conversationId }"
              :disabled="isLoadingConversation && conversation.id !== conversationId"
              @click="loadConversation(conversation.id)"
            >
              <span class="chatbot-sidebar__title">
                {{ conversation.title || 'Percakapan tanpa judul' }}
              </span>
              <span class="chatbot-sidebar__time">
                {{ formatConversationTimestamp(conversation.last_interacted_at) }}
              </span>
            </button>
            <p
              v-if="!conversations.length && !isLoadingConversation"
              class="chatbot-sidebar__empty"
            >
              Belum ada percakapan tersimpan. Mulai chat dan riwayat Anda akan muncul di sini.
            </p>
          </div>
        </aside>

        <div class="chatbot-main">
          <header class="chatbot-main__header">
            <div class="chatbot-main__title">
              <i class="fa fa-stethoscope" aria-hidden="true"></i>
              <div>
                <h2>Chat dengan Apoteker Virtual</h2>
                <p>
                  Tanyakan gejala, interaksi obat, atau tips gaya hidup sehat. Untuk kasus darurat,
                  segera hubungi tenaga medis terdekat.
                </p>
              </div>
            </div>
            <div
              v-if="!isAuthenticated"
              class="chatbot-main__auth-hint"
              role="note"
            >
              <i class="fa fa-circle-info" aria-hidden="true"></i>
              Percakapan Anda bersifat privat dan tidak akan bocor ke pihak mana pun.
            </div>
          </header>

          <div class="chatbot-messages" ref="messageContainerRef" role="log" aria-live="polite">
            <div
              v-for="message in messages"
              :key="message.id"
              class="chat-message"
              :class="[
                `chat-message--${message.role}`,
                { 'chat-message--error': message.isError },
              ]"
            >
              <div class="chat-message__avatar" aria-hidden="true">
                <i
                  :class="message.role === 'user' ? 'fa fa-user' : 'fa fa-robot'"
                ></i>
              </div>
              <div class="chat-message__bubble">
                <p class="chat-message__text">
                  {{ message.content }}
                </p>

                <div
                  v-if="
                    message.metadata &&
                    message.metadata.intent === 'medication-recommendation' &&
                    message.metadata.medication
                  "
                  class="chat-message__medication-card"
                >
                  <div
                    v-if="message.metadata.medication.image_url"
                    class="chat-message__medication-image"
                  >
                    <img
                      :src="message.metadata.medication.image_url"
                      :alt="`Foto ${message.metadata.medication.name}`"
                      loading="lazy"
                    />
                  </div>
                  <div class="chat-message__medication-body">
                    <p class="chat-message__medication-name">
                      {{ message.metadata.medication.name }}
                    </p>
                    <p class="chat-message__medication-form">
                      {{ message.metadata.medication.category }} •
                      {{ message.metadata.medication.form }}
                    </p>
                    <p v-if="message.metadata.medication.stock_status" class="chat-message__medication-stock">
                      {{ message.metadata.medication.stock_status }}
                    </p>

                    <p v-if="message.metadata.medication.how_it_works" class="chat-message__medication-desc">
                      {{ message.metadata.medication.how_it_works }}
                    </p>

                    <div
                      v-if="message.metadata.medication.dosage && Object.keys(message.metadata.medication.dosage).length"
                      class="chat-message__medication-section"
                    >
                      <p class="chat-message__medication-section-title">Aturan Pakai</p>
                      <ul class="chat-message__medication-list">
                        <li v-for="(value, label) in message.metadata.medication.dosage" :key="label">
                          <strong>{{ label }}:</strong> {{ value }}
                        </li>
                      </ul>
                    </div>

                    <div
                      v-if="message.metadata.medication.warnings && message.metadata.medication.warnings.length"
                      class="chat-message__medication-section"
                    >
                      <p class="chat-message__medication-section-title">Perhatian</p>
                      <ul class="chat-message__medication-list chat-message__medication-list--warning">
                        <li v-for="(warning, index) in message.metadata.medication.warnings" :key="index">
                          {{ warning }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div
                  v-if="message.metadata && message.metadata.cta && message.metadata.cta.url"
                  class="chat-message__cta"
                >
                  <Button :href="message.metadata.cta.url" size="sm">
                    {{ message.metadata.cta.label || 'Hubungi Apoteker' }}
                  </Button>
                </div>
              </div>
            </div>

            <div
              v-if="isLoading"
              class="chat-message chat-message--assistant chat-message--typing"
            >
              <div class="chat-message__avatar" aria-hidden="true">
                <i class="fa fa-robot"></i>
              </div>
              <div class="chat-message__bubble">
                <span class="typing-indicator">
                  <span></span>
                  <span></span>
                  <span></span>
                </span>
              </div>
            </div>
          </div>

          <form class="chatbot-input" @submit.prevent="sendMessage">
            <label class="chatbot-input__field">
              <span class="sr-only">Pesan untuk chatbot</span>
              <textarea
                v-model="inputMessage"
                :disabled="isLoading"
                rows="3"
                placeholder="Tuliskan pertanyaan kesehatan Anda di sini..."
                @keydown.enter="handleEnterKey"
              ></textarea>
            </label>

            <div class="chatbot-input__actions">
              <span class="chatbot-input__hint">Gunakan Shift + Enter untuk baris baru</span>
              <Button
                type="submit"
                size="md"
                variant="primary"
                :disabled="!canSend"
              >
                <template #icon>
                  <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </template>
                Kirim
              </Button>
            </div>
          </form>

          <p v-if="errorMessage" class="chatbot-error" role="alert">
            {{ errorMessage }}
          </p>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { computed, nextTick, onMounted, ref, watch } from 'vue'
import Button from '../Components/Button.vue'
import MainLayout from '../Layouts/MainLayout.vue'

const page = usePage()
const isAuthenticated = computed(() => Boolean(page.props.auth?.user))

const welcomeMessage = {
  id: 'welcome',
  role: 'assistant',
  content:
    'Halo, saya asisten virtual Tiarana Pharmacy. Ceritakan gejala atau pertanyaan seputar kesehatan, obat, dan gaya hidup. Untuk kondisi darurat, segera hubungi tenaga medis.',
  metadata: {},
}

const messages = ref([welcomeMessage])
const conversations = ref([])
const conversationId = ref(null)
const inputMessage = ref('')
const isLoading = ref(false)
const isLoadingConversation = ref(false)
const errorMessage = ref('')
const messageContainerRef = ref(null)

const canSend = computed(() => inputMessage.value.trim().length > 0 && !isLoading.value)

const scrollToBottom = async () => {
  await nextTick()
  const container = messageContainerRef.value
  if (container) {
    container.scrollTop = container.scrollHeight
  }
}

watch(
  () => messages.value.length,
  () => {
    scrollToBottom()
  }
)

const conversationStorageKey = 'chatbotConversationId'

const readStoredConversationId = () => {
  if (typeof window === 'undefined') {
    return null
  }

  return window.sessionStorage.getItem(conversationStorageKey)
}

const writeStoredConversationId = (id) => {
  if (typeof window === 'undefined') {
    return
  }

  if (!id) {
    window.sessionStorage.removeItem(conversationStorageKey)
    return
  }

  window.sessionStorage.setItem(conversationStorageKey, String(id))
}

const deleteConversationOnServer = async (id) => {
  if (!id) {
    return false
  }

  try {
    await axios.delete(`/api/chatbot/conversations/${id}`)
    return true
  } catch (error) {
    if (error.response?.status === 404) {
      return true
    }

    console.error('Gagal menghapus percakapan', error)
    return false
  }
}

const clearStoredConversation = async () => {
  const storedId = readStoredConversationId()

  if (!storedId) {
    return
  }

  const wasDeleted = await deleteConversationOnServer(storedId)

  if (wasDeleted) {
    writeStoredConversationId(null)
  }
}

const discardActiveConversation = async () => {
  const conversationIds = new Set()

  if (conversationId.value) {
    conversationIds.add(String(conversationId.value))
  }

  const storedId = readStoredConversationId()
  if (storedId) {
    conversationIds.add(storedId)
  }

  conversationId.value = null

  for (const id of conversationIds) {
    await deleteConversationOnServer(id)
  }

  writeStoredConversationId(null)
}

const formatConversationTimestamp = (value) => {
  if (!value) {
    return ''
  }

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) {
    return ''
  }

  return new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'short',
    timeStyle: 'short',
  }).format(date)
}

const resetState = () => {
  messages.value = [welcomeMessage]
  conversationId.value = null
  errorMessage.value = ''
  inputMessage.value = ''
}

const startNewConversation = async () => {
  if (isLoading.value || isLoadingConversation.value) {
    return
  }

  await discardActiveConversation()
  resetState()

  await fetchConversations()
}

const fetchConversations = async () => {
  if (!isAuthenticated.value) {
    return
  }

  try {
    isLoadingConversation.value = true
    const { data } = await axios.get('/api/chatbot/conversations')
    conversations.value = data?.data ?? []
  } catch (error) {
    console.error('Gagal memuat riwayat percakapan', error)
  } finally {
    isLoadingConversation.value = false
  }
}

const loadConversation = async (id) => {
  if (!isAuthenticated.value || !id) {
    return
  }

  if (conversationId.value === id && messages.value.length > 1) {
    return
  }

  try {
    isLoadingConversation.value = true
    errorMessage.value = ''
    const { data } = await axios.get(`/api/chatbot/conversations/${id}`)
    const loadedMessages = data?.data?.messages ?? []

    if (loadedMessages.length === 0) {
      messages.value = [welcomeMessage]
    } else {
      messages.value = loadedMessages.map((message) => ({
        id: `conversation-${id}-${message.id}`,
        role: message.role === 'model' ? 'assistant' : message.role,
        content: message.content,
        metadata: message.metadata ?? {},
        created_at: message.created_at,
      }))
    }

    conversationId.value = id
  } catch (error) {
    errorMessage.value = 'Tidak dapat memuat percakapan. Coba lagi beberapa saat.'
  } finally {
    isLoadingConversation.value = false
    scrollToBottom()
  }
}

const handleEnterKey = (event) => {
  if (event.shiftKey) {
    return
  }

  event.preventDefault()

  if (canSend.value) {
    sendMessage()
  }
}

const sendMessage = async () => {
  const trimmedMessage = inputMessage.value.trim()

  if (!trimmedMessage || isLoading.value) {
    return
  }

  const userMessage = {
    id: `user-${Date.now()}`,
    role: 'user',
    content: trimmedMessage,
    created_at: new Date().toISOString(),
    metadata: {},
  }

  messages.value = [...messages.value, userMessage]
  inputMessage.value = ''
  errorMessage.value = ''
  isLoading.value = true

  try {
    const payloadMessages = messages.value
      .filter((message) => message.role === 'user' || message.role === 'assistant')
      .map((message) => ({
        role: message.role,
        content: message.content,
      }))

    const { data } = await axios.post('/api/chatbot/message', {
      messages: payloadMessages,
      conversation_id: conversationId.value,
    })

    const assistantPayload = data?.data?.message ?? null
    const reply = assistantPayload?.content ?? data?.data?.reply
    const metadata = assistantPayload?.metadata ?? {}

    if (reply) {
      const assistantMessage = {
        id: `assistant-${Date.now()}`,
        role: 'assistant',
        content: reply,
        created_at: new Date().toISOString(),
        metadata,
      }

      messages.value = [...messages.value, assistantMessage]
    }

    if (data?.data?.conversation_id) {
      const newConversationId = data.data.conversation_id
      const isNewConversation = !conversationId.value

      conversationId.value = newConversationId
      writeStoredConversationId(newConversationId)

      if (isAuthenticated.value && isNewConversation) {
        await fetchConversations()
      } else if (isAuthenticated.value) {
        fetchConversations()
      }
    }
  } catch (error) {
    console.error('Kesalahan saat mengirim pesan', error)
    errorMessage.value =
      error.response?.data?.message ??
      'Asisten sedang sibuk. Silakan coba lagi beberapa saat.'

    messages.value = [
      ...messages.value,
      {
        id: `system-${Date.now()}`,
        role: 'system',
        content: 'Maaf, terjadi kesalahan saat menghubungi asisten. Coba kirim ulang pertanyaan Anda.',
        created_at: new Date().toISOString(),
        isError: true,
        metadata: {},
      },
    ]
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  await clearStoredConversation()
  await fetchConversations()
  scrollToBottom()
})
</script>

<style scoped lang="scss">
@import '../../css/Chatbot.scss';
</style>
