<template>
  <div class="bg-white p-6 w-full">
    <form class="flex flex-col gap-4">

      <!-- BAR ATAS -->
      <div class="grid grid-cols-1 md:grid-cols-2 items-center w-full">
        <div></div>

        <!-- KANAN -->
        <div class="flex justify-end gap-2 items-center">
          <!-- Tahun -->
          <span class="text-sm font-medium text-dark">Tahun</span>
          <select v-model="year" class="border px-2 py-1 rounded" style="width: 100px;">
            <option value="">Year</option>
            <option v-for="y in listYear" :key="y" :value="y">
              {{ y }}
            </option>
          </select>

          <!-- Profile -->
          <span class="text-sm font-medium text-dark">Filter</span>
          <select
            v-model="profile"
            class="border px-2 py-1 rounded"
            @change="onProfileChange"
          >
            <option value="">Filter</option>
            <option value="4">HO</option>
            <option value="10">OCC</option>
          </select>
        </div>
      </div>

      <!-- BAR BAWAH -->
      <div class="flex flex-wrap gap-2 justify-start w-full">
        <button
          v-for="(item, i) in listMonth"
          :key="i"
          type="button"
          class="px-4 py-2 rounded font-medium text-sm transition-colors"
          :class="month === i + 1
            ? 'bg-blue-600 text-white'
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
          @click="selectMonth(i + 1)"
        >
          {{ item }}
        </button>
      </div>

    </form>
  </div>
</template>


<script setup>
import { ref, computed } from "vue"

const emit = defineEmits(["submit"])

const year = ref("")
const month = ref(null)
const profile = ref("")

const listYear = [2025, 2024, 2023]
const listMonth = [
  "Januari","Februari","Maret","April","Mei","Juni",
  "Juli","Agustus","September","Oktober","November","Desember"
]

// pilih bulan
const selectMonth = (val) => {
  month.value = val
}

// validasi
const isValid = computed(() => {
  return year.value && month.value && profile.value
})

// 🔥 SUBMIT SAAT HO / OCC DIPILIH
function onProfileChange() {
  if (!isValid.value) {
    alert("Pilih Year dan Month dulu sebelum Filter")
    return
  }

  emit("submit", {
    year: year.value,
    month: month.value,
    profile: profile.value
  })
}
</script>

<style scoped>
/* Baris filter di kanan */
.filter-bar {
  display: flex;
  justify-content: right; /* 👈 ini yang kamu maksud */
  align-items: center;
  gap: 1.5rem; /* jarak antar elemen */
}
</style>
