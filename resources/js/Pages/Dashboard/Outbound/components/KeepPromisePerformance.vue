<template>
  <div class="p-4">
    <div class="w-full h-96 p-5 border border-gray-200 bg-white rounded-lg shadow">
      <!-- Cegah render sebelum data siap -->
      <Bar v-if="chartData && chartData.labels.length" :data="chartData" :options="options" />
      <div v-else class="text-center text-gray-400 mt-10">
        Loading chart data...
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue"
import { Bar } from "vue-chartjs"
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js"

// ✅ Registrasi Chart.js modules supaya gak error "element not registered"
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
)

// Props dari parent
const props = defineProps({
  listKeepPromisePerform: {
    type: Object,
    default: () => ({
      month: [],
      kp: [],
      bp: [],
      rkp: [],
    }),
  },
})

// computed chartData — aman walau data belum siap
const chartData = computed(() => {
  const data = props.listKeepPromisePerform || {}

  console.log("📊 [KeepPromisePerformance] Data masuk:", data)

  return {
    labels: data.month || [],
    datasets: [
      {
        label: "Keep Promise",
        data: data.kp || [],
        backgroundColor: "#4F46E5",
        borderRadius: 8,
        barThickness: 24,
      },
      {
        label: "Broken Promise",
        data: data.bp || [],
        backgroundColor: "#FB923C",
        borderRadius: 8,
        barThickness: 24,
      },
    ],
  }
})

// Chart.js options
const options = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: "index",
    intersect: false,
  },
  plugins: {
    legend: {
      position: "bottom",
      labels: { 
        color: "#374151",
        padding: 20,
        usePointStyle: true,
        pointStyle: "rect",
      },
    },
    title: {
      display: true,
      text: "Keep & Brokend Promise Performance",
      color: "#1f2937",
      align: "start",
      font: {
        size: 18,
        weight: "600",
      },
      padding: {
        bottom: 30,
      },
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: "#f3f4f6",
      },
      ticks: {
        color: "#6b7280",
      },
    },
    x: {
      grid: {
        display: false,
      },
      ticks: { 
        color: "#6b7280",
      },
    },
  },
}
</script>