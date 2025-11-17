import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useQuranStore = defineStore('quran', () => {
    const surahs = ref([])
    const currentSurah = ref(null)
    const currentAyah = ref(null)
    const loading = ref(false)
    const error = ref(null)

    async function fetchSurahs() {
        loading.value = true
        error.value = null

        try {
            const response = await api.get('/quran/surahs')
            surahs.value = response.data.data
            return surahs.value
        } catch (err) {
            error.value = (err.response && err.response.data && err.response.data.message) || 'حدث خطأ في تحميل السور'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function fetchSurah(surahId) {
        loading.value = true
        error.value = null

        try {
            const response = await api.get(`/quran/surahs/${surahId}`)
            currentSurah.value = response.data.data
            return currentSurah.value
        } catch (err) {
            error.value = (err.response && err.response.data && err.response.data.message) || 'حدث خطأ في تحميل السورة'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function fetchAyah(ayahId) {
        loading.value = true
        error.value = null

        try {
            const response = await api.get(`/quran/ayahs/${ayahId}`)
            currentAyah.value = response.data.data
            return currentAyah.value
        } catch (err) {
            error.value = (err.response && err.response.data && err.response.data.message) || 'حدث خطأ في تحميل الآية'
            throw err
        } finally {
            loading.value = false
        }
    }

    function clearCurrent() {
        currentSurah.value = null
        currentAyah.value = null
    }

    return {
        surahs,
        currentSurah,
        currentAyah,
        loading,
        error,
        fetchSurahs,
        fetchSurah,
        fetchAyah,
        clearCurrent
    }
})