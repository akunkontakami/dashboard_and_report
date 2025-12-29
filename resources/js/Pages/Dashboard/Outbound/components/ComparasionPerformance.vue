<template>
  <div class="p-4 border border-gray-700 bg-white rounded shadow h-96">
    <h2 class="text-lg font-bold mb-2 text-gray-800">
      Comparasion Performance
    </h2>

    <Bar v-if="chartData" :data="chartData" :options="options" />
    <p v-else class="text-gray-400 text-center mt-10">
      No data available
    </p>
  </div>
</template>

<script setup>
import { computed, watchEffect } from "vue"
import { Bar } from "vue-chartjs"
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from "chart.js"

// Daftarkan elemen Chart.js agar tidak error “element not registered”
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

// === Props ===
const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
})

console.log("🧩 [ComparasionPerformance] Komponen dimuat")

watchEffect(() => {
  console.log("👀 [Watcher] props.data berubah:", props.data)
})

// === Chart data ===
const chartData = computed(() => {
  console.log("⚙️ [Computed] dijalankan. Props saat ini:", props.data)

  if (!props.data?.labels?.length) {
    console.warn("⚠️ [Computed] props.data.labels kosong")
    return null
  }

  const prepared = {
    labels: props.data.labels,
    datasets: props.data.datasets.map((ds) => ({
      ...ds,
      backgroundColor:
        ds.backgroundColor ||
        ["#3b82f6", "#22c55e", "#ef4444", "#facc15", "#a855f7"],
    })),
  }

  console.log("✅ [Computed] Data chart valid, siap render:")
  console.log("Labels", prepared.labels)
  console.log("Datasets", prepared.datasets)
  return prepared
})

// === Chart Options ===
const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: "top" },
    title: { display: true, text: "Ratio Keep Promise Chart" },
  },
  scales: {
    y: { beginAtZero: true, ticks: { precision: 0 } },
  },
}
</script>
