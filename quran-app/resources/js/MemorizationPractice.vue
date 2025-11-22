<template>
  <div class="memorization-practice min-h-screen bg-gradient-to-br from-teal-50 to-cyan-50 p-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <router-link
            to="/hifz"
            class="w-10 h-10 bg-teal-100 hover:bg-teal-200 rounded-full flex items-center justify-center transition"
          >
            <span class="text-xl">→</span>
          </router-link>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">تدريب الحفظ التفاعلي</h1>
            <p class="text-gray-600 mt-1">اضغط على الكلمات المخفية لإظهارها وتقييم حفظك</p>
          </div>
        </div>
        <div class="text-5xl">📖</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-teal-500 border-t-transparent"></div>
      <p class="mt-4 text-gray-600">جاري التحميل...</p>
    </div>

    <!-- Select Mode -->
    <div v-else-if="!selectedMode" class="bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">اختر طريقة التدريب</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
        <!-- By Surah -->
        <button
          @click="selectedMode = 'surah'"
          class="p-8 bg-gradient-to-br from-teal-50 to-white rounded-2xl border-2 border-teal-200 hover:border-teal-400 hover:shadow-xl transition-all group"
        >
          <div class="text-6xl mb-4">📚</div>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">حسب السورة</h3>
          <p class="text-gray-600">اختر سورة محددة للتدرب على حفظها</p>
        </button>

        <!-- By Page -->
        <button
          @click="selectedMode = 'page'"
          class="p-8 bg-gradient-to-br from-cyan-50 to-white rounded-2xl border-2 border-cyan-200 hover:border-cyan-400 hover:shadow-xl transition-all group"
        >
          <div class="text-6xl mb-4">📄</div>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">حسب الصفحة</h3>
          <p class="text-gray-600">اختر صفحة من المصحف للتدرب عليها</p>
        </button>
      </div>
    </div>

    <!-- Select Surah -->
    <div v-else-if="selectedMode === 'surah' && !selectedSource" class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">اختر السورة</h2>
        <button
          @click="selectedMode = null"
          class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
        >
          رجوع
        </button>
      </div>

      <div v-if="availableHifz.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">📚</div>
        <p class="text-lg text-gray-600 mb-4">لا توجد سور محفوظة للتدرب عليها</p>
        <router-link
          to="/hifz"
          class="inline-block px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition"
        >
          العودة إلى الحفظ
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <button
          v-for="hifz in availableHifz"
          :key="hifz.id"
          @click="selectSurah(hifz)"
          class="p-5 bg-gradient-to-br from-teal-50 to-white rounded-xl border-2 border-teal-200 hover:border-teal-400 hover:shadow-lg transition text-right"
        >
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center text-white font-bold">
              {{ hifz.surah?.number }}
            </div>
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-800">{{ hifz.surah?.name_arabic }}</h3>
              <p class="text-sm text-gray-600">
                من الآية {{ hifz.start_ayah }} إلى {{ hifz.end_ayah }}
                ({{ hifz.end_ayah - hifz.start_ayah + 1 }} آية)
              </p>
            </div>
          </div>
        </button>
      </div>
    </div>

    <!-- Select Page -->
    <div v-else-if="selectedMode === 'page' && !selectedSource" class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">اختر رقم الصفحة</h2>
        <button
          @click="selectedMode = null"
          class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
        >
          رجوع
        </button>
      </div>

      <div class="max-w-md mx-auto">
        <label class="block text-gray-700 font-semibold mb-2">رقم الصفحة (1-604)</label>
        <div class="flex gap-3">
          <input
            v-model.number="pageNumber"
            type="number"
            min="1"
            max="604"
            class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:outline-none text-lg"
            placeholder="أدخل رقم الصفحة"
            @keyup.enter="selectPage"
          />
          <button
            @click="selectPage"
            :disabled="!pageNumber || pageNumber < 1 || pageNumber > 604"
            class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-semibold"
          >
            ابدأ
          </button>
        </div>
      </div>
    </div>

    <!-- Practice Interface -->
    <div v-else-if="ayahs.length > 0">
      <!-- Stats Bar -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-teal-600">{{ currentAyahIndex + 1 }} / {{ ayahs.length }}</div>
            <div class="text-xs text-gray-600">الآية الحالية</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-gray-800">{{ totalWords }}</div>
            <div class="text-xs text-gray-600">إجمالي الكلمات</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ correctCount }}</div>
            <div class="text-xs text-gray-600">صحيحة</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-red-600">{{ wrongCount }}</div>
            <div class="text-xs text-gray-600">خاطئة</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ accuracy }}%</div>
            <div class="text-xs text-gray-600">نسبة الدقة</div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
          <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div
              class="bg-gradient-to-r from-teal-500 to-cyan-500 h-full transition-all duration-500"
              :style="{ width: progressPercentage + '%' }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Ayah Display -->
      <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <div class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <span class="px-4 py-2 bg-teal-100 text-teal-700 rounded-full text-sm font-semibold">
              {{ selectedMode === 'surah' ? selectedSource.surah?.name_arabic : `الصفحة ${pageNumber}` }}
              - الآية {{ currentAyah.number }}
            </span>
            <div class="flex gap-2">
              <button
                @click="toggleShowAll"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm"
              >
                {{ showAllWords ? '🙈 إخفاء الكل' : '👁️ إظهار الكل' }}
              </button>
            </div>
          </div>

          <!-- Words Display -->
          <div class="text-3xl leading-loose text-right font-arabic" dir="rtl">
            <span
              v-for="(word, index) in currentAyahWords"
              :key="index"
              class="inline-block mx-1 my-1"
            >
              <!-- Hidden Word -->
              <span
                v-if="!word.revealed && !showAllWords"
                @click="revealWord(index)"
                class="inline-block min-w-[80px] px-4 py-2 bg-gradient-to-br from-teal-100 to-teal-50 border-2 border-teal-300 border-dashed rounded-lg cursor-pointer hover:from-teal-200 hover:to-teal-100 hover:shadow-lg transition-all transform hover:scale-105"
              >
                <span class="text-gray-400 text-xl">؟؟؟</span>
              </span>

              <!-- Revealed Word - Awaiting Feedback -->
              <span
                v-else-if="word.revealed && word.feedback === null"
                class="inline-block relative group"
              >
                <span class="text-gray-800 font-semibold px-2">{{ word.text }}</span>
                <div class="absolute -top-14 left-1/2 transform -translate-x-1/2 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity bg-white rounded-lg shadow-xl p-2 z-10">
                  <button
                    @click="markWord(index, true)"
                    class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-lg transition flex items-center justify-center text-xl"
                    title="صحيح"
                  >
                    ✓
                  </button>
                  <button
                    @click="markWord(index, false)"
                    class="w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-lg transition flex items-center justify-center text-xl"
                    title="خطأ"
                  >
                    ✗
                  </button>
                </div>
              </span>

              <!-- Word with Feedback -->
              <span
                v-else
                :class="[
                  'inline-block px-3 py-1 rounded-lg font-semibold',
                  word.feedback === true ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
                @click="word.feedback !== null && resetWord(index)"
              >
                {{ word.text }}
                <span class="text-sm ml-1">{{ word.feedback === true ? '✓' : '✗' }}</span>
              </span>
            </span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 mt-8">
          <button
            v-if="currentAyahIndex > 0"
            @click="previousAyah"
            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold"
          >
            ← الآية السابقة
          </button>
          <button
            v-if="currentAyahIndex < ayahs.length - 1"
            @click="nextAyah"
            class="flex-1 px-6 py-3 bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-lg hover:from-teal-600 hover:to-cyan-600 transition font-semibold shadow-lg"
          >
            الآية التالية →
          </button>
          <button
            v-else
            @click="finishPractice"
            class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg hover:from-green-600 hover:to-emerald-600 transition font-semibold shadow-lg"
          >
            إنهاء التدريب ✓
          </button>
          <button
            @click="resetCurrentAyah"
            class="px-6 py-3 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition font-semibold"
          >
            🔄 إعادة
          </button>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">
              👁️
            </div>
            <div>
              <div class="text-2xl font-bold text-gray-800">{{ revealedCount }}</div>
              <div class="text-sm text-gray-600">كلمات مكشوفة</div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-2xl">
              🔒
            </div>
            <div>
              <div class="text-2xl font-bold text-gray-800">{{ hiddenCount }}</div>
              <div class="text-sm text-gray-600">كلمات مخفية</div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-2xl">
              ⏱️
            </div>
            <div>
              <div class="text-2xl font-bold text-gray-800">{{ ratedCount }}</div>
              <div class="text-sm text-gray-600">كلمات مُقيّمة</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Results Modal -->
    <div
      v-if="showResults"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click.self="closeResults"
    >
      <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full animate-fade-in">
        <div class="text-center">
          <div class="text-6xl mb-4">
            {{ accuracy >= 90 ? '🎉' : accuracy >= 70 ? '👍' : '💪' }}
          </div>
          <h2 class="text-3xl font-bold text-gray-800 mb-2">
            {{ accuracy >= 90 ? 'ممتاز جداً!' : accuracy >= 70 ? 'أحسنت!' : 'استمر في التدريب!' }}
          </h2>
          <p class="text-gray-600 mb-6">أكملت التدريب على {{ ayahs.length }} آية</p>

          <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="text-2xl font-bold text-gray-800">{{ totalWords }}</div>
              <div class="text-xs text-gray-600">كلمات</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
              <div class="text-2xl font-bold text-green-600">{{ correctCount }}</div>
              <div class="text-xs text-gray-600">صحيحة</div>
            </div>
            <div class="bg-red-50 rounded-lg p-4">
              <div class="text-2xl font-bold text-red-600">{{ wrongCount }}</div>
              <div class="text-xs text-gray-600">خاطئة</div>
            </div>
          </div>

          <div class="mb-6">
            <div class="text-5xl font-bold text-teal-600 mb-2">{{ accuracy }}%</div>
            <p class="text-gray-600">نسبة الدقة</p>
          </div>

          <div class="flex gap-3">
            <button
              @click="retryPractice"
              class="flex-1 px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition"
            >
              🔄 إعادة التدريب
            </button>
            <button
              @click="selectNewSource"
              class="flex-1 px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition"
            >
              ➕ تدريب جديد
            </button>
          </div>
          <button
            @click="goToHifz"
            class="w-full mt-3 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
          >
            العودة إلى الحفظ
          </button>
        </div>
      </div>
    </div>

    <!-- Feedback Messages -->
    <div
      v-if="feedbackMessage"
      :class="[
        'fixed bottom-6 right-6 px-6 py-4 rounded-lg shadow-lg animate-fade-in',
        feedbackType === 'success' ? 'bg-green-500' : 'bg-red-500',
        'text-white'
      ]"
    >
      {{ feedbackMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useHifzStore } from './store/hifz'
import { useRouter } from 'vue-router'
import axios from 'axios'

const hifzStore = useHifzStore()
const router = useRouter()

const loading = ref(true)
const selectedMode = ref(null) // 'surah' or 'page'
const selectedSource = ref(null) // hifz object for surah mode
const pageNumber = ref(null)
const ayahs = ref([])
const currentAyahIndex = ref(0)
const showAllWords = ref(false)
const showResults = ref(false)
const feedbackMessage = ref('')
const feedbackType = ref('success')

const availableHifz = computed(() => {
  return hifzStore.userHifz.filter(h =>
    (h.status === 'completed' || h.status === 'in_progress') &&
    h.memorized_count > 0
  )
})

const currentAyah = computed(() => {
  return ayahs.value[currentAyahIndex.value] || {}
})

const currentAyahWords = computed(() => {
  return currentAyah.value.words || []
})

const totalWords = computed(() => {
  return ayahs.value.reduce((sum, ayah) => sum + (ayah.words?.length || 0), 0)
})

const revealedCount = computed(() => {
  return ayahs.value.reduce((sum, ayah) =>
    sum + (ayah.words?.filter(w => w.revealed).length || 0), 0)
})

const hiddenCount = computed(() => {
  return totalWords.value - revealedCount.value
})

const ratedCount = computed(() => {
  return ayahs.value.reduce((sum, ayah) =>
    sum + (ayah.words?.filter(w => w.feedback !== null).length || 0), 0)
})

const correctCount = computed(() => {
  return ayahs.value.reduce((sum, ayah) =>
    sum + (ayah.words?.filter(w => w.feedback === true).length || 0), 0)
})

const wrongCount = computed(() => {
  return ayahs.value.reduce((sum, ayah) =>
    sum + (ayah.words?.filter(w => w.feedback === false).length || 0), 0)
})

const accuracy = computed(() => {
  const rated = ratedCount.value
  if (rated === 0) return 0
  return Math.round((correctCount.value / rated) * 100)
})

const progressPercentage = computed(() => {
  if (totalWords.value === 0) return 0
  return Math.round((ratedCount.value / totalWords.value) * 100)
})

const selectSurah = async (hifz) => {
  selectedSource.value = hifz
  loading.value = true

  try {
    const surahId = hifz.surah?.id || hifz.surah_id
    const response = await axios.get(`/quran/surah/${surahId}/ayahs`)
    const allAyahs = response.data.data || response.data

    const filteredAyahs = allAyahs.filter(ayah =>
      ayah.number >= hifz.start_ayah &&
      ayah.number <= hifz.end_ayah
    )

    ayahs.value = filteredAyahs.map(ayah => processAyah(ayah))
    currentAyahIndex.value = 0
  } catch (error) {
    console.error('Error loading ayahs:', error)
    showFeedback('حدث خطأ في تحميل الآيات', 'error')
  } finally {
    loading.value = false
  }
}

const selectPage = async () => {
  if (!pageNumber.value || pageNumber.value < 1 || pageNumber.value > 604) {
    showFeedback('الرجاء إدخال رقم صفحة صحيح (1-604)', 'error')
    return
  }

  loading.value = true
  selectedSource.value = { type: 'page', number: pageNumber.value }

  try {
    const response = await axios.get(`/quran/page/${pageNumber.value}`)
    const allAyahs = response.data.data || response.data

    ayahs.value = allAyahs.map(ayah => processAyah(ayah))
    currentAyahIndex.value = 0
  } catch (error) {
    console.error('Error loading page:', error)
    showFeedback('حدث خطأ في تحميل الصفحة', 'error')
  } finally {
    loading.value = false
  }
}

const processAyah = (ayah) => {
  const words = ayah.text.split(' ').filter(w => w.trim())

  return {
    ...ayah,
    words: words.map(text => ({
      text,
      revealed: false,
      feedback: null // null = not rated, true = correct, false = wrong
    }))
  }
}

const revealWord = (index) => {
  if (currentAyahWords.value[index]) {
    currentAyahWords.value[index].revealed = true
  }
}

const markWord = (index, isCorrect) => {
  if (currentAyahWords.value[index]) {
    currentAyahWords.value[index].feedback = isCorrect

    if (isCorrect) {
      showFeedback('✓ صحيح', 'success')
    } else {
      showFeedback('✗ خطأ - راجع هذه الكلمة', 'error')
    }
  }
}

const resetWord = (index) => {
  if (currentAyahWords.value[index]) {
    currentAyahWords.value[index].revealed = false
    currentAyahWords.value[index].feedback = null
  }
}

const resetCurrentAyah = () => {
  currentAyahWords.value.forEach(word => {
    word.revealed = false
    word.feedback = null
  })
  showAllWords.value = false
}

const toggleShowAll = () => {
  showAllWords.value = !showAllWords.value
}

const nextAyah = () => {
  if (currentAyahIndex.value < ayahs.value.length - 1) {
    currentAyahIndex.value++
    showAllWords.value = false
  }
}

const previousAyah = () => {
  if (currentAyahIndex.value > 0) {
    currentAyahIndex.value--
    showAllWords.value = false
  }
}

const finishPractice = () => {
  showResults.value = true
}

const retryPractice = () => {
  ayahs.value.forEach(ayah => {
    ayah.words.forEach(word => {
      word.revealed = false
      word.feedback = null
    })
  })
  currentAyahIndex.value = 0
  showAllWords.value = false
  showResults.value = false
}

const selectNewSource = () => {
  selectedMode.value = null
  selectedSource.value = null
  pageNumber.value = null
  ayahs.value = []
  currentAyahIndex.value = 0
  showResults.value = false
}

const goToHifz = () => {
  router.push('/hifz')
}

const closeResults = () => {
  showResults.value = false
}

const showFeedback = (message, type = 'success') => {
  feedbackMessage.value = message
  feedbackType.value = type
  setTimeout(() => {
    feedbackMessage.value = ''
  }, 2000)
}

onMounted(async () => {
  try {
    await hifzStore.fetchUserHifz()
  } catch (error) {
    console.error('Error loading hifz:', error)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');

.font-arabic {
  font-family: 'Amiri', serif;
}

@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

/* Hover effect for buttons */
.group:hover .opacity-0 {
  opacity: 1;
}
</style>
