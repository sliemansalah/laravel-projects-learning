<template>
  <div id="app" class="min-h-screen transition-colors duration-300" :class="isDark ? 'dark bg-gray-900' : 'bg-gradient-to-b from-slate-50 to-slate-100'">
    <!-- Navigation -->
    <nav v-if="authStore.isAuthenticated" class="shadow sticky top-0 z-40 transition-colors duration-300" :class="isDark ? 'bg-gray-800' : 'bg-white'">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo -->
          <div class="text-2xl font-bold text-emerald-600">
            📖 تطبيق القرآن الكريم
          </div>

          <!-- Menu -->
          <div class="flex gap-6 items-center">
            <router-link
              to="/"
              class="transition"
              :class="isDark ? 'text-gray-300 hover:text-emerald-400' : 'text-gray-700 hover:text-emerald-600'"
            >
              الرئيسية
            </router-link>
            <router-link
              to="/surahs"
              class="transition"
              :class="isDark ? 'text-gray-300 hover:text-emerald-400' : 'text-gray-700 hover:text-emerald-600'"
            >
              السور
            </router-link>
            <router-link
              to="/hifz"
              class="transition"
              :class="isDark ? 'text-gray-300 hover:text-emerald-400' : 'text-gray-700 hover:text-emerald-600'"
            >
              الحفظ
            </router-link>
            <router-link
              to="/statistics"
              class="transition"
              :class="isDark ? 'text-gray-300 hover:text-emerald-400' : 'text-gray-700 hover:text-emerald-600'"
            >
              الإحصائيات
            </router-link>

            <!-- Dark Mode Toggle -->
            <button
              @click="toggleDarkMode"
              class="p-2 rounded-lg transition-all hover:scale-110"
              :class="isDark ? 'bg-gray-700 text-yellow-400' : 'bg-gray-100 text-gray-600'"
              :title="isDark ? 'الوضع النهاري' : 'الوضع الليلي'"
            >
              <span class="text-xl">{{ isDark ? '☀️' : '🌙' }}</span>
            </button>

            <button
              @click="logout"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition"
            >
              تسجيل الخروج
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto px-4 py-8">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './store/auth'
import { useRouter } from 'vue-router'
import { useDarkMode } from './composables/useDarkMode'

const authStore = useAuthStore()
const router = useRouter()
const { isDark, toggleDarkMode, initTheme } = useDarkMode()

onMounted(() => {
  authStore.restoreAuth()
  initTheme()
})

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
