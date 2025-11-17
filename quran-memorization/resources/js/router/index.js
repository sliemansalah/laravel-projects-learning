import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [{
        path: '/',
        name: 'Home',
        component: () =>
            import ('@/views/Home.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/login',
        name: 'Login',
        component: () =>
            import ('@/views/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: () =>
            import ('@/views/Register.vue'),
        meta: { guest: true }
    },
    {
        path: '/memorize/:surahId',
        name: 'Memorize',
        component: () =>
            import ('@/views/Memorize.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/memorize/:surahId/ayah/:ayahId',
        name: 'AyahMemorize',
        component: () =>
            import ('@/views/AyahMemorize.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/review',
        name: 'Review',
        component: () =>
            import ('@/views/Review.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/progress',
        name: 'Progress',
        component: () =>
            import ('@/views/Progress.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/profile',
        name: 'Profile',
        component: () =>
            import ('@/views/Profile.vue'),
        meta: { requiresAuth: true }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()
    const isAuthenticated = authStore.isAuthenticated

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login')
    } else if (to.meta.guest && isAuthenticated) {
        next('/')
    } else {
        next()
    }
})

export default router