<template>
  <div class="text-center">
    <!-- Instructions -->
    <div class="mb-8">
      <div class="text-lg text-gray-700 mb-4">طابق الكلمات العربية مع معانيها</div>
      <div class="text-sm text-gray-600">انقر على الكلمة ثم انقر على المعنى المناسب</div>
    </div>

    <!-- Matching Area -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto mb-8">
      <!-- Arabic Words Column -->
      <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-4 font-bold">الكلمات</div>
        <button
          v-for="word in arabicWords"
          :key="word.id"
          @click="selectArabic(word)"
          :disabled="word.matched"
          class="w-full arabic-text text-3xl px-6 py-4 rounded-lg border-3 transition-all"
          :class="getArabicClass(word)"
        >
          {{ word.text_arabic }}
        </button>
      </div>

      <!-- Translations Column -->
      <div class="space-y-3">
        <div class="text-sm text-gray-600 mb-4 font-bold">المعاني</div>
        <button
          v-for="translation in translations"
          :key="translation.id"
          @click="selectTranslation(translation)"
          :disabled="translation.matched"
          class="w-full text-xl px-6 py-4 rounded-lg border-3 transition-all"
          :class="getTranslationClass(translation)"
        >
          {{ translation.translation }}
        </button>
      </div>
    </div>

    <!-- Feedback -->
    <div v-if="feedback" class="mb-6 animate-fade-in">
      <div v-if="lastMatchCorrect" class="text-green-600 text-xl font-bold">
        ✓ مطابقة صحيحة!
      </div>
      <div v-else class="text-red-600 text-xl font-bold">
        ✗ مطابقة خاطئة، حاول مرة أخرى
      </div>
    </div>

    <!-- Progress -->
    <div class="mb-6">
      <div class="text-sm text-gray-600 mb-2">
        {{ matchedCount }} من {{ totalWords }} مكتمل
      </div>
      <div class="w-full bg-gray-200 rounded-full h-3 max-w-md mx-auto">
        <div
          class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-300"
          :style="{ width: `${(matchedCount / totalWords) * 100}%` }"
        ></div>
      </div>
    </div>

    <!-- Complete Button -->
    <div v-if="allMatched">
      <button
        @click="complete"
        class="px-8 py-4 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-lg font-bold"
      >
        ✓ إنهاء
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  words: { type: Array, required: true }
})

const emit = defineEmits(['complete'])

const arabicWords = ref([])
const translations = ref([])
const selectedArabic = ref(null)
const selectedTranslation = ref(null)
const feedback = ref(false)
const lastMatchCorrect = ref(false)
const mistakes = ref(0)

const matchedCount = computed(() => {
  return arabicWords.value.filter(w => w.matched).length
})

const totalWords = computed(() => arabicWords.value.length)

const allMatched = computed(() => {
  return matchedCount.value === totalWords.value
})

onMounted(() => {
  initializeWords()
})

const initializeWords = () => {
  // Initialize arabic words
  arabicWords.value = props.words.map(w => ({
    ...w,
    matched: false
  }))

  // Shuffle translations
  translations.value = [...props.words]
    .sort(() => Math.random() - 0.5)
    .map(w => ({
      id: w.id,
      translation: w.translation,
      matched: false
    }))
}

const selectArabic = (word) => {
  if (word.matched) return
  selectedArabic.value = word
  checkMatch()
}

const selectTranslation = (translation) => {
  if (translation.matched) return
  selectedTranslation.value = translation
  checkMatch()
}

const checkMatch = () => {
  if (!selectedArabic.value || !selectedTranslation.value) return

  const isMatch = selectedArabic.value.id === selectedTranslation.value.id

  if (isMatch) {
    selectedArabic.value.matched = true
    selectedTranslation.value.matched = true
    lastMatchCorrect.value = true
  } else {
    lastMatchCorrect.value = false
    mistakes.value++
  }

  feedback.value = true
  setTimeout(() => {
    feedback.value = false
    selectedArabic.value = null
    selectedTranslation.value = null
  }, 1000)
}

const getArabicClass = (word) => {
  if (word.matched) {
    return 'border-green-500 bg-green-100 cursor-not-allowed opacity-50'
  }
  if (selectedArabic.value?.id === word.id) {
    return 'border-emerald-500 bg-emerald-100 font-bold'
  }
  return 'border-gray-300 bg-white hover:border-emerald-500 hover:bg-emerald-50 cursor-pointer'
}

const getTranslationClass = (translation) => {
  if (translation.matched) {
    return 'border-green-500 bg-green-100 cursor-not-allowed opacity-50'
  }
  if (selectedTranslation.value?.id === translation.id) {
    return 'border-emerald-500 bg-emerald-100 font-bold'
  }
  return 'border-gray-300 bg-white hover:border-emerald-500 hover:bg-emerald-50 cursor-pointer'
}

const complete = () => {
  emit('complete', { mistakes: mistakes.value })
}
</script>
