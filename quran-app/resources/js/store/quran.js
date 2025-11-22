// resources/js/stores/quran.js

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useQuranStore = defineStore('quran', () => {
    const surahs = ref([])
    const currentSurah = ref(null)
    const ayahs = ref([])
    const loading = ref(false)
    const error = ref(null)

    const totalSurahs = computed(() => surahs.value.length)

    const fetchSurahs = async() => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get('/quran/surahs')
            surahs.value = response.data.data
            return response.data.data
        } catch (err) {
            error.value = err.response ? .data ? .message || 'خطأ في جلب السور'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchSurah = async(surahId) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get(`/quran/surah/${surahId}`)
            currentSurah.value = response.data.data
            ayahs.value = response.data.data.ayahs || []
            return response.data.data
        } catch (err) {
            error.value = err.response ? .data ? .message || 'خطأ في جلب السورة'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchSurahByNumber = async(number) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get(`/quran/surah/number/${number}`)
            currentSurah.value = response.data.data
            ayahs.value = response.data.data.ayahs || []
            return response.data.data
        } catch (err) {
            error.value = err.response ? .data ? .message || 'خطأ في جلب السورة'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchAyahs = async(surahId) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get(`/quran/surah/${surahId}/ayahs`)
            ayahs.value = response.data.data
            return response.data.data
        } catch (err) {
            error.value = err.response ? .data ? .message || 'خطأ في جلب الآيات'
            throw err
        } finally {
            loading.value = false
        }
    }

    const searchSurahs = async(query) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get(`/quran/surahs/search?q=${query}`)
            return response.data.data
        } catch (err) {
            error.value = err.response ? .data ? .message || 'خطأ في البحث'
            throw err
        } finally {
            loading.value = false
        }
    }

    const searchAyahs = async(query) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get(`/quran/ayahs/search?q=${query}`)
            return response.data.data
        } catch (err) {
            error.value = err.response ? .data ? .message || 'خطأ في البحث'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchPageAyahs = async(pageNumber) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get(`/quran/page/${pageNumber}`)
            ayahs.value = response.data.data
            return response.data.data
        } catch (err) {
            error.value = err.response ? .data ? .message || 'خطأ في جلب الصفحة'
            throw err
        } finally {
            loading.value = false
        }
    }

    return {
        surahs,
        currentSurah,
        ayahs,
        loading,
        error,
        totalSurahs,
        fetchSurahs,
        fetchSurah,
        fetchSurahByNumber,
        fetchAyahs,
        searchSurahs,
        searchAyahs,
        fetchPageAyahs
    }
})