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
          <h1 v-if="surah" class="text-xl font-bold text-emerald-800 arabic-text">
            سورة {{ surah.name_arabic }}
          </h1>
          <div class="w-20"></div>
        </div>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-500 border-t-transparent mx-auto mb-4"></div>
        <p class="text-gray-600">جاري التحميل...</p>
      </div>
    </div>

    <!-- Main Content -->
    <main v-else-if="surah" class="max-w-4xl mx-auto px-4 py-8">
      <!-- Surah Info Card -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="text-center">
          <h2 class="text-3xl font-bold text-gray-800 arabic-text mb-2">
            {{ surah.name_arabic }}
          </h2>
          <p class="text-gray-600">
            {{ surah.name_english }} • {{ surah.total_ayahs }} آيات
          </p>
          <div class="mt-4">
            <div class="flex items-center justify-center gap-2 text-sm text-gray-600 mb-2">
              <span>{{ Math.round(surah.progress_percentage || 0) }}%</span>
              <span>مكتمل</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div
                class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-500"
                :style="{ width: `${surah.progress_percentage || 0}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ayahs List -->
      <div class="space-y-4">
        <div
          v-for="ayah in surah.ayahs"
          :key="ayah.id"
          class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
        >
          <!-- Ayah Header -->
          <div class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-4">
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold">الآية {{ ayah.number }}</span>
              <div v-if="ayah.user_progress" class="text-sm">
                {{ getStatusText(ayah.user_progress.status) }}
              </div>
            </div>
          </div>

          <!-- Ayah Content -->
          <div class="p-6">
            <!-- Arabic Text -->
            <div class="text-center mb-6">
              <p class="arabic-text text-3xl leading-loose text-gray-800 mb-4">
                {{ ayah.text_arabic }}
              </p>
              <p class="text-gray-600 text-lg">
                {{ ayah.translation_ar }}
              </p>
            </div>

            <!-- Tafseer -->
            <div v-if="showTafseer[ayah.id]" class="bg-emerald-50 rounded-lg p-4 mb-4">
              <h4 class="font-bold text-emerald-800 mb-2">التفسير:</h4>
              <p class="text-gray-700 leading-relaxed">{{ ayah.tafseer }}</p>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 justify-center">
              <button
                @click="toggleTafseer(ayah.id)"
                class="px-6 py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-colors"
              >
                {{ showTafseer[ayah.id] ? 'إخفاء' : 'عرض' }} التفسير
              </button>

              <button
                @click="startMemorizing(ayah)"
                class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:shadow-lg transition-all font-bold"
              >
                {{ ayah.user_progress ? 'راجع الآية' : 'ابدأ الحفظ' }}
              </button>

              <button
                v-if="ayah.audio_url"
                @click="playAudio(ayah.audio_url)"
                class="px-6 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors"
              >
                🔊 استماع
              </button>
            </div>

            <!-- Words Preview -->
            <div v-if="showWords[ayah.id]" class="mt-6 pt-6 border-t border-gray-200">
              <h4 class="font-bold text-gray-800 mb-4 text-center">الكلمات:</h4>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div
                  v-for="word in ayah.words"
                  :key="word.id"
                  :class="getWordClass(word)"
                  class="p-3 rounded-lg text-center border-2 transition-all"
                >
                  <div class="arabic-text text-2xl mb-2">{{ word.text_arabic }}</div>
                  <div class="text-sm text-gray-600">{{ word.translation }}</div>
                  <div v-if="word.user_status" class="text-xs mt-2">
                    <span :class="getStrengthColor(word.user_status.strength)">
                      ⭐ {{ word.user_status.strength }}/10
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <button
              @click="toggleWords(ayah.id)"
              class="mt-4 w-full py-2 text-gray-600 hover:text-gray-800 text-sm"
            >
              {{ showWords[ayah.id] ? '▲ إخفاء الكلمات' : '▼ عرض الكلمات' }}
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Memorization Modal (قريباً) -->
    <!-- سنضيف modal للحفظ التفاعلي -->
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuranStore } from '@/stores/quran'
import { useMemorizationStore } from '@/stores/memorization'

const router = useRouter()
const route = useRoute()
const quranStore = useQuranStore()
const memorizationStore = useMemorizationStore()

const surah = ref(null)
const loading = ref(true)
const showTafseer = reactive({})
const showWords = reactive({})

onMounted(async () => {
  try {
    const surahId = route.params.surahId
    surah.value = await quranStore.fetchSurah(surahId)
  } catch (error) {
    console.error('Error loading surah:', error)
  } finally {
    loading.value = false
  }
})

const toggleTafseer = (ayahId) => {
  showTafseer[ayahId] = !showTafseer[ayahId]
}

const toggleWords = (ayahId) => {
  showWords[ayahId] = !showWords[ayahId]
}

const startMemorizing = async (ayah) => {
  try {
    await memorizationStore.startAyah(ayah.id)
    // يمكن فتح modal أو الانتقال لصفحة الحفظ
    router.push(`/memorize/${surah.value.id}/ayah/${ayah.id}`)
  } catch (error) {
    console.error('Error starting memorization:', error)
  }
}

const playAudio = (audioUrl) => {
  const audio = new Audio(audioUrl)
  audio.play()
}

const getStatusText = (status) => {
  const statusMap = {
    new: 'جديد',
    learning: 'قيد الحفظ',
    reviewing: 'مراجعة',
    mastered: 'متقن'
  }
  return statusMap[status] || 'غير محدد'
}

const getWordClass = (word) => {
  if (!word.user_status) return 'border-gray-300 bg-gray-50'

  const statusColors = {
    new: 'border-blue-300 bg-blue-50',
    learning: 'border-amber-300 bg-amber-50',
    reviewing: 'border-emerald-300 bg-emerald-50',
    mastered: 'border-green-500 bg-green-50'
  }
  return statusColors[word.user_status.status] || 'border-gray-300 bg-gray-50'
}

const getStrengthColor = (strength) => {
  if (strength >= 8) return 'text-green-600 font-bold'
  if (strength >= 5) return 'text-emerald-600'
  if (strength >= 3) return 'text-amber-600'
  return 'text-gray-600'
}
</script>
