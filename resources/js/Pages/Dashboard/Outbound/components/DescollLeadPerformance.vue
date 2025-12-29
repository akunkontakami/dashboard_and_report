<template>
  <div class="p-4 border border-gray-700 rounded bg-white shadow-md">
    <h2 class="text-lg font-bold mb-3 text-gray-800">
      Contact Detail
    </h2>

    <!-- Jika data ada -->
    <div v-if="listContractPromisePerDescoll.length"  class="relative h-96 overflow-auto shadow-md rounded-lg">
      <table class="w-full text-sm text-left text-dark border-collapse border border-gray-300">
        <thead class="bg-gray-100 sticky top-0 bg-white z-10">
          <tr class="text-left">
            <th class="border border-gray-300 p-2">Desk Coll Name</th>
            <th class="border border-gray-300 p-2">Contract Distribution</th>
            <th class="border border-gray-300 p-2">PTP</th>
            <th class="border border-gray-300 p-2">KP</th>
            <th class="border border-gray-300 p-2">BP</th>
            <th class="border border-gray-300 p-2">KP/PTP</th>
            <th class="border border-gray-300 p-2">Call Count</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(item, i) in listContractPromisePerDescoll"
            :key="i"
            class="hover:bg-gray-50"
          >
            <td class="border border-gray-300 p-2">{{ item.DescollName }}</td>
            <td class="border border-gray-300 p-2 text-center">{{ item.totalcontract }}</td>
            <td class="border border-gray-300 p-2 text-center">{{ item.totalPTP }}</td>
            <td class="border border-gray-300 p-2 text-center">{{ item.totalPTPKP }}</td>
            <td class="border border-gray-300 p-2 text-center">{{ item.totalPTPBP }}</td>
            <td class="border border-gray-300 p-2 text-center font-semibold">
              {{ calcRatio(item.totalPTPKP, item.totalPTP) }}
            </td>
            <td class="border border-gray-300 p-2 text-center">{{ item.callCount }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Jika data kosong -->
    <p v-else class="text-gray-400 text-center mt-6">Tidak ada data.</p>
  </div>
</template>

<script setup>
const props = defineProps({
  listContractPromisePerDescoll: {
    type: Array,
    default: () => [],
  },
})

// fungsi bantu untuk menghitung ratio (KP/PTP)
function calcRatio(kp, ptp) {
  if (!ptp || ptp === 0) return "-"
  const ratio = (kp / ptp) * 100
  return ratio.toFixed(2) + "%"
}
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
</style>
