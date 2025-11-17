<template>
  <div class="text-center">
    <!-- Instructions -->
    <div class="mb-8">
      <div class="text-sm text-gray-600 mb-4">الكلمة {{ currentIndex + 1 }} من {{ totalWords }}</div>
      <div class="text-lg text-gray-700 mb-4">استمع للكلمة واكتبها</div>
    </div>

    <!-- Audio Player -->
    <div class="mb-8">
      <button
        @click="playAudio"
        :disabled="playing"
        class="px-12 py-8 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-all transform hover:scale-105 disabled:bg-gray-300 disabled:cursor-not-allowed"
      >
        <div class="text-6xl mb-2">{{ playing ? '⏸️' : '🔊' }}</div>
        <div class="text-lg">{{ playing ? 'جاري التشغيل...' : 'استمع للكلمة' }}</div>
      </button>
    </div>

    <!-- Play Counter -->
    <div class="mb-6 text-sm text-gray-600">
      عدد مرات الاستماع: {{ playCount }}
    </div>

    <!-- Writing Input -->
    <div class="mb-8">
      <input
        v-model="userInput"
        @keyup.enter="checkAnswer"
        type="text"
        dir="rtl"
        class="arabic-text text-4xl text-center w-full max-w-md mx-auto px-6 py-4 border-4 rounded-lg focus:outline-none focus:border-blue-500 transition-all"
        :class="inputClass"
        placeholder="اكتب ما سمعته..."
        :disabled="answered"
        autofocus
      />
    </div>

    <!-- Feedback -->
    <div v-if="feedback" class="mb-6">
      <div v-if="isCorrect" class="text-green-600 text-xl font-bold animate-fade-in">
        ✓ ممتاز! {{ word.text_arabic }}
      </div>
      <div v-else class="text-red-600 text-xl font-bold animate-fade-in">
        ✗ الإجابة الصحيحة: {{ word.text_arabic }}
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="space-y-4">
      <div v-if="!answered" class="flex gap-4 justify-center">
        <button
          @click="checkAnswer"
          :disabled="!userInput.trim()"
          class="px-8 py-4 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-lg font-bold disabled:bg-gray-300 disabled:cursor-not-allowed"
        >
          ✓ تحقق
        </button>
        <button
          @click="showTranslation = !showTranslation"
          class="px-6 py-4 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors"
        >
          💡 {{ showTranslation ? 'إخفاء' : 'أظهر' }} المعنى
        </button>
      </div>

      <div v-if="showTranslation" class="text-lg text-amber-700 font-bold">
        المعنى: "{{ word.translation }}"
      </div>

      <div v-if="answered">
        <button
          @click="nextWord"
          class="px-8 py-4 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-lg font-bold"
        >
          ← الكلمة التالية
        </button>
      </div>
    </div>

    <!-- Hint -->
    <div class="mt-8 text-sm text-gray-500">
      <p>استمع جيداً ثم اكتب الكلمة التي سمعتها</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  word: { type: Object, required: true },
  currentIndex: { type: Number, required: true },
  totalWords: { type: Number, required: true }
})

const emit = defineEmits(['correct', 'wrong'])

const userInput = ref('')
const answered = ref(false)
const isCorrect = ref(false)
const feedback = ref(false)
const playing = ref(false)
const playCount = ref(0)
const showTranslation = ref(false)
const audioElement = ref(null)

const inputClass = computed(() => {
  if (!feedback.value) return 'border-gray-300'
  return isCorrect.value ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50'
})

const normalizeArabic = (text) => {
  return text
    .replace(/[إأآا]/g, 'ا')
    .replace(/[ىي]/g, 'ي')
    .replace(/ة/g, 'ه')
    .replace(/[ًٌٍَُِّْ]/g, '')
    .trim()
}

const playAudio = () => {
  // Note: In real implementation, you would have actual audio files
  // For now, we'll use Speech Synthesis API as a fallback
  if ('speechSynthesis' in window) {
    const utterance = new SpeechSynthesisUtterance(props.word.text_arabic)
    utterance.lang = 'ar-SA'
    utterance.rate = 0.8

    utterance.onstart = () => {
      playing.value = true
      playCount.value++
    }

    utterance.onend = () => {
      playing.value = false
    }

    window.speechSynthesis.speak(utterance)
  } else {
    alert('متصفحك لا يدعم تشغيل الصوت')
  }
}

const checkAnswer = () => {
  if (!userInput.value.trim() || answered.value) return

  const normalizedInput = normalizeArabic(userInput.value)
  const normalizedAnswer = normalizeArabic(props.word.text_arabic)

  isCorrect.value = normalizedInput === normalizedAnswer
  answered.value = true
  feedback.value = true

  if (isCorrect.value) {
    emit('correct')
  } else {
    emit('wrong')
  }
}

const nextWord = () => {
  userInput.value = ''
  answered.value = false
  feedback.value = false
  playCount.value = 0
  showTranslation.value = false
}

defineExpose({ nextWord })
</script>
