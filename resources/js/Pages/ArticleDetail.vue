<template>
  <MainLayout>
    <div class="article-detail-page">
      <template v-if="article">
        <section class="article-detail-hero" :style="heroStyle">
        <div class="article-detail-hero__overlay" ref="heroOverlay">
          <div class="article-detail-hero__content">
            <Link
              href="/artikel"
              class="article-detail-back"
              ref="heroBackButton"
            >
              <i class="fa-solid fa-arrow-left"></i>
              <span>Kembali</span>
            </Link>
            <h1 class="article-detail-title" ref="heroTitle">
              {{ article.title }}
            </h1>
            <p class="article-detail-date" ref="heroDate">
              {{ formattedDate }}
            </p>
          </div>
        </div>
      </section>

      <section class="article-detail-content">
        <article class="article-detail-card" ref="contentCard">
          <div class="article-detail-intro">
            <p
              v-for="(paragraph, index) in article.introduction"
              :key="`intro-${index}`"
              class="article-detail-paragraph"
            >
              {{ paragraph }}
            </p>
          </div>

          <div
            v-for="(section, index) in article.sections"
            :key="section.title"
            class="article-detail-section"
          >
            <h2 class="article-detail-section__title">
              {{ section.title }}
            </h2>
            <div class="article-detail-section__body">
              <template
                v-for="(block, blockIndex) in section.body"
                :key="`block-${index}-${blockIndex}`"
              >
                <p
                  v-if="block.type === 'paragraph'"
                  class="article-detail-paragraph"
                >
                  {{ block.text }}
                </p>

                <ul
                  v-else-if="block.type === 'list' && !block.ordered"
                  class="article-detail-list"
                >
                  <li
                    v-for="(item, itemIndex) in block.items"
                    :key="`list-${index}-${blockIndex}-${itemIndex}`"
                    class="article-detail-list__item"
                  >
                    {{ item }}
                  </li>
                </ul>

                <ol
                  v-else-if="block.type === 'list' && block.ordered"
                  class="article-detail-list article-detail-list--ordered"
                >
                  <li
                    v-for="(item, itemIndex) in block.items"
                    :key="`olist-${index}-${blockIndex}-${itemIndex}`"
                    class="article-detail-list__item"
                  >
                    {{ item }}
                  </li>
                </ol>
              </template>
            </div>
          </div>
        </article>
      </section>
      </template>
      <section v-else class="article-detail-missing">
        <div class="article-detail-missing__card">
          <h1>Artikel tidak ditemukan</h1>
          <p>
            Maaf, artikel yang Anda cari belum tersedia. Silakan kembali ke daftar artikel untuk membaca materi lainnya.
          </p>
          <Link href="/artikel" class="article-detail-back">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Artikel</span>
          </Link>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed, nextTick, onMounted, ref } from 'vue'
import MainLayout from '../Layouts/MainLayout.vue'
import { initializeArticleDetailAnimations } from '../animations/articleDetailAnimations'

const props = defineProps({
  slug: {
    type: String,
    required: true,
  },
})

const heroOverlay = ref(null)
const heroBackButton = ref(null)
const heroTitle = ref(null)
const heroDate = ref(null)
const contentCard = ref(null)

const asset = (path) => new URL(path, import.meta.url).href

const articles = {
  'amoksisilin-kapan-perlu-kapan-tidak': {
    title: 'Amoksisilin: Kapan Perlu, Kapan Tidak',
    date: '2025-08-12',
    heroImage: asset('../../images/articles/amoksisilin.jpg'),
    introduction: [
      'Amoksisilin adalah antibiotik golongan penisilin yang efektif melawan banyak infeksi bakteri. Tapi - dan ini penting - antibiotik tidak bekerja untuk penyakit yang disebabkan virus seperti flu, pilek, atau sebagian besar sakit tenggorokan. Menggunakan antibiotik saat tidak perlu tidak akan mempercepat sembuh dan justru bisa menimbulkan efek samping serta mempercepat terjadinya resistensi antibiotik.',
    ],
    sections: [
      {
        title: 'Mengapa bijak memakai antibiotik itu penting?',
        body: [
          {
            type: 'paragraph',
            text: 'WHO baru-baru ini menegaskan bahwa 1 dari 6 infeksi bakteri di dunia sudah resisten terhadap pengobatan antibiotik. Penyalahgunaan dan penggunaan berlebihan antibiotik adalah pemicunya.',
          },
        ],
      },
      {
        title: 'Amoksisilin dapat diresepkan dokter untuk infeksi bakteri tertentu, misalnya:',
        body: [
          {
            type: 'paragraph',
            text: 'Amoksisilin dapat diresepkan dokter untuk infeksi bakteri tertentu, misalnya:',
          },
          {
            type: 'list',
            ordered: false,
            items: [
              'Radang tenggorok bakteri (strep throat) setelah tes cepat/radang tenggorok kultur positif. Antibiotik memang diperlukan bila hasil tes positif; jangan mengobati sakit tenggorok virus dengan antibiotik.',
              'Sinusitis bakteri akut dengan ciri khas: gejala >10 hari tanpa membaik, demam tinggi & nyeri wajah purulen >= 3 hari, atau "double-sickening" (awal membaik lalu memburuk lagi). Kriteria ini membantu membedakan dari sinusitis virus yang tidak butuh antibiotik.',
              'Otitis media (infeksi telinga tengah), infeksi saluran napas bawah bakteri, infeksi kulit tertentu, atau infeksi gigi - sesuai penilaian klinis dokter dan hasil pemeriksaan. (Prinsip umum: pastikan ada bukti kuat infeksi bakteri, bukan virus.)',
            ],
          },
        ],
      },
      {
        title: 'Tanda-tanda yang Perlu Dievaluasi Dokter',
        body: [
          {
            type: 'paragraph',
            text: 'Segera konsultasi bila Anda mengalami salah satu dari berikut:',
          },
          {
            type: 'list',
            ordered: false,
            items: [
              'Demam tinggi, sesak napas, nyeri telinga hebat, nyeri wajah purulen, atau gejala >10 hari tanpa membaik.',
              'Radang tenggorok disertai pembesaran kelenjar leher, demam, tidak ada batuk - dokter dapat menilai dan melakukan tes strep bila perlu.',
            ],
          },
        ],
      },
      {
        title: 'Cara Pakai yang Benar',
        body: [
          {
            type: 'paragraph',
            text: 'Gunakan hanya dengan resep dokter dan ikuti aturan pakai dengan tepat. Di Indonesia, amoksisilin termasuk obat keras - pembeliannya harus di apotek dengan resep.',
          },
          {
            type: 'list',
            ordered: true,
            items: [
              'Ikuti jadwal minum sesuai anjuran dokter dan jangan menggandakan dosis bila lupa.',
              'Habiskan sesuai durasi resep (jangan berhenti mendadak tanpa saran tenaga kesehatan), meski gejala terasa membaik. Ini membantu mencegah kambuh dan resistensi.',
              'Jangan berbagi obat, menyimpan sisa untuk sakit lain, atau mengulang resep lama tanpa evaluasi.',
            ],
          },
        ],
      },
      {
        title: 'Efek Samping yang Perlu Diwaspadai',
        body: [
          {
            type: 'paragraph',
            text: 'Efek yang umum: mual, diare, ruam, pusing, infeksi jamur.',
          },
          {
            type: 'paragraph',
            text: 'Efek yang jarang tetapi serius: alergi berat (anafilaksis) dan infeksi Clostridioides difficile yang menimbulkan diare berat - butuh pertolongan medis segera. Jika Anda mengalami diare berat atau darah/lendir pada tinja selama atau setelah minum antibiotik, segera hubungi tenaga kesehatan.',
          },
        ],
      },
      {
        title: 'Peringatan & Interaksi Obat',
        body: [
          {
            type: 'paragraph',
            text: 'Sebelum minum amoksisilin, beri tahu dokter/apoteker bila Anda:',
          },
          {
            type: 'list',
            ordered: false,
            items: [
              'Alergi penisilin atau pernah mengalami reaksi alergi obat.',
              'Memiliki penyakit ginjal atau sedang hamil/menyusui (umumnya aman bila diresepkan, tetapi tetap perlu penilaian).',
              'Mengonsumsi obat tertentu - misalnya methotrexate (risiko peningkatan toksisitas) atau pengencer darah seperti warfarin (kadang dapat memengaruhi INR, perlu pemantauan).',
            ],
          },
        ],
      },
    ],
  },
}

const article = computed(() => articles[props.slug] ?? null)

const formattedDate = computed(() => {
  if (!article.value?.date) {
    return ''
  }

  const date = new Date(article.value.date)
  if (Number.isNaN(date.getTime())) {
    return article.value.date
  }

  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(date)
})

const heroStyle = computed(() => {
  if (!article.value?.heroImage) {
    return {}
  }

  return {
    '--hero-image': `url(${article.value.heroImage})`,
  }
})

onMounted(() => {
  if (!article.value) {
    return
  }

  nextTick(() => {
    initializeArticleDetailAnimations({
      heroOverlay: heroOverlay.value,
      heroTitle: heroTitle.value,
      heroDate: heroDate.value,
      heroBackButton: heroBackButton.value,
      contentCard: contentCard.value,
    })
  })
})
</script>

<style lang="scss" scoped>
@import '../../css/ArticleDetail.scss';
</style>
