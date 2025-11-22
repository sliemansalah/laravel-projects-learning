<template>
  <div class="surahs min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50 p-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">سور القرآن الكريم</h1>
          <p class="text-gray-600 mt-1">114 سورة</p>
        </div>
        <div class="text-5xl">📖</div>
      </div>

      <!-- Search Bar -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="ابحث عن سورة..."
          class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
        />
        <div class="absolute left-4 top-3.5 text-gray-400 text-xl">🔍</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="quranStore.loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-emerald-500 border-t-transparent"></div>
      <p class="mt-4 text-gray-600">جاري تحميل السور...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="quranStore.error" class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg">
      {{ quranStore.error }}
    </div>

    <!-- Surahs Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <router-link
        v-for="surah in filteredSurahs"
        :key="surah.id"
        :to="`/surah/${surah.number}`"
        class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-5 group hover:scale-105"
      >
        <div class="flex items-center justify-between mb-3">
          <!-- Surah Number Badge -->
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
              {{ surah.number }}
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-800 group-hover:text-emerald-600 transition">
                {{ surah.name_arabic }}
              </h3>
              <p class="text-sm text-gray-500">{{ surah.name_english }}</p>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between text-sm text-gray-600 mt-3 pt-3 border-t border-gray-100">
          <div class="flex items-center gap-2">
            <span class="text-lg">📄</span>
            <span>{{ surah.verses_count }} آية</span>
          </div>
          <div class="flex items-center gap-2">
            <span :class="[
              'px-2 py-1 rounded-full text-xs font-semibold',
              surah.revelation_place === 'مكة' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'
            ]">
              {{ surah.revelation_place === 'مكة' ? 'مكية' : 'مدنية' }}
            </span>
          </div>
        </div>
      </router-link>
    </div>

    <!-- Empty State -->
    <div v-if="!quranStore.loading && filteredSurahs.length === 0" class="text-center py-12">
      <div class="text-6xl mb-4">🔍</div>
      <p class="text-gray-600 text-lg">لا توجد نتائج للبحث</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useQuranStore } from './store/quran'

const quranStore = useQuranStore()
const searchQuery = ref('')

const filteredSurahs = computed(() => {
  if (!searchQuery.value) {
    return quranStore.surahs
  }

  const query = searchQuery.value.toLowerCase()
  return quranStore.surahs.filter(surah =>
    surah.name_arabic?.includes(query) ||
    surah.name_english?.toLowerCase().includes(query) ||
    surah.number?.toString().includes(query)
  )
})

onMounted(async () => {
  try {
    await quranStore.fetchSurahs()
  } catch (error) {
    console.error('Error loading surahs:', error)
  }
})
</script>
