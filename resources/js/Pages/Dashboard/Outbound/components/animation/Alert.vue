<script setup>
import { computed } from 'vue'

// Props
defineProps({
  status: {
    type: Boolean,
    required: true
  },
  text: {
    type: [String, Number],
    required: true
  }
})

// Warna tergantung status
const alertClass = computed(() =>
  props.status
    ? 'bg-green-200 border-green-600 text-green-600'
    : 'bg-red-200 border-red-600 text-red-600'
)
</script>

<template>
  <div
    class="fixed z-40 w-full top-0 border-l-4 p-4"
    :class="alertClass"
    role="alert"
  >
    <p class="font-bold">{{ status ? 'Success' : 'Failed' }}</p>
    <p>{{ text }}</p>
  </div>
</template>
