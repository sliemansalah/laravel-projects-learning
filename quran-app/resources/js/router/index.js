// resources/js/router/index.js

import { useAuthStore } from '../store/auth'
import Login from '../Login.vue'
import Register from '../Register.vue'
import Dashboard from '../Dashboard.vue'
import Surahs from '../Surahs.vue'
import SurahDetail from '../SurahDetail.vue'
import Hifz from '../Hifz.vue'
import Statistics from '../Statistics.vue'
import DragDropTest from '../DragDropTest.vue'

const routes = [{
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { requiresGuest: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: { requiresGuest: true }
    },
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/surahs',
        name: 'Surahs',
        component: Surahs,
        meta: { requiresAuth: true }
    },
    {
        path: '/surah/:id',
        name: 'SurahDetail',
        component: SurahDetail,
        meta: { requiresAuth: true }
    },
    {
        path: '/hifz',
        name: 'Hifz',
        component: Hifz,
        meta: { requiresAuth: true }
    },
    {
        path: '/statistics',
        name: 'Statistics',
        component: Statistics,
        meta: { requiresAuth: true }
    },
    {
        path: '/drag-drop-test',
        name: 'DragDropTest',
        component: DragDropTest,
        meta: { requiresAuth: true }
    }
]

// Navigation Guards
routes.forEach(route => {
    if (route.meta ?.requiresAuth) {
        route.beforeEnter = (to, from, next) => {
            const authStore = useAuthStore()
            if (!authStore.isAuthenticated) {
                next('/login')
            } else {
                next()
            }
        }
    }
    if (route.meta ?.requiresGuest) {
        route.beforeEnter = (to, from, next) => {
            const authStore = useAuthStore()
            if (authStore.isAuthenticated) {
                next('/')
            } else {
                next()
            }
        }
    }
})

export default routes