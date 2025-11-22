<template>
  <div class="surah-detail min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50 p-6">
    <!-- Loading State -->
    <div v-if="quranStore.loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-emerald-500 border-t-transparent"></div>
      <p class="mt-4 text-gray-600">جاري تحميل السورة...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="quranStore.error" class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg">
      {{ quranStore.error }}
      <router-link to="/surahs" class="block mt-4 text-emerald-600 hover:text-emerald-700">
        العودة إلى قائمة السور
      </router-link>
    </div>

    <!-- Surah Content -->
    <div v-else-if="surah">
      <!-- Surah Header -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <router-link
            to="/surahs"
            class="flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition"
          >
            <span class="text-xl">←</span>
            <span>رجوع</span>
          </router-link>

          <div class="flex gap-2">
            <button
              @click="toggleBookmark"
              class="px-4 py-2 rounded-lg transition"
              :class="isBookmarked ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
            >
              {{ isBookmarked ? '⭐' : '☆' }} حفظ
            </button>
          </div>
        </div>

        <div class="text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full text-white text-2xl font-bold mb-4 shadow-lg">
            {{ surah.number }}
          </div>
          <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ surah.name_arabic }}</h1>
          <p class="text-xl text-gray-600 mb-4">{{ surah.name_english }}</p>

          <div class="flex items-center justify-center gap-6 text-sm text-gray-600">
            <div class="flex items-center gap-2">
              <span class="text-lg">📄</span>
              <span>{{ surah.verses_count }} آية</span>
            </div>
            <div class="flex items-center gap-2">
              <span :class="[
                'px-3 py-1 rounded-full text-xs font-semibold',
                surah.revelation_place === 'مكة' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'
              ]">
                {{ surah.revelation_place === 'مكة' ? 'مكية' : 'مدنية' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Basmala -->
        <div v-if="surah.number !== 1 && surah.number !== 9" class="text-center mt-6 pt-6 border-t border-gray-200">
          <p class="text-3xl text-emerald-700 font-arabic leading-loose">
            بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
          </p>
        </div>
      </div>

      <!-- Hifz Controls -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">إدارة الحفظ</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">من الآية</label>
            <input
              v-model.number="hifzRange.start"
              type="number"
              min="1"
              :max="surah.verses_count"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى الآية</label>
            <input
              v-model.number="hifzRange.end"
              type="number"
              :min="hifzRange.start"
              :max="surah.verses_count"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <div class="flex items-end">
            <button
              @click="startHifz"
              :disabled="hifzStore.loading || !isValidRange"
              class="w-full px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
              {{ hifzStore.loading ? 'جاري الحفظ...' : 'بدء الحفظ' }}
            </button>
          </div>
        </div>

        <div v-if="hifzMessage" class="mt-4 p-3 rounded-lg" :class="hifzMessageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
          {{ hifzMessage }}
        </div>
      </div>

      <!-- Ayahs List -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-800">الآيات</h2>

          <div class="flex gap-2">
            <button
              @click="fontSize = Math.max(fontSize - 2, 16)"
              class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
            >
              أ-
            </button>
            <button
              @click="fontSize = Math.min(fontSize + 2, 32)"
              class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
            >
              أ+
            </button>
          </div>
        </div>

        <div v-if="ayahs.length === 0" class="text-center py-8 text-gray-500">
          <div class="text-5xl mb-4">📖</div>
          <p>لا توجد آيات</p>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="ayah in ayahs"
            :key="ayah.id"
            class="group relative p-6 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 hover:border-emerald-300 transition-all duration-300"
            :class="{'ring-2 ring-emerald-500 bg-emerald-50': isAyahInHifzRange(ayah.ayah_number)}"
          >
            <!-- Ayah Number Badge -->
            <div class="absolute top-4 left-4 flex items-center justify-center w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-full text-sm font-bold shadow-md">
              {{ ayah.ayah_number }}
            </div>

            <!-- Ayah Text -->
            <div class="pr-14">
              <p
                class="text-gray-800 font-arabic leading-loose text-right mb-4"
                :style="{ fontSize: fontSize + 'px' }"
                dir="rtl"
              >
                {{ ayah.text_arabic }}
                <span class="inline-block mr-2 text-emerald-600">﴿{{ convertToArabicNumerals(ayah.ayah_number) }}﴾</span>
              </p>

              <!-- Translation (if available) -->
              <p v-if="ayah.translation" class="text-gray-600 text-sm leading-relaxed" dir="rtl">
                {{ ayah.translation }}
              </p>
            </div>

            <!-- Ayah Actions -->
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity">
              <button
                @click="copyAyah(ayah)"
                class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition"
              >
                📋 نسخ
              </button>
              <button
                @click="shareAyah(ayah)"
                class="px-3 py-1 text-sm bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition"
              >
                🔗 مشاركة
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuranStore } from './store/quran'
import { useHifzStore } from './store/hifz'

const route = useRoute()
const router = useRouter()
const quranStore = useQuranStore()
const hifzStore = useHifzStore()

const surah = computed(() => quranStore.currentSurah)
const ayahs = computed(() => quranStore.ayahs)

const fontSize = ref(24)
const isBookmarked = ref(false)
const hifzRange = ref({ start: 1, end: 1 })
const hifzMessage = ref('')
const hifzMessageType = ref('success')

const isValidRange = computed(() => {
  return hifzRange.value.start > 0 &&
         hifzRange.value.end >= hifzRange.value.start &&
         hifzRange.value.end <= (surah.value?.verses_count || 0)
})

const isAyahInHifzRange = (ayahNumber) => {
  return ayahNumber >= hifzRange.value.start && ayahNumber <= hifzRange.value.end
}

const toggleBookmark = () => {
  isBookmarked.value = !isBookmarked.value
  // TODO: Save bookmark to localStorage or backend
  const message = isBookmarked.value ? 'تم حفظ السورة في المفضلة' : 'تم إزالة السورة من المفضلة'
  showMessage(message, 'success')
}

const startHifz = async () => {
  if (!isValidRange.value) {
    showMessage('الرجاء اختيار نطاق صحيح من الآيات', 'error')
    return
  }

  try {
    await hifzStore.startHifz(
      surah.value.id,
      hifzRange.value.start,
      hifzRange.value.end
    )
    showMessage('تم بدء الحفظ بنجاح! يمكنك متابعة تقدمك من صفحة الحفظ', 'success')
  } catch (error) {
    showMessage(error.response?.data?.message || 'حدث خطأ أثناء بدء الحفظ', 'error')
  }
}

const copyAyah = async (ayah) => {
  const text = `${ayah.text_arabic}\n﴿${surah.value.name_arabic} - ${ayah.ayah_number}﴾`
  try {
    await navigator.clipboard.writeText(text)
    showMessage('تم نسخ الآية بنجاح', 'success')
  } catch (error) {
    showMessage('فشل نسخ الآية', 'error')
  }
}

const shareAyah = async (ayah) => {
  const text = `${ayah.text_arabic}\n﴿${surah.value.name_arabic} - ${ayah.ayah_number}﴾`

  if (navigator.share) {
    try {
      await navigator.share({ text })
      showMessage('تم المشاركة بنجاح', 'success')
    } catch (error) {
      if (error.name !== 'AbortError') {
        copyAyah(ayah)
      }
    }
  } else {
    copyAyah(ayah)
  }
}

const convertToArabicNumerals = (num) => {
  const arabicNumerals = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩']
  return String(num).split('').map(digit => arabicNumerals[parseInt(digit)]).join('')
}

const showMessage = (message, type = 'success') => {
  hifzMessage.value = message
  hifzMessageType.value = type
  setTimeout(() => {
    hifzMessage.value = ''
  }, 5000)
}

onMounted(async () => {
  const surahId = route.params.id

  try {
    await quranStore.fetchSurahByNumber(surahId)

    if (surah.value) {
      hifzRange.value.end = surah.value.verses_count

      // Check if surah is bookmarked
      const bookmarks = JSON.parse(localStorage.getItem('quran_bookmarks') || '[]')
      isBookmarked.value = bookmarks.includes(surah.value.id)
    }
  } catch (error) {
    console.error('Error loading surah:', error)
  }
})
</script>

<style scoped>
.font-arabic {
  font-family: 'Amiri', 'Traditional Arabic', 'Arabic Typesetting', serif;
  font-weight: 400;
}
</style>
