<template>
  <div class="text-center">
    <!-- Current Word Display -->
    <div class="mb-8">
      <div class="text-sm text-gray-600 mb-4">الكلمة {{ currentIndex + 1 }} من {{ totalWords }}</div>
      <div class="arabic-text text-6xl font-bold text-emerald-800 mb-6">
        {{ showWord ? word.text_arabic : '؟' }}
      </div>
      <div v-if="showWord" class="text-xl text-gray-600 mb-4">
        {{ word.translation }}
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="space-y-4">
      <div v-if="!showWord" class="mb-6">
        <button
          @click="revealWord"
          class="px-8 py-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-lg font-bold"
        >
          👁️ إظهار الكلمة
        </button>
      </div>

      <div v-else class="flex gap-4 justify-center">
        <button
          @click="markCorrect"
          class="px-8 py-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-lg font-bold"
        >
          ✓ صحيح
        </button>
        <button
          @click="markWrong"
          class="px-8 py-4 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-lg font-bold"
        >
          ✗ خطأ
        </button>
      </div>
    </div>

    <!-- Hint -->
    <div class="mt-8 text-sm text-gray-500">
      <p v-if="!showWord">حاول تذكر الكلمة ثم اضغط "إظهار الكلمة"</p>
      <p v-else>هل تذكرت الكلمة بشكل صحيح؟</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  word: { type: Object, required: true },
  currentIndex: { type: Number, required: true },
  totalWords: { type: Number, required: true }
})

const emit = defineEmits(['correct', 'wrong'])

const showWord = ref(false)

const revealWord = () => {
  showWord.value = true
}

const markCorrect = () => {
  emit('correct')
  showWord.value = false
}

const markWrong = () => {
  emit('wrong')
  showWord.value = false
}
</script>
