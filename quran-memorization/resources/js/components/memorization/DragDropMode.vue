<template>
  <div class="text-center">
    <!-- Instructions -->
    <div class="mb-8">
      <div class="text-sm text-gray-600 mb-4">رتب الكلمات لتكوين الآية الصحيحة</div>
      <div class="text-lg text-emerald-700 font-bold mb-4">
        {{ ayah.translation_ar }}
      </div>
    </div>

    <!-- Drop Area (User's Answer) -->
    <div class="mb-8 bg-emerald-50 rounded-2xl p-6 min-h-[150px]">
      <div class="text-sm text-gray-600 mb-4">إجابتك:</div>
      <div
        class="flex flex-wrap gap-3 justify-center min-h-[80px] p-4 border-2 border-dashed border-emerald-300 rounded-lg"
        @drop="onDrop($event)"
        @dragover.prevent
        @dragenter.prevent
      >
        <div
          v-for="(word, index) in answeredWords"
          :key="`answer-${index}`"
          :draggable="!locked"
          @dragstart="startDrag($event, index, 'answer')"
          class="arabic-text text-3xl px-6 py-3 bg-white border-2 border-emerald-500 rounded-lg shadow-md cursor-move hover:shadow-lg transition-all"
          :class="{ 'cursor-not-allowed opacity-50': locked }"
        >
          {{ word.text_arabic }}
        </div>
        <div v-if="answeredWords.length === 0" class="text-gray-400 text-lg py-6">
          اسحب الكلمات هنا
        </div>
      </div>
    </div>

    <!-- Available Words (Shuffled) -->
    <div class="mb-8">
      <div class="text-sm text-gray-600 mb-4">الكلمات المتاحة:</div>
      <div class="flex flex-wrap gap-3 justify-center p-4 bg-gray-50 rounded-lg min-h-[100px]">
        <div
          v-for="(word, index) in availableWords"
          :key="`available-${index}`"
          :draggable="!locked"
          @dragstart="startDrag($event, index, 'available')"
          class="arabic-text text-3xl px-6 py-3 bg-white border-2 border-gray-300 rounded-lg shadow-sm cursor-move hover:shadow-md hover:border-emerald-400 transition-all"
          :class="{ 'cursor-not-allowed opacity-50': locked }"
        >
          {{ word.text_arabic }}
        </div>
      </div>
    </div>

    <!-- Feedback -->
    <div v-if="feedback" class="mb-6">
      <div v-if="isCorrect" class="text-green-600 text-xl font-bold animate-fade-in">
        ✓ ممتاز! الترتيب صحيح
      </div>
      <div v-else class="text-red-600 text-xl font-bold animate-fade-in">
        ✗ الترتيب غير صحيح، حاول مرة أخرى
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-4 justify-center">
      <button
        v-if="!locked"
        @click="checkAnswer"
        :disabled="answeredWords.length !== ayah.words.length"
        class="px-8 py-4 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-lg font-bold disabled:bg-gray-300 disabled:cursor-not-allowed"
      >
        ✓ تحقق من الإجابة
      </button>
      <button
        v-if="!locked"
        @click="resetOrder"
        class="px-6 py-4 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
      >
        🔄 إعادة تعيين
      </button>
      <button
        v-if="locked && isCorrect"
        @click="nextAyah"
        class="px-8 py-4 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-lg font-bold"
      >
        ← التالي
      </button>
    </div>

    <!-- Hint -->
    <div class="mt-8 text-sm text-gray-500">
      <p>اسحب الكلمات وأفلتها لترتيبها بالشكل الصحيح</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  ayah: { type: Object, required: true }
})

const emit = defineEmits(['complete', 'mistake'])

const answeredWords = ref([])
const availableWords = ref([])
const draggedItem = ref(null)
const dragSource = ref(null)
const locked = ref(false)
const feedback = ref(false)
const isCorrect = ref(false)

onMounted(() => {
  shuffleWords()
})

const shuffleWords = () => {
  const words = [...props.ayah.words]
  availableWords.value = words.sort(() => Math.random() - 0.5)
  answeredWords.value = []
}

const startDrag = (event, index, source) => {
  draggedItem.value = index
  dragSource.value = source
  event.dataTransfer.effectAllowed = 'move'
}

const onDrop = (event) => {
  event.preventDefault()
  if (locked.value) return

  const source = dragSource.value
  const index = draggedItem.value

  if (source === 'available') {
    const word = availableWords.value[index]
    answeredWords.value.push(word)
    availableWords.value.splice(index, 1)
  } else if (source === 'answer') {
    // Reorder within answer area
    const word = answeredWords.value[index]
    answeredWords.value.splice(index, 1)
    answeredWords.value.push(word)
  }

  draggedItem.value = null
  dragSource.value = null
}

const checkAnswer = () => {
  if (answeredWords.value.length !== props.ayah.words.length) return

  const isOrderCorrect = answeredWords.value.every((word, index) => {
    return word.id === props.ayah.words[index].id
  })

  isCorrect.value = isOrderCorrect
  feedback.value = true
  locked.value = isOrderCorrect

  if (isOrderCorrect) {
    emit('complete', { mistakes: 0 })
  } else {
    emit('mistake')
    setTimeout(() => {
      feedback.value = false
    }, 2000)
  }
}

const resetOrder = () => {
  shuffleWords()
  feedback.value = false
  isCorrect.value = false
}

const nextAyah = () => {
  emit('next')
}
</script>
