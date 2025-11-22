<template>
  <div class="dashboard min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50 p-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 mb-2">مرحباً بك في تطبيق القرآن الكريم</h1>
          <p class="text-gray-600">{{ authStore.user?.name || 'ضيف' }}</p>
        </div>
        <div class="text-6xl">📖</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="hifzStore.loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-emerald-500 border-t-transparent"></div>
      <p class="mt-4 text-gray-600">جاري التحميل...</p>
    </div>

    <!-- Statistics Cards -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <!-- Total Surahs in Quran -->
      <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">عدد السور</h3>
          <div class="text-3xl">📚</div>
        </div>
        <p class="text-4xl font-bold">114</p>
        <p class="text-emerald-100 text-sm mt-2">سورة في القرآن الكريم</p>
      </div>

      <!-- Completed Surahs -->
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">السور المحفوظة</h3>
          <div class="text-3xl">✅</div>
        </div>
        <p class="text-4xl font-bold">{{ hifzStore.completedSurahs }}</p>
        <p class="text-blue-100 text-sm mt-2">سورة مكتملة</p>
      </div>

      <!-- In Progress Surahs -->
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">قيد الحفظ</h3>
          <div class="text-3xl">📝</div>
        </div>
        <p class="text-4xl font-bold">{{ hifzStore.inProgressSurahs }}</p>
        <p class="text-purple-100 text-sm mt-2">سورة قيد الحفظ</p>
      </div>

      <!-- Memorized Pages -->
      <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">الصفحات المحفوظة</h3>
          <div class="text-3xl">📄</div>
        </div>
        <p class="text-4xl font-bold">{{ hifzStore.memorizedPages }}</p>
        <p class="text-orange-100 text-sm mt-2">من أصل 604 صفحة</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">الإجراءات السريعة</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <router-link
          to="/surahs"
          class="flex items-center justify-between p-4 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition group"
        >
          <div>
            <h3 class="font-semibold text-gray-800">تصفح السور</h3>
            <p class="text-sm text-gray-600">استعرض جميع سور القرآن</p>
          </div>
          <div class="text-2xl group-hover:scale-110 transition">📖</div>
        </router-link>

        <router-link
          to="/hifz"
          class="flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition group"
        >
          <div>
            <h3 class="font-semibold text-gray-800">إدارة الحفظ</h3>
            <p class="text-sm text-gray-600">تتبع تقدمك في الحفظ</p>
          </div>
          <div class="text-2xl group-hover:scale-110 transition">🎯</div>
        </router-link>

        <router-link
          to="/statistics"
          class="flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition group"
        >
          <div>
            <h3 class="font-semibold text-gray-800">الإحصائيات</h3>
            <p class="text-sm text-gray-600">شاهد تقدمك التفصيلي</p>
          </div>
          <div class="text-2xl group-hover:scale-110 transition">📊</div>
        </router-link>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">النشاط الأخير</h2>
      <div v-if="hifzStore.userHifz.length === 0" class="text-center py-8 text-gray-500">
        <div class="text-5xl mb-4">📚</div>
        <p>لم تبدأ أي حفظ بعد</p>
        <router-link to="/surahs" class="inline-block mt-4 px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
          ابدأ الآن
        </router-link>
      </div>
      <div v-else class="space-y-3">
        <div
          v-for="hifz in hifzStore.userHifz.slice(0, 5)"
          :key="hifz.id"
          class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition"
        >
          <div class="flex items-center gap-4">
            <div class="text-2xl">
              {{ hifz.status === 'completed' ? '✅' : '📝' }}
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">{{ hifz.surah?.name_arabic || 'سورة' }}</h3>
              <p class="text-sm text-gray-600">
                من الآية {{ hifz.start_ayah }} إلى {{ hifz.end_ayah }}
              </p>
            </div>
          </div>
          <div class="text-left">
            <span :class="[
              'px-3 py-1 rounded-full text-sm font-semibold',
              hifz.status === 'completed' ? 'bg-green-100 text-green-800' :
              hifz.status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
              'bg-gray-100 text-gray-800'
            ]">
              {{ hifz.status === 'completed' ? 'مكتمل' : hifz.status === 'in_progress' ? 'قيد الحفظ' : 'جديد' }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './store/auth'
import { useHifzStore } from './store/hifz'

const authStore = useAuthStore()
const hifzStore = useHifzStore()

onMounted(async () => {
  try {
    await hifzStore.fetchUserHifz()
    await hifzStore.fetchUserPages()
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  }
})
</script>
