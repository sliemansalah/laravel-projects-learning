<template>
  <div class="hifz min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50 p-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">إدارة الحفظ والمراجعة</h1>
          <p class="text-gray-600 mt-1">تتبع تقدمك في حفظ القرآن الكريم</p>
        </div>
        <div class="text-5xl">🎯</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="hifzStore.loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-emerald-500 border-t-transparent"></div>
      <p class="mt-4 text-gray-600">جاري التحميل...</p>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-5 text-white">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold">السور المكتملة</h3>
            <div class="text-2xl">✅</div>
          </div>
          <p class="text-3xl font-bold">{{ hifzStore.completedSurahs }}</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold">قيد الحفظ</h3>
            <div class="text-2xl">📝</div>
          </div>
          <p class="text-3xl font-bold">{{ hifzStore.inProgressSurahs }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold">الصفحات المحفوظة</h3>
            <div class="text-2xl">📄</div>
          </div>
          <p class="text-3xl font-bold">{{ hifzStore.memorizedPages }}</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-5 text-white">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold">نسبة الإنجاز</h3>
            <div class="text-2xl">📊</div>
          </div>
          <p class="text-3xl font-bold">{{ completionPercentage }}%</p>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex gap-2 border-b border-gray-200 mb-6">
          <button
            @click="activeTab = 'current'"
            :class="[
              'px-6 py-3 font-semibold transition border-b-2',
              activeTab === 'current'
                ? 'text-emerald-600 border-emerald-600'
                : 'text-gray-600 border-transparent hover:text-emerald-600'
            ]"
          >
            الحفظ الحالي
          </button>
          <button
            @click="activeTab = 'review'"
            :class="[
              'px-6 py-3 font-semibold transition border-b-2',
              activeTab === 'review'
                ? 'text-emerald-600 border-emerald-600'
                : 'text-gray-600 border-transparent hover:text-emerald-600'
            ]"
          >
            المراجعة
          </button>
          <button
            @click="activeTab = 'completed'"
            :class="[
              'px-6 py-3 font-semibold transition border-b-2',
              activeTab === 'completed'
                ? 'text-emerald-600 border-emerald-600'
                : 'text-gray-600 border-transparent hover:text-emerald-600'
            ]"
          >
            المكتمل
          </button>
        </div>

        <!-- Current Hifz Tab -->
        <div v-if="activeTab === 'current'">
          <div v-if="inProgressHifz.length === 0" class="text-center py-12 text-gray-500">
            <div class="text-6xl mb-4">📚</div>
            <p class="text-lg mb-4">لا توجد سور قيد الحفظ حالياً</p>
            <router-link
              to="/surahs"
              class="inline-block px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition"
            >
              ابدأ حفظ سورة جديدة
            </router-link>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="hifz in inProgressHifz"
              :key="hifz.id"
              class="p-5 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-200 hover:shadow-lg transition"
            >
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ hifz.surah?.number }}
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-gray-800">{{ hifz.surah?.name_arabic }}</h3>
                    <p class="text-sm text-gray-600">
                      من الآية {{ hifz.start_ayah }} إلى {{ hifz.end_ayah }}
                      ({{ hifz.end_ayah - hifz.start_ayah + 1 }} آية)
                    </p>
                  </div>
                </div>

                <router-link
                  :to="`/surah/${hifz.surah?.number}`"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                >
                  فتح السورة
                </router-link>
              </div>

              <!-- Progress Bar -->
              <div class="mb-4">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                  <span>التقدم</span>
                  <span>{{ hifz.memorized_count || 0 }} / {{ hifz.end_ayah - hifz.start_ayah + 1 }} آية</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                  <div
                    class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full transition-all duration-500"
                    :style="{ width: getProgressPercentage(hifz) + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Review Count -->
              <div class="flex items-center justify-between text-sm mb-4">
                <span class="text-gray-600">عدد المراجعات: {{ hifz.reviewed_count || 0 }}</span>
                <span class="text-gray-500">
                  آخر مراجعة: {{ formatDate(hifz.last_reviewed_at) }}
                </span>
              </div>

              <!-- Actions -->
              <div class="flex gap-2">
                <button
                  @click="incrementMemorized(hifz)"
                  class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm"
                >
                  ✅ حفظت آية جديدة
                </button>
                <button
                  @click="markAsReviewed(hifz)"
                  class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm"
                >
                  🔄 راجعت الآن
                </button>
                <button
                  @click="markAsCompleted(hifz)"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm"
                  :disabled="!canMarkAsCompleted(hifz)"
                >
                  ✔️ اكتمل
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Review Tab -->
        <div v-if="activeTab === 'review'">
          <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
            <h3 class="font-semibold text-purple-900 mb-2">💡 نظام المراجعة الذكي</h3>
            <p class="text-sm text-purple-700">
              يعتمد على خوارزمية التكرار المتباعد لتحديد السور التي تحتاج للمراجعة بناءً على آخر مراجعة
            </p>
          </div>

          <div v-if="needsReview.length === 0" class="text-center py-12 text-gray-500">
            <div class="text-6xl mb-4">✨</div>
            <p class="text-lg">رائع! لا توجد سور تحتاج للمراجعة الآن</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="hifz in needsReview"
              :key="hifz.id"
              class="p-5 bg-gradient-to-br from-purple-50 to-white rounded-xl border-2"
              :class="getReviewUrgencyClass(hifz)"
            >
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ hifz.surah?.number }}
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-gray-800">{{ hifz.surah?.name_arabic }}</h3>
                    <p class="text-sm text-gray-600">
                      من الآية {{ hifz.start_ayah }} إلى {{ hifz.end_ayah }}
                    </p>
                  </div>
                </div>

                <div class="text-left">
                  <span :class="getReviewUrgencyBadge(hifz)">
                    {{ getReviewUrgencyText(hifz) }}
                  </span>
                  <p class="text-xs text-gray-500 mt-1">
                    آخر مراجعة: {{ getDaysSinceReview(hifz) }}
                  </p>
                </div>
              </div>

              <div class="flex gap-2">
                <router-link
                  :to="`/surah/${hifz.surah?.number}`"
                  class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm text-center"
                >
                  بدء المراجعة
                </router-link>
                <button
                  @click="markAsReviewed(hifz)"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm"
                >
                  ✓ تم المراجعة
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Completed Tab -->
        <div v-if="activeTab === 'completed'">
          <div v-if="completedHifz.length === 0" class="text-center py-12 text-gray-500">
            <div class="text-6xl mb-4">🎯</div>
            <p class="text-lg">لم تكمل أي سورة بعد</p>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              v-for="hifz in completedHifz"
              :key="hifz.id"
              class="p-5 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-200 hover:shadow-lg transition"
            >
              <div class="flex items-center gap-4 mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-2xl">
                  ✓
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-bold text-gray-800">{{ hifz.surah?.name_arabic }}</h3>
                  <p class="text-sm text-gray-600">
                    {{ hifz.end_ayah - hifz.start_ayah + 1 }} آية
                  </p>
                </div>
              </div>

              <div class="flex items-center justify-between text-xs text-gray-600">
                <span>اكتمل في: {{ formatDate(hifz.completed_at) }}</span>
                <span>المراجعات: {{ hifz.reviewed_count || 0 }}</span>
              </div>

              <router-link
                :to="`/surah/${hifz.surah?.number}`"
                class="block mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm text-center"
              >
                مراجعة السورة
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <div
      v-if="successMessage"
      class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg animate-fade-in"
    >
      {{ successMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useHifzStore } from './store/hifz'
import { format, parseISO, differenceInDays } from 'date-fns'
import { ar } from 'date-fns/locale'

const hifzStore = useHifzStore()
const activeTab = ref('current')
const successMessage = ref('')

const inProgressHifz = computed(() =>
  hifzStore.userHifz.filter(h => h.status === 'in_progress')
)

const completedHifz = computed(() =>
  hifzStore.userHifz.filter(h => h.status === 'completed')
)

const needsReview = computed(() => {
  return hifzStore.userHifz
    .filter(h => h.status === 'completed' || h.status === 'in_progress')
    .filter(h => {
      if (!h.last_reviewed_at) return true
      const daysSince = differenceInDays(new Date(), parseISO(h.last_reviewed_at))
      return daysSince >= 3 // Need review every 3 days minimum
    })
    .sort((a, b) => {
      const daysA = getDaysSinceReviewNum(a)
      const daysB = getDaysSinceReviewNum(b)
      return daysB - daysA // Most urgent first
    })
})

const completionPercentage = computed(() => {
  const total = 114 // Total surahs in Quran
  const completed = hifzStore.completedSurahs
  return Math.round((completed / total) * 100)
})

const getProgressPercentage = (hifz) => {
  const total = hifz.end_ayah - hifz.start_ayah + 1
  const memorized = hifz.memorized_count || 0
  return Math.min(Math.round((memorized / total) * 100), 100)
}

const canMarkAsCompleted = (hifz) => {
  const total = hifz.end_ayah - hifz.start_ayah + 1
  return (hifz.memorized_count || 0) >= total
}

const formatDate = (dateString) => {
  if (!dateString) return 'لم يتم بعد'
  try {
    return format(parseISO(dateString), 'yyyy/MM/dd', { locale: ar })
  } catch {
    return 'تاريخ غير صالح'
  }
}

const getDaysSinceReview = (hifz) => {
  if (!hifz.last_reviewed_at) return 'لم تتم المراجعة'
  const days = differenceInDays(new Date(), parseISO(hifz.last_reviewed_at))
  return `منذ ${days} يوم`
}

const getDaysSinceReviewNum = (hifz) => {
  if (!hifz.last_reviewed_at) return 999
  return differenceInDays(new Date(), parseISO(hifz.last_reviewed_at))
}

const getReviewUrgencyClass = (hifz) => {
  const days = getDaysSinceReviewNum(hifz)
  if (days > 14) return 'border-red-400'
  if (days > 7) return 'border-orange-400'
  return 'border-purple-200'
}

const getReviewUrgencyBadge = (hifz) => {
  const days = getDaysSinceReviewNum(hifz)
  if (days > 14) return 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800'
  if (days > 7) return 'px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800'
  return 'px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800'
}

const getReviewUrgencyText = (hifz) => {
  const days = getDaysSinceReviewNum(hifz)
  if (days > 14) return '🔴 عاجل جداً'
  if (days > 7) return '🟠 مطلوب قريباً'
  return '🟣 مراجعة عادية'
}

const incrementMemorized = async (hifz) => {
  try {
    const newCount = (hifz.memorized_count || 0) + 1
    await hifzStore.updateHifzStatus(hifz.id, 'in_progress', newCount, hifz.reviewed_count || 0)
    showSuccess('رائع! تم تسجيل حفظ آية جديدة')
  } catch (error) {
    console.error('Error updating hifz:', error)
  }
}

const markAsReviewed = async (hifz) => {
  try {
    const newReviewCount = (hifz.reviewed_count || 0) + 1
    await hifzStore.updateHifzStatus(hifz.id, hifz.status, hifz.memorized_count || 0, newReviewCount)
    showSuccess('تم تسجيل المراجعة بنجاح')
  } catch (error) {
    console.error('Error updating review:', error)
  }
}

const markAsCompleted = async (hifz) => {
  if (!canMarkAsCompleted(hifz)) {
    showSuccess('الرجاء إكمال حفظ جميع الآيات أولاً')
    return
  }

  try {
    await hifzStore.updateHifzStatus(hifz.id, 'completed', hifz.memorized_count, hifz.reviewed_count || 0)
    showSuccess('مبارك! تم إكمال الحفظ بنجاح 🎉')
  } catch (error) {
    console.error('Error completing hifz:', error)
  }
}

const showSuccess = (message) => {
  successMessage.value = message
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

onMounted(async () => {
  try {
    await hifzStore.fetchUserHifz()
    await hifzStore.fetchUserPages()
  } catch (error) {
    console.error('Error loading hifz data:', error)
  }
})
</script>

<style scoped>
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
</style>
