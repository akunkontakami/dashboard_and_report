<template>
  <div class="relative h-96 overflow-auto shadow-md rounded-lg p-4">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
      <thead class="text-xs uppercase bg-white text-gray-400">
        <tr>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">Desk Coll Name</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">Bucket Called</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">Agreement No</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">Customer Name</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">PTP Date</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">PTP Amount</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">PTP Payment Date</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">PTP Payment Amount</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">Result PTP</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">Bucket EOM</th>
          <th class="px-6 py-3 summary-title border border-gray-300 p-2">Remark</th>
        </tr>
      </thead>

      <tbody>
        <template v-if="hasData">
          <template v-for="(buckets, deskcollName) in detailContractPromiseValue" :key="deskcollName">
            <template v-for="(entries, bucketKey) in buckets" :key="bucketKey">
              <tr v-for="(entry, i) in entries || []" :key="`${deskcollName}-${bucketKey}-${i}`" class="bg-white text-gray-800">
                <!-- Gabung baris pertama untuk DeskColl dan Bucket -->
                <template v-if="i === 0">
                  <td class="px-6 py-4 align-top summary-title border border-gray-300 p-2" :rowspan="entries?.length || 1">{{ deskcollName || '-' }}</td>
                  <td class="px-6 py-4 align-top summary-title border border-gray-300 p-2" :rowspan="entries?.length || 1">{{ bucketKey || '-' }}</td>
                </template>

                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ entry?.AgreementNo || '-' }}</td>
                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ entry?.CustomerName || '-' }}</td>
                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ entry?.PTPDate || '-' }}</td>
                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ entry?.PTPAmount || '-' }}</td>
                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ entry?.PTPPaymentDate || '-' }}</td>
                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ entry?.PTPPaymentAmount || '-' }}</td>
                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ entry?.ResultPTP || '-' }}</td>
                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ bucketKey || '-' }}</td>
                <td class="px-6 py-4 summary-title border border-gray-300 p-2">{{ entry?.Remark || '-' }}</td>
              </tr>
            </template>
          </template>
        </template>

        <tr v-else>
          <td class="px-6 py-4 text-center" colspan="11">No data available</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { computed, watchEffect } from 'vue'
import { detailContractPromise } from '../stores'

// Ambil .value supaya bisa digunakan di template
const detailContractPromiseValue = computed(() => detailContractPromise.value || {})

// Cek ada data atau tidak
const hasData = computed(() => Object.keys(detailContractPromiseValue.value).length > 0)

// 🔍 Debug: log data ke console setiap kali berubah
watchEffect(() => {
  console.log("✅ detailContractPromiseValue cek:", detailContractPromiseValue.value)
  console.log("✅ hasData:", hasData.value)
})
</script>
