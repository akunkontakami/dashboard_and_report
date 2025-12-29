<template>
  <div class="relative h-96 overflow-auto shadow-md rounded-lg mb-8">
    <span class="px-6 py-3 text-lg font-bold text-gray-700">Desk Coll Performance</span><br>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
      <thead class="text-xs uppercase bg-white text-gray-400">
        <tr>
          <th scope="col" class="px-6 py-3 summary-title">Desk Coll Name</th>
          <th scope="col" class="px-6 py-3 summary-title">Contract Called</th>
          <th scope="col" class="px-6 py-3 summary-title">Call Count</th>
          <th scope="col" class="px-6 py-3 summary-title">PTP</th>
          <th scope="col" class="px-6 py-3 summary-title">KP</th>
          <th scope="col" class="px-6 py-3 summary-title">BP</th>
          <th scope="col" class="px-6 py-3 summary-title">PTP/Call</th>
          <th scope="col" class="px-6 py-3 summary-title">KP/PTP</th>
          <th scope="col" class="px-6 py-3 summary-title">BP/PTP</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(item, i) in data"
          :key="i"
          class="bg-white text-dark hover:bg-orange-200 transition"
        >
          <td class="px-6 py-4 summary-title">{{ item.DescollName }}</td>
          <td class="px-6 py-4 summary-title font-medium whitespace-nowrap">{{ formatNumber(item.totalcontract) }}</td>
          <td class="px-6 py-4 summary-title font-medium whitespace-nowrap">{{ formatNumber(item.callCount) }}</td>
          <td class="px-6 py-4 summary-title">{{ formatNumber(item.totalPTP) }}</td>
          <td class="px-6 py-4 summary-title">{{ formatNumber(item.totalPTPKP) }}</td>
          <td class="px-6 py-4 summary-title">{{ formatNumber(item.totalPTPBP) }}</td>
          <td class="px-6 py-4 summary-title">{{ calcPercent(item.totalPTP, item.callCount) }}%</td>
          <td class="px-6 py-4 summary-title">{{ calcPercent(item.totalPTPKP, item.totalPTP) }}%</td>
          <td class="px-6 py-4 summary-title">{{ calcPercent(item.totalPTPBP, item.totalPTP) }}%</td>
        </tr>
      </tbody>
    </table>

    <div v-if="!data.length" class="text-center py-6 text-gray-400">
      No data available
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  data: {
    type: Array,
    required: true,
    default: () => [],
  },
})

// Format angka agar lebih rapi
const formatNumber = (num) => {
  if (num === null || num === undefined) return "-"
  return num.toLocaleString("en-US")
}

// Hitung rasio dalam bentuk persentase
const calcPercent = (num, denom) => {
  if (!denom) return 0
  return ((num / denom) * 100).toFixed(2)
}
</script>
