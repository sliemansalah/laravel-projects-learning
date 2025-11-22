<template>
  <div class="drag-drop-test min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 p-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <router-link
            to="/hifz"
            class="w-10 h-10 bg-purple-100 hover:bg-purple-200 rounded-full flex items-center justify-center transition"
          >
            <span class="text-xl">→</span>
          </router-link>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">اختبار الحفظ - ملء الفراغات</h1>
            <p class="text-gray-600 mt-1">اسحب الكلمات الصحيحة لملء الفراغات</p>
          </div>
        </div>
        <div class="text-5xl">🧩</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-purple-500 border-t-transparent"></div>
      <p class="mt-4 text-gray-600">جاري تحميل الاختبار...</p>
    </div>

    <!-- Select Hifz -->
    <div v-else-if="!selectedHifz" class="bg-white rounded-2xl shadow-lg p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">اختر السورة للاختبار</h2>

      <div v-if="availableHifz.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">📚</div>
        <p class="text-lg text-gray-600 mb-4">لا توجد سور محفوظة لاختبارها</p>
        <router-link
          to="/hifz"
          class="inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
        >
          العودة إلى الحفظ
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <button
          v-for="hifz in availableHifz"
          :key="hifz.id"
          @click="selectHifz(hifz)"
          class="p-5 bg-gradient-to-br from-purple-50 to-white rounded-xl border-2 border-purple-200 hover:border-purple-400 hover:shadow-lg transition text-right"
        >
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
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

    <!-- Test Interface -->
    <div v-else>
      <!-- Progress Bar -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-3">
          <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ selectedHifz.surah?.name_arabic }}</h3>
            <p class="text-sm text-gray-600">السؤال {{ currentQuestionIndex + 1 }} من {{ questions.length }}</p>
          </div>
          <div class="text-right">
            <p class="text-2xl font-bold text-purple-600">{{ score }} / {{ questions.length }}</p>
            <p class="text-xs text-gray-500">النقاط</p>
          </div>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
          <div
            class="bg-gradient-to-r from-purple-500 to-pink-500 h-full transition-all duration-500"
            :style="{ width: ((currentQuestionIndex) / questions.length * 100) + '%' }"
          ></div>
        </div>
      </div>

      <!-- Current Question -->
      <div v-if="currentQuestion" class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <div class="mb-8">
          <div class="flex items-center gap-2 mb-4">
            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">
              الآية {{ currentQuestion.ayahNumber }}
            </span>
          </div>

          <!-- Ayah with blanks -->
          <div class="text-2xl leading-loose text-right font-arabic" dir="rtl">
            <span
              v-for="(part, index) in currentQuestion.parts"
              :key="index"
            >
              <span v-if="part.type === 'text'">{{ part.value }}</span>

              <!-- Drop Zone -->
              <span
                v-else
                @dragover.prevent
                @drop="handleDrop($event, part.id)"
                @dragenter="handleDragEnter($event, part.id)"
                @dragleave="handleDragLeave($event, part.id)"
                :class="[
                  'inline-block min-w-[120px] mx-2 px-4 py-2 border-2 border-dashed rounded-lg transition-all',
                  getDropZoneClass(part.id)
                ]"
              >
                <span v-if="part.filled" class="text-purple-700 font-semibold">
                  {{ part.filled }}
                </span>
                <span v-else class="text-gray-400 text-sm">
                  اسحب هنا
                </span>
              </span>
            </span>
          </div>
        </div>

        <!-- Available Words (Draggable) -->
        <div class="border-t-2 border-gray-200 pt-6">
          <h4 class="text-lg font-semibold text-gray-700 mb-4">الكلمات المتاحة:</h4>
          <div class="flex flex-wrap gap-3 justify-center">
            <div
              v-for="word in currentQuestion.availableWords"
              :key="word.id"
              :draggable="!word.used"
              @dragstart="handleDragStart($event, word)"
              @dragend="handleDragEnd"
              :class="[
                'px-6 py-3 rounded-xl text-lg font-semibold transition-all cursor-move',
                word.used
                  ? 'bg-gray-200 text-gray-400 cursor-not-allowed opacity-50'
                  : 'bg-gradient-to-br from-purple-500 to-pink-500 text-white shadow-lg hover:shadow-xl hover:scale-105'
              ]"
            >
              {{ word.text }}
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 mt-8">
          <button
            @click="checkAnswer"
            :disabled="!isAnswerComplete"
            class="flex-1 px-6 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-emerald-600 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl"
          >
            ✓ تحقق من الإجابة
          </button>
          <button
            @click="resetQuestion"
            class="px-6 py-4 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition"
          >
            🔄 إعادة تعيين
          </button>
        </div>
      </div>

      <!-- Results Modal -->
      <div
        v-if="showResults"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        @click.self="closeResults"
      >
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full animate-fade-in">
          <div class="text-center">
            <div class="text-6xl mb-4">
              {{ score === questions.length ? '🎉' : score >= questions.length * 0.7 ? '👍' : '💪' }}
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">
              {{ score === questions.length ? 'ممتاز!' : score >= questions.length * 0.7 ? 'أحسنت!' : 'استمر!' }}
            </h2>
            <p class="text-gray-600 mb-6">حصلت على {{ score }} من {{ questions.length }} نقطة</p>

            <div class="mb-6">
              <div class="text-5xl font-bold text-purple-600 mb-2">
                {{ Math.round((score / questions.length) * 100) }}%
              </div>
              <p class="text-gray-600">نسبة النجاح</p>
            </div>

            <div class="flex gap-3">
              <button
                @click="retryTest"
                class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
              >
                🔄 إعادة الاختبار
              </button>
              <button
                @click="selectNewHifz"
                class="flex-1 px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition"
              >
                ➕ اختبار آخر
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
const selectedHifz = ref(null)
const questions = ref([])
const currentQuestionIndex = ref(0)
const score = ref(0)
const draggedWord = ref(null)
const dragOverZone = ref(null)
const showResults = ref(false)
const feedbackMessage = ref('')
const feedbackType = ref('success')

const availableHifz = computed(() => {
  return hifzStore.userHifz.filter(h =>
    (h.status === 'completed' || h.status === 'in_progress') &&
    h.memorized_count > 0
  )
})

const currentQuestion = computed(() => {
  return questions.value[currentQuestionIndex.value]
})

const isAnswerComplete = computed(() => {
  if (!currentQuestion.value) return false
  return currentQuestion.value.parts
    .filter(p => p.type === 'blank')
    .every(p => p.filled)
})

const getDropZoneClass = (blankId) => {
  const blank = currentQuestion.value?.parts.find(p => p.id === blankId)
  if (!blank) return 'border-gray-300 bg-gray-50'

  if (blank.filled) {
    return 'border-purple-400 bg-purple-50'
  }

  if (dragOverZone.value === blankId) {
    return 'border-purple-500 bg-purple-100 scale-105'
  }

  return 'border-gray-300 bg-gray-50 hover:border-purple-300'
}

const selectHifz = async (hifz) => {
  selectedHifz.value = hifz
  loading.value = true

  try {
    // Fetch ayahs for this hifz range
    const response = await axios.get(`/api/quran/surah/${hifz.surah.number}/ayahs`)
    const allAyahs = response.data.data || response.data
    const ayahs = allAyahs.filter(ayah =>
      ayah.number >= hifz.start_ayah &&
      ayah.number <= hifz.end_ayah
    )

    // Generate questions (random 5 ayahs or all if less than 5)
    const numQuestions = Math.min(5, ayahs.length)
    const selectedAyahs = shuffleArray([...ayahs]).slice(0, numQuestions)

    questions.value = selectedAyahs.map(ayah => generateQuestion(ayah))
    currentQuestionIndex.value = 0
    score.value = 0
  } catch (error) {
    console.error('Error loading test:', error)
    showFeedback('حدث خطأ في تحميل الاختبار', 'error')
  } finally {
    loading.value = false
  }
}

const generateQuestion = (ayah) => {
  const words = ayah.text.split(' ').filter(w => w.trim())

  // Remove small words (less than 3 characters) from being blanks
  const eligibleIndices = words
    .map((w, i) => ({ word: w, index: i }))
    .filter(item => item.word.length >= 3)
    .map(item => item.index)

  // Select 2-3 random words to be blanks
  const numBlanks = Math.min(3, Math.max(2, Math.floor(eligibleIndices.length / 3)))
  const blankIndices = shuffleArray([...eligibleIndices]).slice(0, numBlanks)

  // Create parts with text and blanks
  const parts = []
  let blankId = 0

  words.forEach((word, index) => {
    if (index > 0) {
      parts.push({ type: 'text', value: ' ' })
    }

    if (blankIndices.includes(index)) {
      parts.push({
        type: 'blank',
        id: `blank-${blankId++}`,
        correctAnswer: word,
        filled: null
      })
    } else {
      parts.push({ type: 'text', value: word })
    }
  })

  // Create available words (blanks + some random distractors)
  const correctWords = blankIndices.map(i => words[i])
  const otherWords = words.filter((w, i) => !blankIndices.includes(i) && w.length >= 3)
  const distractors = shuffleArray(otherWords).slice(0, Math.min(2, otherWords.length))

  const availableWords = shuffleArray([
    ...correctWords.map((w, i) => ({ id: `word-${i}`, text: w, used: false })),
    ...distractors.map((w, i) => ({ id: `distractor-${i}`, text: w, used: false }))
  ])

  return {
    ayahNumber: ayah.numberInSurah,
    parts,
    availableWords,
    originalText: ayah.text
  }
}

const handleDragStart = (event, word) => {
  if (word.used) {
    event.preventDefault()
    return
  }
  draggedWord.value = word
  event.dataTransfer.effectAllowed = 'move'
}

const handleDragEnd = () => {
  draggedWord.value = null
  dragOverZone.value = null
}

const handleDragEnter = (event, blankId) => {
  event.preventDefault()
  dragOverZone.value = blankId
}

const handleDragLeave = (event, blankId) => {
  if (event.currentTarget === event.target) {
    dragOverZone.value = null
  }
}

const handleDrop = (event, blankId) => {
  event.preventDefault()
  dragOverZone.value = null

  if (!draggedWord.value) return

  const blank = currentQuestion.value.parts.find(p => p.id === blankId)
  if (!blank) return

  // If blank already filled, return the word to available
  if (blank.filled) {
    const oldWord = currentQuestion.value.availableWords.find(w => w.text === blank.filled)
    if (oldWord) oldWord.used = false
  }

  // Fill the blank
  blank.filled = draggedWord.value.text
  draggedWord.value.used = true
  draggedWord.value = null
}

const checkAnswer = () => {
  const blanks = currentQuestion.value.parts.filter(p => p.type === 'blank')
  const correct = blanks.every(b => b.filled === b.correctAnswer)

  if (correct) {
    score.value++
    showFeedback('إجابة صحيحة! ممتاز 🎉', 'success')

    setTimeout(() => {
      if (currentQuestionIndex.value < questions.value.length - 1) {
        currentQuestionIndex.value++
      } else {
        showResults.value = true
      }
    }, 1500)
  } else {
    showFeedback('إجابة خاطئة، حاول مرة أخرى', 'error')
  }
}

const resetQuestion = () => {
  const blanks = currentQuestion.value.parts.filter(p => p.type === 'blank')
  blanks.forEach(blank => {
    if (blank.filled) {
      const word = currentQuestion.value.availableWords.find(w => w.text === blank.filled)
      if (word) word.used = false
      blank.filled = null
    }
  })
}

const retryTest = () => {
  currentQuestionIndex.value = 0
  score.value = 0
  showResults.value = false
  questions.value = shuffleArray(questions.value)
  questions.value.forEach(q => {
    q.parts.filter(p => p.type === 'blank').forEach(blank => blank.filled = null)
    q.availableWords.forEach(w => w.used = false)
  })
}

const selectNewHifz = () => {
  selectedHifz.value = null
  questions.value = []
  currentQuestionIndex.value = 0
  score.value = 0
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
  }, 3000)
}

const shuffleArray = (array) => {
  const newArray = [...array]
  for (let i = newArray.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1))
    ;[newArray[i], newArray[j]] = [newArray[j], newArray[i]]
  }
  return newArray
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

/* Smooth drag animation */
[draggable="true"] {
  user-select: none;
  -webkit-user-drag: element;
}

[draggable="true"]:active {
  cursor: grabbing;
  opacity: 0.5;
}
</style>
