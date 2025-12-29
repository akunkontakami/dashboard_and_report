<template>
  <div class="flex flex-nowrap justify-center items-start space-x-4 mt-6">
    <!-- === TABEL === -->
    <div class="w-full relative overflow-auto shadow-md rounded-lg">
      <span class="px-6 py-3 text-lg font-bold text-gray-700">Contract Performance</span><br>
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs uppercase bg-white text-gray-400">
          <tr>
            <th class="px-6 py-3 summary-title">##</th>
            <th class="px-6 py-3 summary-title">B 1-30</th>
            <th class="px-6 py-3 summary-title">C 31-60</th>
            <th class="px-6 py-3 summary-title">D 61-90</th>
            <th class="px-6 py-3 summary-title">Total</th>
          </tr>
        </thead>
        <tbody v-if="contractPromisePerOverdue && Object.keys(contractPromisePerOverdue).length">
          <tr class="text-dark">
            <th class="px-6 py-4">Contract Distributed</th>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['1-30']?.CD || 0 }}</td>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['31-60']?.CD || 0 }}</td>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['61-90']?.CD || 0 }}</td>
            <td class="px-6 py-4">{{ total('CD') }}</td>
          </tr>
          <tr class="text-dark">
            <th class="px-6 py-4">Promise to Pay</th>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['1-30']?.PTP || 0 }}</td>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['31-60']?.PTP || 0 }}</td>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['61-90']?.PTP || 0 }}</td>
            <td class="px-6 py-4">{{ total('PTP') }}</td>
          </tr>
          <tr class="text-dark">
            <th class="px-6 py-4">Keep Promise</th>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['1-30']?.KP || 0 }}</td>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['31-60']?.KP || 0 }}</td>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['61-90']?.KP || 0 }}</td>
            <td class="px-6 py-4">{{ total('KP') }}</td>
          </tr>
          <tr class="text-dark">
            <th class="px-6 py-4">Broken Promise</th>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['1-30']?.BP || 0 }}</td>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['31-60']?.BP || 0 }}</td>
            <td class="px-6 py-4">{{ contractPromisePerOverdue['61-90']?.BP || 0 }}</td>
            <td class="px-6 py-4">{{ total('BP') }}</td>
          </tr>
          <tr class="text-dark">
            <th class="px-6 py-4">Ratio Keep Promise</th>
            <td class="px-6 py-4">{{ ratio('1-30') }}%</td>
            <td class="px-6 py-4">{{ ratio('31-60') }}%</td>
            <td class="px-6 py-4">{{ ratio('61-90') }}%</td>
            <td class="px-6 py-4">{{ ratioTotal }}%</td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="5" class="text-center text-gray-400 py-10">
              Loading overdue data...
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- === PIE CHART === -->
    <!-- <div class="w-1/3 h-80">
      <Pie v-if="chartData" :data="chartData" :options="options" />
    </div> -->
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'

// === import store global ===
import { contractPromisePerOverdue } from '../stores/index.js'
import { generateColor } from '../utils/index.js'

// register chart component
ChartJS.register(ArcElement, Tooltip, Legend)

// ====== pastikan default value di sini jika belum ada ======
if (!contractPromisePerOverdue.value || Object.keys(contractPromisePerOverdue.value).length === 0) {
  contractPromisePerOverdue.value = {
    "1-30": { CD: 0, PTP: 0, KP: 0, BP: 0 },
    "31-60": { CD: 0, PTP: 0, KP: 0, BP: 0 },
    "61-90": { CD: 0, PTP: 0, KP: 0, BP: 0 }
  }
}

// debug
console.log('📦 OverduePerformance.vue dimuat')
onMounted(() => {
  console.log('🚀 OverduePerformance mounted')
  console.log('📊 contractPromisePerOverdue saat ini:', contractPromisePerOverdue.value)
})

// === fungsi total & ratio ===
function total(key) {
  const data = contractPromisePerOverdue.value
  if (!data?.['1-30'] || !data?.['31-60'] || !data?.['61-90']) return 0
  return (
    (data['1-30'][key] || 0) +
    (data['31-60'][key] || 0) +
    (data['61-90'][key] || 0)
  )
}

function ratio(key) {
  const d = contractPromisePerOverdue.value?.[key]
  if (!d || !d.PTP) return 0
  return ((d.KP / d.PTP) * 100).toFixed(2)
}

const ratioTotal = computed(() => {
  const data = contractPromisePerOverdue.value
  if (!data?.['1-30']) return 0
  const totalKP = total('KP')
  const totalPTP = total('PTP')
  return totalPTP > 0 ? ((totalKP / totalPTP) * 100).toFixed(2) : 0
})

// === Chart Data ===
const chartData = computed(() => {
  const d = contractPromisePerOverdue.value
  if (!d?.['1-30']) return null
  return {
    labels: ['Keep Promise', 'Broken Promise'],
    datasets: [
      {
        label: 'Promise Overview',
        data: [total('KP'), total('BP')],
        backgroundColor: [generateColor(), generateColor()],
        borderWidth: 1,
      },
    ],
  }
})

// === Chart Options ===
const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top' },
    tooltip: { enabled: true },
  },
}
</script>
