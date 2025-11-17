<template>
  <div class="text-center">
    <!-- Instructions -->
    <div class="mb-8">
      <div class="text-sm text-gray-600 mb-4">الكلمة {{ currentIndex + 1 }} من {{ totalWords }}</div>
      <div class="text-lg text-gray-700 mb-4">ما هي الكلمة التالية؟</div>

      <!-- Context (Previous words) -->
      <div v-if="previousWords.length > 0" class="mb-6">
        <div class="text-sm text-gray-500 mb-2">الكلمات السابقة:</div>
        <div class="arabic-text text-3xl text-gray-600">
          {{ previousWords.join(' ') }}
        </div>
      </div>

      <!-- Translation Hint -->
      <div v-if="word.translation" class="text-lg text-emerald-700 mb-4">
        معناها: "{{ word.translation }}"
      </div>
    </div>

    <!-- Choices -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 max-w-2xl mx-auto">
      <button
        v-for="(choice, index) in choices"
        :key="index"
        @click="selectChoice(choice)"
        :disabled="answered"
        class="arabic-text text-3xl px-8 py-6 rounded-xl border-3 transition-all duration-300 transform hover:scale-105"
        :class="getChoiceClass(choice)"
      >
        {{ choice.text_arabic }}
      </button>
    </div>

    <!-- Feedback -->
    <div v-if="feedback" class="mb-6 animate-fade-in">
      <div v-if="isCorrect" class="text-green-600 text-xl font-bold">
        ✓ إجابة صحيحة!
      </div>
      <div v-else class="text-red-600 text-xl font-bold">
        ✗ الإجابة الصحيحة: {{ word.text_arabic }}
      </div>
    </div>

    <!-- Next Button -->
    <div v-if="answered">
      <button
        @click="nextWord"
        class="px-8 py-4 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-lg font-bold"
      >
        ← الكلمة التالية
      </button>
    </div>

    <!-- Hint -->
    <div class="mt-8 text-sm text-gray-500">
      <p>اختر الكلمة الصحيحة من الخيارات</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  word: { type: Object, required: true },
  allWords: { type: Array, required: true },
  currentIndex: { type: Number, required: true },
  totalWords: { type: Number, required: true }
})

const emit = defineEmits(['correct', 'wrong'])

const choices = ref([])
const selectedChoice = ref(null)
const answered = ref(false)
const isCorrect = ref(false)
const feedback = ref(false)

const previousWords = computed(() => {
  return props.allWords
    .slice(Math.max(0, props.currentIndex - 2), props.currentIndex)
    .map(w => w.text_arabic)
})

watch(() => props.word, () => {
  generateChoices()
  resetState()
}, { immediate: true })

const generateChoices = () => {
  // Get random wrong choices from other words
  const wrongChoices = props.allWords
    .filter(w => w.id !== props.word.id)
    .sort(() => Math.random() - 0.5)
    .slice(0, 3)

  // Combine with correct answer and shuffle
  choices.value = [props.word, ...wrongChoices]
    .sort(() => Math.random() - 0.5)
}

const resetState = () => {
  selectedChoice.value = null
  answered.value = false
  isCorrect.value = false
  feedback.value = false
}

const selectChoice = (choice) => {
  if (answered.value) return

  selectedChoice.value = choice
  answered.value = true
  isCorrect.value = choice.id === props.word.id
  feedback.value = true

  if (isCorrect.value) {
    emit('correct')
  } else {
    emit('wrong')
  }
}

const getChoiceClass = (choice) => {
  if (!answered.value) {
    return 'border-gray-300 bg-white hover:border-emerald-500 hover:bg-emerald-50 cursor-pointer'
  }

  if (choice.id === props.word.id) {
    return 'border-green-500 bg-green-100 font-bold'
  }

  if (choice.id === selectedChoice.value?.id && !isCorrect.value) {
    return 'border-red-500 bg-red-100'
  }

  return 'border-gray-300 bg-gray-100 opacity-50 cursor-not-allowed'
}

const nextWord = () => {
  resetState()
}

defineExpose({ nextWord })
</script>
