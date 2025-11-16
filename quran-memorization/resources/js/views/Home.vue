<template>
  <div class="min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-emerald-800">
            بسم الله الرحمن الرحيم
          </h1>
          <div class="flex items-center gap-4">
            <span class="text-gray-700">{{ authStore.user?.name }}</span>
            <button
              @click="handleLogout"
              class="text-sm text-red-600 hover:text-red-700"
            >
              تسجيل الخروج
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <StatsCard
          title="الكلمات المحفوظة"
          :value="stats.mastered_words || 0"
          icon="✅"
          color="emerald"
        />
        <StatsCard
          title="قيد المراجعة"
          :value="stats.reviewing_words || 0"
          icon="📖"
          color="blue"
        />
        <StatsCard
          title="المراجعات المستحقة"
          :value="stats.due_reviews || 0"
          icon="⏰"
          color="amber"
        />
      </div>

      <!-- Review Button -->
      <div v-if="stats.due_reviews > 0" class="mb-8">
        <router-link
          to="/review"
          class="block w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-center py-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow"
        >
          <div class="text-2xl font-bold mb-2">ابدأ المراجعة اليومية</div>
          <div class="text-lg">{{ stats.due_reviews }} كلمة في انتظارك</div>
        </router-link>
      </div>

      <!-- Surahs List -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">السور المتاحة</h2>

        <div v-if="quranStore.loading" class="text-center py-8">
          <p class="text-gray-500">جاري التحميل...</p>
        </div>

        <div v-else-if="quranStore.surahs.length === 0" class="text-center py-8">
          <p class="text-gray-500">لا توجد سور متاحة حالياً</p>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="surah in quranStore.surahs"
            :key="surah.id"
            class="border border-gray-200 rounded-xl p-6 hover:border-emerald-500 hover:shadow-md transition-all cursor-pointer"
            @click="router.push(`/memorize/${surah.id}`)"
          >
            <div class="flex justify-between items-center">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                  <span class="text-emerald-700 font-bold">{{ surah.number }}</span>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-800 arabic-text">
                    {{ surah.name_arabic }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    {{ surah.name_english }} • {{ surah.total_ayahs }} آيات
                  </p>
                </div>
              </div>

              <div class="text-left">
                <div v-if="surah.progress_percentage !== undefined" class="text-sm text-gray-600 mb-1">
                  {{ Math.round(surah.progress_percentage) }}%
                </div>
                <div class="w-24 bg-gray-200 rounded-full h-2">
                  <div
                    class="bg-emerald-500 h-2 rounded-full transition-all"
                    :style="{ width: `${surah.progress_percentage || 0}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useQuranStore } from '@/stores/quran'
import { useMemorizationStore } from '@/stores/memorization'
import StatsCard from '@/components/StatsCard.vue'

const router = useRouter()
const authStore = useAuthStore()
const quranStore = useQuranStore()
const memorizationStore = useMemorizationStore()

const stats = ref({
  mastered_words: 0,
  reviewing_words: 0,
  due_reviews: 0
})

onMounted(async () => {
  await quranStore.fetchSurahs()
  const progressData = await memorizationStore.getProgress()
  stats.value = progressData
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
