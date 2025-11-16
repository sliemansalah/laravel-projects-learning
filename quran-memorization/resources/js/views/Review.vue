<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-10">
      <div class="max-w-4xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <button
            @click="router.back()"
            class="flex items-center gap-2 text-gray-600 hover:text-gray-800"
          >
            <span>←</span>
            <span>رجوع</span>
          </button>
          <h1 class="text-xl font-bold text-emerald-800">المراجعة اليومية</h1>
          <div class="text-sm text-gray-600">
            {{ reviewedCount }}/{{ totalItems }}
          </div>
        </div>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-500 border-t-transparent mx-auto mb-4"></div>
        <p class="text-gray-600">جاري تحميل المراجعة...</p>
      </div>
    </div>

    <!-- No Reviews -->
    <div v-else-if="!currentWord && reviewItems.length === 0" class="max-w-4xl mx-auto px-4 py-20">
      <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <div class="text-6xl mb-6">🎉</div>
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
          أحسنت! لا توجد مراجعات اليوم
        </h2>
        <p class="text-gray-600 mb-8">
          استمر في الحفظ وسنذكرك بالمراجعة في الوقت المناسب
        </p>
        <router-link
          to="/"
          class="inline-block px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:shadow-lg transition-all font-bold"
        >
          العودة للرئيسية
        </router-link>
      </div>
    </div>

    <!-- Review Interface -->
    <main v-else-if="currentWord" class="max-w-4xl mx-auto px-4 py-8">
      <!-- Progress Bar -->
      <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
          <span>التقدم</span>
          <span>{{ Math.round((reviewedCount / totalItems) * 100) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
          <div
            class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-500"
            :style="{ width: `${(reviewedCount / totalItems) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Review Card -->
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-6 text-center">
          <h3 class="text-lg mb-2">ما معنى هذه الكلمة؟</h3>
          <div class="text-sm opacity-90">
            {{ currentWord.word.ayah.surah.name_arabic }} • الآية {{ currentWord.word.ayah.number }}
          </div>
        </div>

        <!-- Word Display -->
        <div class="p-8">
          <div v-if="!showAnswer" class="text-center">
            <div class="arabic-text text-6xl text-gray-800 mb-8">
              {{ currentWord.word.text_arabic }}
            </div>

            <button
              @click="revealAnswer"
              class="px-12 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition-all font-bold text-lg"
            >
              إظهار الإجابة
            </button>
          </div>

          <!-- Answer Revealed -->
          <div v-else class="space-y-6">
            <div class="text-center">
              <div class="arabic-text text-6xl text-gray-800 mb-4">
                {{ currentWord.word.text_arabic }}
              </div>
              <div class="text-2xl text-gray-700 font-bold mb-2">
                {{ currentWord.word.translation }}
              </div>
              <div class="text-gray-600 text-lg">
                {{ currentWord.word.meaning }}
              </div>
            </div>

            <!-- Context -->
            <div class="bg-emerald-50 rounded-xl p-6">
              <h4 class="font-bold text-emerald-800 mb-3 text-center">
                في السياق:
              </h4>
              <p class="arabic-text text-2xl text-center text-gray-800 leading-loose">
                {{ currentWord.word.ayah.text_arabic }}
              </p>
            </div>

            <!-- Response Buttons -->
            <div class="space-y-3">
              <p class="text-center text-gray-700 font-medium mb-4">
                هل عرفت الإجابة؟
              </p>

              <button
                @click="submitResponse(true)"
                :disabled="submitting"
                class="w-full py-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:shadow-lg transition-all font-bold text-lg disabled:opacity-50"
              >
                ✓ نعم، أعرفها
              </button>

              <button
                @click="submitResponse(false)"
                :disabled="submitting"
                class="w-full py-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg transition-all font-bold text-lg disabled:opacity-50"
              >
                ✗ لا، لم أعرفها
              </button>
            </div>

            <!-- Strength Indicator -->
            <div class="text-center pt-4">
              <div class="text-sm text-gray-600 mb-2">
                القوة الحالية: {{ currentWord.strength }}/10
              </div>
              <div class="flex gap-1 justify-center">
                <div
                  v-for="i in 10"
                  :key="i"
                  :class="i <= currentWord.strength ? 'bg-emerald-500' : 'bg-gray-300'"
                  class="w-8 h-2 rounded-full transition-colors"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Session Stats -->
      <div class="grid grid-cols-2 gap-4 mt-6">
        <div class="bg-white rounded-xl p-4 shadow text-center">
          <div class="text-3xl font-bold text-green-600">{{ correctCount }}</div>
          <div class="text-sm text-gray-600">صحيحة</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow text-center">
          <div class="text-3xl font-bold text-red-600">{{ wrongCount }}</div>
          <div class="text-sm text-gray-600">خاطئة</div>
        </div>
      </div>
    </main>

    <!-- Completion Modal -->
    <div v-if="showCompletionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full text-center animate-fade-in">
        <div class="text-6xl mb-4">🎉</div>
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
          أحسنت!
        </h2>
        <p class="text-gray-600 mb-6">
          أكملت مراجعة {{ totalItems }} كلمة
        </p>

        <div class="grid grid-cols-2 gap-4 mb-6">
          <div class="bg-green-50 rounded-lg p-4">
            <div class="text-2xl font-bold text-green-600">{{ correctCount }}</div>
            <div class="text-sm text-gray-600">صحيحة</div>
          </div>
          <div class="bg-red-50 rounded-lg p-4">
            <div class="text-2xl font-bold text-red-600">{{ wrongCount }}</div>
            <div class="text-sm text-gray-600">خاطئة</div>
          </div>
        </div>

        <div class="text-lg text-gray-700 mb-6">
          نسبة النجاح: <span class="font-bold text-emerald-600">{{ accuracy }}%</span>
        </div>

        <button
          @click="router.push('/')"
          class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:shadow-lg transition-all font-bold"
        >
          العودة للرئيسية
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useMemorizationStore } from '@/stores/memorization'

const router = useRouter()
const memorizationStore = useMemorizationStore()

const loading = ref(true)
const reviewItems = ref([])
const currentIndex = ref(0)
const showAnswer = ref(false)
const submitting = ref(false)
const correctCount = ref(0)
const wrongCount = ref(0)
const showCompletionModal = ref(false)

const currentWord = computed(() => reviewItems.value[currentIndex.value])
const totalItems = computed(() => reviewItems.value.length)
const reviewedCount = computed(() => currentIndex.value)
const accuracy = computed(() => {
  const total = correctCount.value + wrongCount.value
  return total > 0 ? Math.round((correctCount.value / total) * 100) : 0
})

onMounted(async () => {
  try {
    const data = await memorizationStore.getTodayReview()
    reviewItems.value = data.items
  } catch (error) {
    console.error('Error loading reviews:', error)
  } finally {
    loading.value = false
  }
})

const revealAnswer = () => {
  showAnswer.value = true
}

const submitResponse = async (isCorrect) => {
  submitting.value = true

  try {
    await memorizationStore.submitWord(currentWord.value.word_id, isCorrect)

    if (isCorrect) {
      correctCount.value++
    } else {
      wrongCount.value++
    }

    // Move to next word
    setTimeout(() => {
      if (currentIndex.value < reviewItems.value.length - 1) {
        currentIndex.value++
        showAnswer.value = false
      } else {
        // Session complete
        completeSession()
      }
      submitting.value = false
    }, 500)
  } catch (error) {
    console.error('Error submitting response:', error)
    submitting.value = false
  }
}

const completeSession = async () => {
  try {
    await memorizationStore.completeSession()
    showCompletionModal.value = true
  } catch (error) {
    console.error('Error completing session:', error)
  }
}
</script>
