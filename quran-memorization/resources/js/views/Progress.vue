<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-4xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <button
            @click="router.back()"
            class="flex items-center gap-2 text-gray-600 hover:text-gray-800"
          >
            <span>←</span>
            <span>رجوع</span>
          </button>
          <h1 class="text-xl font-bold text-emerald-800">تقدمك</h1>
          <div class="w-20"></div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 py-8">
      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-emerald-500 border-t-transparent mx-auto"></div>
        <p class="text-gray-600 mt-4">جاري التحميل...</p>
      </div>

      <div v-else class="space-y-6">
        <!-- Overall Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="bg-white rounded-xl p-4 shadow text-center">
            <div class="text-3xl font-bold text-emerald-600">
              {{ stats.mastered_words || 0 }}
            </div>
            <div class="text-sm text-gray-600">كلمات متقنة</div>
          </div>
          <div class="bg-white rounded-xl p-4 shadow text-center">
            <div class="text-3xl font-bold text-blue-600">
              {{ stats.reviewing_words || 0 }}
            </div>
            <div class="text-sm text-gray-600">قيد المراجعة</div>
          </div>
          <div class="bg-white rounded-xl p-4 shadow text-center">
            <div class="text-3xl font-bold text-amber-600">
              {{ stats.learning_words || 0 }}
            </div>
            <div class="text-sm text-gray-600">قيد الحفظ</div>
          </div>
          <div class="bg-white rounded-xl p-4 shadow text-center">
            <div class="text-3xl font-bold text-purple-600">
              {{ stats.total_reviews || 0 }}
            </div>
            <div class="text-sm text-gray-600">إجمالي المراجعات</div>
          </div>
        </div>

        <!-- Accuracy Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-4">نسبة الدقة</h3>
          <div class="flex items-center justify-center">
            <div class="relative w-48 h-48">
              <svg class="transform -rotate-90 w-48 h-48">
                <circle
                  cx="96"
                  cy="96"
                  r="80"
                  stroke="#e5e7eb"
                  stroke-width="12"
                  fill="transparent"
                />
                <circle
                  cx="96"
                  cy="96"
                  r="80"
                  stroke="#10b981"
                  stroke-width="12"
                  fill="transparent"
                  :stroke-dasharray="circumference"
                  :stroke-dashoffset="dashOffset"
                  class="transition-all duration-1000"
                />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                  <div class="text-4xl font-bold text-emerald-600">
                    {{ stats.accuracy || 0 }}%
                  </div>
                  <div class="text-sm text-gray-600">دقة</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-4">توزيع الحالة</h3>
          <div class="space-y-3">
            <div>
              <div class="flex justify-between text-sm mb-1">
                <span>متقنة</span>
                <span>{{ masteredPercentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-green-500 h-3 rounded-full transition-all duration-500"
                  :style="{ width: masteredPercentage + '%' }"
                ></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-sm mb-1">
                <span>قيد المراجعة</span>
                <span>{{ reviewingPercentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-blue-500 h-3 rounded-full transition-all duration-500"
                  :style="{ width: reviewingPercentage + '%' }"
                ></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-sm mb-1">
                <span>قيد الحفظ</span>
                <span>{{ learningPercentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-amber-500 h-3 rounded-full transition-all duration-500"
                  :style="{ width: learningPercentage + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Words Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-4">إجمالي الكلمات</h3>
          <div class="text-center">
            <div class="text-6xl font-bold text-emerald-600 mb-2">
              {{ stats.total_words || 0 }}
            </div>
            <p class="text-gray-600">كلمة مسجلة في النظام</p>
          </div>
        </div>

        <!-- Achievement Placeholder -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-4">الإنجازات</h3>
          <div class="text-center text-gray-500 py-8">
            <div class="text-6xl mb-4">🏆</div>
            <p class="text-lg">قريباً: نظام الإنجازات والشارات</p>
            <p class="text-sm mt-2">استمر في الحفظ والمراجعة لكسب الإنجازات!</p>
          </div>
        </div>

        <!-- Motivational Message -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl shadow-lg p-6 text-white text-center">
          <div class="text-4xl mb-3">💪</div>
          <h3 class="text-xl font-bold mb-2">استمر في التقدم!</h3>
          <p class="opacity-90">
            {{ getMotivationalMessage() }}
          </p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useMemorizationStore } from '@/stores/memorization'

const router = useRouter()
const memorizationStore = useMemorizationStore()

const loading = ref(true)
const stats = ref({
  total_words: 0,
  mastered_words: 0,
  reviewing_words: 0,
  learning_words: 0,
  total_reviews: 0,
  accuracy: 0
})

// Circle calculations for progress ring
const circumference = computed(() => 2 * Math.PI * 80)
const dashOffset = computed(() => {
  const percentage = stats.value.accuracy || 0
  return circumference.value - (percentage / 100) * circumference.value
})

const masteredPercentage = computed(() => {
  if (stats.value.total_words === 0) return 0
  return Math.round((stats.value.mastered_words / stats.value.total_words) * 100)
})

const reviewingPercentage = computed(() => {
  if (stats.value.total_words === 0) return 0
  return Math.round((stats.value.reviewing_words / stats.value.total_words) * 100)
})

const learningPercentage = computed(() => {
  if (stats.value.total_words === 0) return 0
  return Math.round((stats.value.learning_words / stats.value.total_words) * 100)
})

const getMotivationalMessage = () => {
  const mastered = stats.value.mastered_words || 0

  if (mastered === 0) {
    return 'ابدأ رحلة الحفظ الآن! كل رحلة تبدأ بخطوة واحدة'
  } else if (mastered < 10) {
    return 'بداية رائعة! استمر في هذا المعدل'
  } else if (mastered < 50) {
    return 'تقدم ممتاز! أنت في الطريق الصحيح'
  } else if (mastered < 100) {
    return 'مذهل! أنت تحفظ بثبات واستمرارية'
  } else {
    return 'ماشاء الله! تقدم مميز جداً، بارك الله فيك'
  }
}

onMounted(async () => {
  try {
    const data = await memorizationStore.getProgress()
    stats.value = data
  } catch (error) {
    console.error('Error loading progress:', error)
  } finally {
    loading.value = false
  }
})
</script>
