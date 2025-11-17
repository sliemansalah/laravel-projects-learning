<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-10">
      <div class="max-w-4xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <button
            @click="handleBack"
            class="flex items-center gap-2 text-gray-600 hover:text-gray-800"
          >
            <span>←</span>
            <span>رجوع</span>
          </button>
          <h1 class="text-xl font-bold text-emerald-800">
            {{ currentMode ? modeNames[currentMode] : 'اختر طريقة الحفظ' }}
          </h1>
          <div class="w-20"></div>
        </div>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-500 border-t-transparent mx-auto mb-4"></div>
        <p class="text-gray-600">جاري التحميل...</p>
      </div>
    </div>

    <!-- Mode Selection -->
    <main v-else-if="ayah && !currentMode" class="max-w-6xl mx-auto px-4 py-8">
      <!-- Ayah Info -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <div class="text-center">
          <h2 class="text-2xl font-bold text-gray-800 mb-2">
            {{ ayah.surah?.name_arabic }} - الآية {{ ayah.number }}
          </h2>
          <p class="arabic-text text-3xl leading-loose text-gray-800 mb-4">
            {{ ayah.text_arabic }}
          </p>
          <p class="text-gray-600 text-lg">
            {{ ayah.translation_ar }}
          </p>
        </div>
      </div>

      <!-- Mode Selection Grid -->
      <div class="mb-6">
        <h3 class="text-2xl font-bold text-center text-gray-800 mb-6">اختر طريقة الحفظ</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Show/Hide Mode -->
          <button
            @click="selectMode('show-hide')"
            class="mode-card group"
          >
            <div class="text-6xl mb-4">👁️</div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">إظهار وإخفاء</h4>
            <p class="text-gray-600 text-sm">اختبر ذاكرتك بإظهار وإخفاء الكلمات</p>
            <div class="mt-4 text-emerald-600 font-medium">مناسبة للمبتدئين</div>
          </button>

          <!-- Writing Mode -->
          <button
            @click="selectMode('writing')"
            class="mode-card group"
          >
            <div class="text-6xl mb-4">✍️</div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">الكتابة</h4>
            <p class="text-gray-600 text-sm">اكتب الكلمات لتقوية الحفظ</p>
            <div class="mt-4 text-blue-600 font-medium">متوسط الصعوبة</div>
          </button>

          <!-- Multiple Choice Mode -->
          <button
            @click="selectMode('multiple-choice')"
            class="mode-card group"
          >
            <div class="text-6xl mb-4">📝</div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">اختيار متعدد</h4>
            <p class="text-gray-600 text-sm">اختر الكلمة الصحيحة من عدة خيارات</p>
            <div class="mt-4 text-green-600 font-medium">سهل ومريح</div>
          </button>

          <!-- Listening Mode -->
          <button
            @click="selectMode('listening')"
            class="mode-card group"
          >
            <div class="text-6xl mb-4">🎧</div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">الاستماع</h4>
            <p class="text-gray-600 text-sm">استمع واكتب ما سمعته</p>
            <div class="mt-4 text-purple-600 font-medium">يقوي النطق</div>
          </button>

          <!-- Drag & Drop Mode -->
          <button
            @click="selectMode('drag-drop')"
            class="mode-card group"
          >
            <div class="text-6xl mb-4">🔄</div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">السحب والإفلات</h4>
            <p class="text-gray-600 text-sm">رتب الكلمات بالترتيب الصحيح</p>
            <div class="mt-4 text-amber-600 font-medium">تفاعلي وممتع</div>
          </button>

          <!-- Matching Mode -->
          <button
            @click="selectMode('matching')"
            class="mode-card group"
          >
            <div class="text-6xl mb-4">🔗</div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">المطابقة</h4>
            <p class="text-gray-600 text-sm">طابق الكلمات مع معانيها</p>
            <div class="mt-4 text-teal-600 font-medium">يقوي المعاني</div>
          </button>
        </div>
      </div>
    </main>

    <!-- Training Mode -->
    <main v-else-if="ayah && currentMode" class="max-w-4xl mx-auto px-4 py-8">
      <!-- Session Progress (for word-based modes) -->
      <div v-if="isWordBasedMode" class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <div class="text-center flex-1">
            <div class="text-3xl font-bold text-emerald-600">{{ currentWordIndex + 1 }}</div>
            <div class="text-sm text-gray-600">من {{ totalWords }}</div>
          </div>
          <div class="text-center flex-1">
            <div class="text-3xl font-bold text-green-600">{{ correctCount }}</div>
            <div class="text-sm text-gray-600">صحيح</div>
          </div>
          <div class="text-center flex-1">
            <div class="text-3xl font-bold text-red-600">{{ wrongCount }}</div>
            <div class="text-sm text-gray-600">خطأ</div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-3">
          <div
            class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-300"
            :style="{ width: `${progressPercentage}%` }"
          ></div>
        </div>
      </div>

      <!-- Ayah Context Card -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="text-center">
          <h2 class="text-xl font-bold text-gray-800 mb-2">
            {{ ayah.surah?.name_arabic }} - الآية {{ ayah.number }}
          </h2>
          <button
            @click="showFullAyah = !showFullAyah"
            class="text-sm text-emerald-600 hover:text-emerald-700"
          >
            {{ showFullAyah ? '▼ إخفاء الآية' : '▶ عرض الآية كاملة' }}
          </button>
          <div v-if="showFullAyah" class="mt-4">
            <p class="arabic-text text-2xl leading-loose text-gray-800 mb-2">
              {{ ayah.text_arabic }}
            </p>
            <p class="text-gray-600">
              {{ ayah.translation_ar }}
            </p>
          </div>
        </div>
      </div>

      <!-- Training Component -->
      <div v-if="!sessionCompleted" class="bg-white rounded-2xl shadow-lg p-8">
        <component
          :is="currentModeComponent"
          v-bind="currentModeProps"
          @correct="handleCorrect"
          @wrong="handleWrong"
          @complete="handleModeComplete"
          @next="nextItem"
          @mistake="handleMistake"
          ref="modeComponent"
        />

        <!-- Change Mode Button -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
          <button
            @click="changeMode"
            class="text-sm text-gray-600 hover:text-gray-800"
          >
            🔄 تغيير طريقة الحفظ
          </button>
        </div>
      </div>

      <!-- Session Completed -->
      <div v-else class="bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center">
          <div class="text-6xl mb-4">🎉</div>
          <h2 class="text-3xl font-bold text-emerald-800 mb-4">أحسنت!</h2>
          <p class="text-xl text-gray-600 mb-6">لقد أتممت الجلسة بنجاح</p>

          <!-- Results -->
          <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="text-3xl font-bold text-gray-800">{{ totalWords }}</div>
              <div class="text-sm text-gray-600">إجمالي الكلمات</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
              <div class="text-3xl font-bold text-green-600">{{ correctCount }}</div>
              <div class="text-sm text-gray-600">صحيح</div>
            </div>
            <div class="bg-red-50 rounded-lg p-4">
              <div class="text-3xl font-bold text-red-600">{{ wrongCount }}</div>
              <div class="text-sm text-gray-600">خطأ</div>
            </div>
          </div>

          <div class="bg-emerald-50 rounded-lg p-4 mb-6">
            <div class="text-4xl font-bold text-emerald-800">
              {{ accuracy }}%
            </div>
            <div class="text-sm text-gray-600">نسبة النجاح</div>
          </div>

          <!-- Actions -->
          <div class="flex flex-wrap gap-4 justify-center">
            <button
              @click="tryDifferentMode"
              class="px-6 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors"
            >
              🔄 جرب طريقة أخرى
            </button>
            <button
              @click="reviewAgain"
              class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
            >
              🔁 راجع مرة أخرى
            </button>
            <button
              @click="goToSurah"
              class="px-6 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors"
            >
              ← رجوع للسورة
            </button>
            <button
              @click="goHome"
              class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
            >
              🏠 الصفحة الرئيسية
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Error State -->
    <div v-else class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center">
        <div class="text-6xl mb-4">❌</div>
        <p class="text-xl text-gray-600 mb-4">حدث خطأ في تحميل البيانات</p>
        <button
          @click="router.back()"
          class="px-6 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors"
        >
          رجوع
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuranStore } from '@/stores/quran'
import { useMemorizationStore } from '@/stores/memorization'
import ShowHideMode from '@/components/memorization/ShowHideMode.vue'
import WritingMode from '@/components/memorization/WritingMode.vue'
import MultipleChoiceMode from '@/components/memorization/MultipleChoiceMode.vue'
import ListeningMode from '@/components/memorization/ListeningMode.vue'
import DragDropMode from '@/components/memorization/DragDropMode.vue'
import MatchingMode from '@/components/memorization/MatchingMode.vue'

const router = useRouter()
const route = useRoute()
const quranStore = useQuranStore()
const memorizationStore = useMemorizationStore()

const ayah = ref(null)
const loading = ref(true)
const currentMode = ref(null)
const currentWordIndex = ref(0)
const correctCount = ref(0)
const wrongCount = ref(0)
const sessionCompleted = ref(false)
const showFullAyah = ref(false)
const modeComponent = ref(null)

const modeNames = {
  'show-hide': 'إظهار وإخفاء',
  'writing': 'الكتابة',
  'multiple-choice': 'اختيار متعدد',
  'listening': 'الاستماع',
  'drag-drop': 'السحب والإفلات',
  'matching': 'المطابقة'
}

const totalWords = computed(() => ayah.value?.words?.length || 0)
const currentWord = computed(() => ayah.value?.words?.[currentWordIndex.value])

const progressPercentage = computed(() => {
  if (totalWords.value === 0) return 0
  return Math.round((currentWordIndex.value / totalWords.value) * 100)
})

const accuracy = computed(() => {
  const total = correctCount.value + wrongCount.value
  return total > 0 ? Math.round((correctCount.value / total) * 100) : 0
})

const isWordBasedMode = computed(() => {
  return ['show-hide', 'writing', 'multiple-choice', 'listening'].includes(currentMode.value)
})

const currentModeComponent = computed(() => {
  const components = {
    'show-hide': ShowHideMode,
    'writing': WritingMode,
    'multiple-choice': MultipleChoiceMode,
    'listening': ListeningMode,
    'drag-drop': DragDropMode,
    'matching': MatchingMode
  }
  return components[currentMode.value]
})

const currentModeProps = computed(() => {
  if (!ayah.value) return {}

  const baseProps = {
    word: currentWord.value,
    currentIndex: currentWordIndex.value,
    totalWords: totalWords.value,
    allWords: ayah.value.words
  }

  switch (currentMode.value) {
    case 'drag-drop':
      return { ayah: ayah.value }
    case 'matching':
      return { words: ayah.value.words }
    default:
      return baseProps
  }
})

onMounted(async () => {
  try {
    const ayahId = route.params.ayahId
    ayah.value = await quranStore.fetchAyah(ayahId)

    // Start memorization session
    await memorizationStore.startAyah(ayahId)
  } catch (error) {
    console.error('Error loading ayah:', error)
  } finally {
    loading.value = false
  }
})

const selectMode = (mode) => {
  currentMode.value = mode
  resetSession()
}

const changeMode = () => {
  if (confirm('هل تريد تغيير طريقة الحفظ؟ سيتم حفظ تقدمك الحالي.')) {
    currentMode.value = null
  }
}

const handleCorrect = async () => {
  correctCount.value++

  if (currentWord.value) {
    try {
      await memorizationStore.submitWord(currentWord.value.id, true)
    } catch (error) {
      console.error('Error submitting word:', error)
    }
  }

  if (isWordBasedMode.value) {
    nextWord()
  }
}

const handleWrong = async () => {
  wrongCount.value++

  if (currentWord.value) {
    try {
      await memorizationStore.submitWord(currentWord.value.id, false)
    } catch (error) {
      console.error('Error submitting word:', error)
    }
  }

  if (isWordBasedMode.value) {
    nextWord()
  }
}

const handleMistake = () => {
  wrongCount.value++
}

const handleModeComplete = async (data) => {
  if (data && data.mistakes !== undefined) {
    wrongCount.value += data.mistakes
    correctCount.value = totalWords.value - data.mistakes
  }
  await completeSession()
}

const nextWord = () => {
  if (modeComponent.value?.nextWord) {
    modeComponent.value.nextWord()
  }

  currentWordIndex.value++

  if (currentWordIndex.value >= totalWords.value) {
    completeSession()
  }
}

const nextItem = () => {
  completeSession()
}

const completeSession = async () => {
  try {
    await memorizationStore.completeSession()
    sessionCompleted.value = true
  } catch (error) {
    console.error('Error completing session:', error)
  }
}

const resetSession = () => {
  currentWordIndex.value = 0
  correctCount.value = 0
  wrongCount.value = 0
  sessionCompleted.value = false
}

const reviewAgain = () => {
  resetSession()
}

const tryDifferentMode = () => {
  currentMode.value = null
  resetSession()
}

const goToSurah = () => {
  router.push(`/memorize/${route.params.surahId}`)
}

const goHome = () => {
  router.push('/')
}

const handleBack = () => {
  if (sessionCompleted.value) {
    goToSurah()
  } else if (currentMode.value) {
    if (confirm('هل تريد حقاً مغادرة الجلسة؟ سيتم حفظ تقدمك الحالي.')) {
      goToSurah()
    }
  } else {
    goToSurah()
  }
}
</script>

<style scoped>
.mode-card {
  @apply bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-2xl hover:scale-105 cursor-pointer border-2 border-transparent hover:border-emerald-500;
}
</style>
