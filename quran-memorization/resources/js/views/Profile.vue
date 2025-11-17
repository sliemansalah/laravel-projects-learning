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
          <h1 class="text-xl font-bold text-emerald-800">الملف الشخصي</h1>
          <div class="w-20"></div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 py-8">
      <!-- User Info Card -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="text-center mb-6">
          <div class="w-24 h-24 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white text-4xl font-bold mx-auto mb-4">
            {{ userInitial }}
          </div>
          <h2 class="text-2xl font-bold text-gray-800 mb-1">
            {{ authStore.user?.name }}
          </h2>
          <p class="text-gray-600">{{ authStore.user?.email }}</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-3 gap-4">
          <div class="text-center p-4 bg-emerald-50 rounded-xl">
            <div class="text-3xl font-bold text-emerald-600">
              {{ authStore.user?.total_points || 0 }}
            </div>
            <div class="text-sm text-gray-600">النقاط</div>
          </div>
          <div class="text-center p-4 bg-blue-50 rounded-xl">
            <div class="text-3xl font-bold text-blue-600">
              {{ authStore.user?.current_streak || 0 }}
            </div>
            <div class="text-sm text-gray-600">أيام متتالية</div>
          </div>
          <div class="text-center p-4 bg-purple-50 rounded-xl">
            <div class="text-3xl font-bold text-purple-600">
              {{ authStore.user?.longest_streak || 0 }}
            </div>
            <div class="text-sm text-gray-600">أطول سلسلة</div>
          </div>
        </div>
      </div>

      <!-- Settings Card -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">الإعدادات</h3>

        <div class="space-y-6">
          <!-- Daily Goal -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              الهدف اليومي (عدد الكلمات)
            </label>
            <input
              v-model.number="settings.daily_goal"
              type="number"
              min="1"
              max="100"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
            />
          </div>

          <!-- Reminder Time -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              وقت التذكير
            </label>
            <input
              v-model="settings.reminder_time"
              type="time"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
            />
          </div>

          <!-- Notifications -->
          <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700">
              تفعيل الإشعارات
            </label>
            <button
              @click="settings.notifications_enabled = !settings.notifications_enabled"
              :class="settings.notifications_enabled ? 'bg-emerald-500' : 'bg-gray-300'"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            >
              <span
                :class="settings.notifications_enabled ? 'translate-x-6' : 'translate-x-1'"
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              />
            </button>
          </div>

          <!-- Dark Mode -->
          <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700">
              الوضع الليلي
            </label>
            <button
              @click="settings.dark_mode = !settings.dark_mode"
              :class="settings.dark_mode ? 'bg-emerald-500' : 'bg-gray-300'"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            >
              <span
                :class="settings.dark_mode ? 'translate-x-6' : 'translate-x-1'"
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              />
            </button>
          </div>

          <!-- Save Button -->
          <button
            @click="saveSettings"
            :disabled="saving"
            class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:shadow-lg transition-all font-bold disabled:opacity-50"
          >
            {{ saving ? 'جاري الحفظ...' : 'حفظ الإعدادات' }}
          </button>
        </div>
      </div>

      <!-- Logout Button -->
      <button
        @click="handleLogout"
        class="w-full mt-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium"
      >
        تسجيل الخروج
      </button>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const settings = ref({
  daily_goal: 5,
  reminder_time: '20:00',
  notifications_enabled: true,
  dark_mode: false
})

const saving = ref(false)

const userInitial = computed(() => {
  return authStore.user?.name?.charAt(0).toUpperCase() || 'U'
})

onMounted(() => {
  if (authStore.user) {
    settings.value = {
      daily_goal: authStore.user.daily_goal || 5,
      reminder_time: authStore.user.reminder_time || '20:00',
      notifications_enabled: authStore.user.notifications_enabled ?? true,
      dark_mode: authStore.user.dark_mode || false
    }
  }
})

const saveSettings = async () => {
  saving.value = true
  try {
    await api.patch('/user/settings', settings.value)
    alert('تم حفظ الإعدادات بنجاح')
  } catch (error) {
    console.error('Error saving settings:', error)
    alert('حدث خطأ أثناء حفظ الإعدادات')
  } finally {
    saving.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
