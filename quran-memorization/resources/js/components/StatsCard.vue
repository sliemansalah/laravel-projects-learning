<template>
  <div :class="cardClasses" class="rounded-xl p-6 shadow-lg">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium opacity-90 mb-1">{{ title }}</p>
        <p class="text-3xl font-bold">{{ value }}</p>
      </div>
      <div class="text-4xl">{{ icon }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  value: [Number, String],
  icon: String,
  color: {
    type: String,
    default: 'emerald'
  }
})

const cardClasses = computed(() => {
  const colorMap = {
    emerald: 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white',
    blue: 'bg-gradient-to-br from-blue-500 to-blue-600 text-white',
    amber: 'bg-gradient-to-br from-amber-500 to-amber-600 text-white',
    purple: 'bg-gradient-to-br from-purple-500 to-purple-600 text-white'
  }
  return colorMap[props.color] || colorMap.emerald
})
</script>
