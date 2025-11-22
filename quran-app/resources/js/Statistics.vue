<template>
  <div class="statistics min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50 p-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">الإحصائيات والتقارير</h1>
          <p class="text-gray-600 mt-1">تقرير مفصل عن تقدمك في حفظ القرآن الكريم</p>
        </div>
        <div class="text-5xl">📊</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="hifzStore.loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-emerald-500 border-t-transparent"></div>
      <p class="mt-4 text-gray-600">جاري تحميل الإحصائيات...</p>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Overview Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Completion -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold">نسبة الحفظ الكلية</h3>
            <div class="text-3xl">🎯</div>
          </div>
          <p class="text-4xl font-bold mb-2">{{ totalCompletionPercentage }}%</p>
          <div class="flex items-center gap-2 text-emerald-100 text-sm">
            <span>{{ hifzStore.completedSurahs }}/114 سورة</span>
          </div>
        </div>

        <!-- Total Ayahs -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold">إجمالي الآيات</h3>
            <div class="text-3xl">📖</div>
          </div>
          <p class="text-4xl font-bold mb-2">{{ totalMemorizedAyahs }}</p>
          <div class="text-blue-100 text-sm">
            من أصل 6236 آية
          </div>
        </div>

        <!-- Total Pages -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold">الصفحات المحفوظة</h3>
            <div class="text-3xl">📄</div>
          </div>
          <p class="text-4xl font-bold mb-2">{{ hifzStore.memorizedPages }}</p>
          <div class="text-purple-100 text-sm">
            من أصل 604 صفحة
          </div>
        </div>

        <!-- Review Count -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold">إجمالي المراجعات</h3>
            <div class="text-3xl">🔄</div>
          </div>
          <p class="text-4xl font-bold mb-2">{{ totalReviews }}</p>
          <div class="text-orange-100 text-sm">
            مراجعة مكتملة
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Progress Chart -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">توزيع الحفظ حسب الحالة</h2>
          <div class="space-y-4">
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">السور المكتملة</span>
                <span class="text-sm font-bold text-emerald-600">{{ hifzStore.completedSurahs }} سورة</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div
                  class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-full rounded-full transition-all duration-500"
                  :style="{ width: (hifzStore.completedSurahs / 114 * 100) + '%' }"
                ></div>
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">قيد الحفظ</span>
                <span class="text-sm font-bold text-blue-600">{{ hifzStore.inProgressSurahs }} سورة</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div
                  class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full transition-all duration-500"
                  :style="{ width: (hifzStore.inProgressSurahs / 114 * 100) + '%' }"
                ></div>
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">لم تبدأ</span>
                <span class="text-sm font-bold text-gray-600">{{ notStartedSurahs }} سورة</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div
                  class="bg-gradient-to-r from-gray-400 to-gray-500 h-full rounded-full transition-all duration-500"
                  :style="{ width: (notStartedSurahs / 114 * 100) + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Juz Progress -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">الإنجاز حسب الأجزاء</h2>
          <div class="grid grid-cols-5 gap-2">
            <div
              v-for="juz in 30"
              :key="juz"
              class="aspect-square rounded-lg flex items-center justify-center text-sm font-bold transition-all cursor-pointer hover:scale-110"
              :class="getJuzClass(juz)"
              :title="`الجزء ${juz}`"
            >
              {{ juz }}
            </div>
          </div>
          <div class="flex items-center justify-center gap-4 mt-4 text-xs">
            <div class="flex items-center gap-1">
              <div class="w-4 h-4 bg-green-500 rounded"></div>
              <span class="text-gray-600">مكتمل</span>
            </div>
            <div class="flex items-center gap-1">
              <div class="w-4 h-4 bg-blue-500 rounded"></div>
              <span class="text-gray-600">قيد الحفظ</span>
            </div>
            <div class="flex items-center gap-1">
              <div class="w-4 h-4 bg-gray-300 rounded"></div>
              <span class="text-gray-600">لم يبدأ</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-800">النشاط الأخير</h2>
          <select
            v-model="activityFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
          >
            <option value="all">الكل</option>
            <option value="completed">المكتمل</option>
            <option value="in_progress">قيد الحفظ</option>
          </select>
        </div>

        <div v-if="filteredHifz.length === 0" class="text-center py-8 text-gray-500">
          <div class="text-5xl mb-4">📚</div>
          <p>لا يوجد نشاط بعد</p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="hifz in filteredHifz.slice(0, 10)"
            :key="hifz.id"
            class="flex items-center justify-between p-4 bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 hover:shadow-md transition"
          >
            <div class="flex items-center gap-4">
              <div
                class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold"
                :class="hifz.status === 'completed' ? 'bg-gradient-to-br from-green-500 to-green-600' : 'bg-gradient-to-br from-blue-500 to-blue-600'"
              >
                {{ hifz.surah?.number }}
              </div>
              <div>
                <h3 class="font-semibold text-gray-800">{{ hifz.surah?.name_arabic }}</h3>
                <p class="text-sm text-gray-600">
                  من الآية {{ hifz.start_ayah }} إلى {{ hifz.end_ayah }}
                </p>
              </div>
            </div>

            <div class="text-left">
              <span
                class="px-3 py-1 rounded-full text-xs font-semibold"
                :class="hifz.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
              >
                {{ hifz.status === 'completed' ? 'مكتمل' : 'قيد الحفظ' }}
              </span>
              <p class="text-xs text-gray-500 mt-1">
                المراجعات: {{ hifz.reviewed_count || 0 }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Average Progress -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xl">
              📈
            </div>
            <h3 class="text-lg font-bold text-gray-800">متوسط التقدم</h3>
          </div>
          <p class="text-3xl font-bold text-indigo-600 mb-2">{{ averageProgress }}%</p>
          <p class="text-sm text-gray-600">متوسط الإنجاز في السور قيد الحفظ</p>
        </div>

        <!-- Current Streak -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center text-white text-xl">
              🔥
            </div>
            <h3 class="text-lg font-bold text-gray-800">التسلسل الحالي</h3>
          </div>
          <p class="text-3xl font-bold text-yellow-600 mb-2">{{ currentStreak }} يوم</p>
          <p class="text-sm text-gray-600">أيام متتالية من الحفظ أو المراجعة</p>
        </div>

        <!-- Achievement Level -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center text-white text-xl">
              ⭐
            </div>
            <h3 class="text-lg font-bold text-gray-800">مستوى الإنجاز</h3>
          </div>
          <p class="text-3xl font-bold text-pink-600 mb-2">{{ achievementLevel }}</p>
          <p class="text-sm text-gray-600">{{ achievementTitle }}</p>
        </div>
      </div>

      <!-- Achievements & Badges -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">الشارات والإنجازات</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div
            v-for="badge in badges"
            :key="badge.id"
            class="text-center p-4 rounded-xl transition-all hover:scale-105"
            :class="badge.unlocked ? 'bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-400' : 'bg-gray-50 border border-gray-200 opacity-50'"
          >
            <div class="text-4xl mb-2">{{ badge.icon }}</div>
            <h4 class="font-semibold text-sm text-gray-800 mb-1">{{ badge.title }}</h4>
            <p class="text-xs text-gray-600">{{ badge.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useHifzStore } from './store/hifz'

const hifzStore = useHifzStore()
const activityFilter = ref('all')

const notStartedSurahs = computed(() => {
  return 114 - hifzStore.completedSurahs - hifzStore.inProgressSurahs
})

const totalCompletionPercentage = computed(() => {
  return Math.round((hifzStore.completedSurahs / 114) * 100)
})

const totalMemorizedAyahs = computed(() => {
  return hifzStore.userHifz.reduce((sum, hifz) => {
    if (hifz.status === 'completed') {
      return sum + (hifz.end_ayah - hifz.start_ayah + 1)
    } else if (hifz.status === 'in_progress') {
      return sum + (hifz.memorized_count || 0)
    }
    return sum
  }, 0)
})

const totalReviews = computed(() => {
  return hifzStore.userHifz.reduce((sum, hifz) => sum + (hifz.reviewed_count || 0), 0)
})

const averageProgress = computed(() => {
  const inProgress = hifzStore.userHifz.filter(h => h.status === 'in_progress')
  if (inProgress.length === 0) return 0

  const totalProgress = inProgress.reduce((sum, hifz) => {
    const total = hifz.end_ayah - hifz.start_ayah + 1
    const memorized = hifz.memorized_count || 0
    return sum + (memorized / total * 100)
  }, 0)

  return Math.round(totalProgress / inProgress.length)
})

const currentStreak = computed(() => {
  // TODO: Calculate from backend based on last activity dates
  return 7 // Placeholder
})

const achievementLevel = computed(() => {
  const completed = hifzStore.completedSurahs
  if (completed >= 100) return 10
  if (completed >= 80) return 9
  if (completed >= 60) return 8
  if (completed >= 40) return 7
  if (completed >= 30) return 6
  if (completed >= 20) return 5
  if (completed >= 15) return 4
  if (completed >= 10) return 3
  if (completed >= 5) return 2
  return 1
})

const achievementTitle = computed(() => {
  const level = achievementLevel.value
  const titles = {
    10: 'حافظ القرآن الكريم',
    9: 'قارب الختم',
    8: 'حافظ متقدم',
    7: 'حافظ متوسط',
    6: 'طالب متميز',
    5: 'طالب مجتهد',
    4: 'طالب نشيط',
    3: 'بداية موفقة',
    2: 'أولى الخطوات',
    1: 'مبتدئ'
  }
  return titles[level]
})

const filteredHifz = computed(() => {
  if (activityFilter.value === 'all') {
    return hifzStore.userHifz
  }
  return hifzStore.userHifz.filter(h => h.status === activityFilter.value)
})

const getJuzClass = (juz) => {
  // TODO: Implement actual juz progress tracking from backend
  // For now, using a simplified version
  const progress = Math.random()
  if (progress > 0.7) {
    return 'bg-green-500 text-white shadow-md'
  } else if (progress > 0.3) {
    return 'bg-blue-500 text-white shadow-md'
  }
  return 'bg-gray-300 text-gray-600'
}

const badges = computed(() => [
  {
    id: 1,
    icon: '🌟',
    title: 'أول سورة',
    description: 'أكمل أول سورة',
    unlocked: hifzStore.completedSurahs >= 1
  },
  {
    id: 2,
    icon: '📖',
    title: '5 سور',
    description: 'أكمل 5 سور',
    unlocked: hifzStore.completedSurahs >= 5
  },
  {
    id: 3,
    icon: '⭐',
    title: '10 سور',
    description: 'أكمل 10 سور',
    unlocked: hifzStore.completedSurahs >= 10
  },
  {
    id: 4,
    icon: '🏆',
    title: 'جزء كامل',
    description: 'أكمل جزء كامل',
    unlocked: hifzStore.completedSurahs >= 20
  },
  {
    id: 5,
    icon: '🔥',
    title: 'أسبوع متواصل',
    description: '7 أيام متتالية',
    unlocked: currentStreak.value >= 7
  },
  {
    id: 6,
    icon: '🔄',
    title: 'مراجع نشيط',
    description: '50 مراجعة',
    unlocked: totalReviews.value >= 50
  },
  {
    id: 7,
    icon: '💎',
    title: 'حافظ متقدم',
    description: '50 سورة',
    unlocked: hifzStore.completedSurahs >= 50
  },
  {
    id: 8,
    icon: '👑',
    title: 'حافظ القرآن',
    description: 'أتممت الحفظ',
    unlocked: hifzStore.completedSurahs >= 114
  },
  {
    id: 9,
    icon: '📄',
    title: '100 صفحة',
    description: 'حفظت 100 صفحة',
    unlocked: hifzStore.memorizedPages >= 100
  },
  {
    id: 10,
    icon: '🎯',
    title: 'ملتزم',
    description: '30 يوم متواصل',
    unlocked: currentStreak.value >= 30
  },
  {
    id: 11,
    icon: '✨',
    title: 'مثابر',
    description: '100 مراجعة',
    unlocked: totalReviews.value >= 100
  },
  {
    id: 12,
    icon: '🌙',
    title: 'محب للقرآن',
    description: 'مراجعة يومية',
    unlocked: totalReviews.value >= 200
  }
])

onMounted(async () => {
  try {
    await hifzStore.fetchUserHifz()
    await hifzStore.fetchUserPages()
    await hifzStore.fetchStats()
  } catch (error) {
    console.error('Error loading statistics:', error)
  }
})
</script>
