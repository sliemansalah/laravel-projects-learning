<template>
  <div class="text-center">
    <!-- Instructions -->
    <div class="mb-8">
      <div class="text-sm text-gray-600 mb-4">الكلمة {{ currentIndex + 1 }} من {{ totalWords }}</div>
      <div class="text-lg text-gray-700 mb-4">اكتب الكلمة التالية:</div>
      <div v-if="word.translation" class="text-xl text-emerald-700 font-bold mb-6">
        "{{ word.translation }}"
      </div>
    </div>

    <!-- Writing Input -->
    <div class="mb-8">
      <input
        v-model="userInput"
        @keyup.enter="checkAnswer"
        type="text"
        dir="rtl"
        class="arabic-text text-4xl text-center w-full max-w-md mx-auto px-6 py-4 border-4 rounded-lg focus:outline-none focus:border-emerald-500 transition-all"
        :class="inputClass"
        :placeholder="showHint ? hint : 'اكتب الكلمة هنا...'"
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
          @click="toggleHint"
          class="px-6 py-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
        >
          💡 {{ showHint ? 'إخفاء المساعدة' : 'مساعدة' }}
        </button>
        <button
          @click="skip"
          class="px-6 py-4 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
        >
          ⏭️ تخطي
        </button>
      </div>

      <div v-else>
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
      <p>اضغط Enter للتحقق من الإجابة</p>
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
const showHint = ref(false)

const hint = computed(() => {
  if (!props.word.text_arabic) return ''
  const length = props.word.text_arabic.length
  return props.word.text_arabic.substring(0, Math.ceil(length / 3)) + '...'
})

const inputClass = computed(() => {
  if (!feedback.value) return 'border-gray-300'
  return isCorrect.value ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50'
})

const normalizeArabic = (text) => {
  return text
    .replace(/[إأآا]/g, 'ا')
    .replace(/[ىي]/g, 'ي')
    .replace(/ة/g, 'ه')
    .replace(/[ًٌٍَُِّْ]/g, '') // Remove diacritics
    .trim()
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

const skip = () => {
  answered.value = true
  isCorrect.value = false
  feedback.value = true
  emit('wrong')
}

const nextWord = () => {
  userInput.value = ''
  answered.value = false
  feedback.value = false
  showHint.value = false
}

const toggleHint = () => {
  showHint.value = !showHint.value
}

defineExpose({ nextWord })
</script>
