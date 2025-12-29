<template>
  <div class="chart-container">
    <Line v-if="chartData.labels.length" :data="chartData" :options="options" />
    <div v-else class="text-center text-gray-400 mt-10">
      No ratio data available
    </div>
  </div>
</template>

<script setup>
import { ref, watch, reactive } from "vue"
import { Line } from "vue-chartjs"
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from "chart.js"

// Register chart components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

// Props
const props = defineProps({
  keepPromiseRatio: {
    type: Object,
    default: () => ({ labels: [], datasets: [] }),
  },
})

// Reactive chart data
const chartData = reactive({
  labels: props.keepPromiseRatio?.labels || [],
  datasets: props.keepPromiseRatio?.datasets?.map(dataset => ({
    ...dataset,
    borderColor: '#6366f1',
    backgroundColor: 'transparent',
    borderWidth: 2,
    tension: 0.1,
    pointRadius: 0,
    pointHoverRadius: 4,
  })) || [],
})

// Watch props untuk update chart
watch(
  () => props.keepPromiseRatio,
  (newVal) => {
    chartData.labels = newVal?.labels || []
    chartData.datasets = newVal?.datasets?.map(dataset => ({
      ...dataset,
      borderColor: '#6366f1',
      backgroundColor: 'transparent',
      borderWidth: 2,
      tension: 0.1,
      pointRadius: 0,
      pointHoverRadius: 4,
    })) || []
  },
  { deep: true, immediate: true }
)

// Chart options
const options = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { 
      display: false
    },
    title: { 
      display: true, 
      text: "Rasio Kinerja",
      align: 'start',
      color: '#1f2937',
      font: {
        size: 20,
        weight: 'bold'
      },
      padding: {
        bottom: 30
      }
    },
  },
  scales: {
    x: { 
      grid: { 
        display: false
      },
      ticks: { 
        color: '#9ca3af',
        font: {
          size: 12
        }
      },
      border: {
        display: false
      }
    },
    y: { 
      beginAtZero: true,
      ticks: { 
        color: '#9ca3af',
        font: {
          size: 12
        },
        stepSize: 7
      },
      grid: { 
        color: '#e5e7eb',
        drawBorder: false
      },
      border: {
        display: false
      }
    },
  },
})
</script>

<style scoped>
.chart-container {
  background-color: #ffffff;
  border-radius: 0.5rem;
  padding: 2rem;
  width: 100%;
  height: 400px;
}
</style>