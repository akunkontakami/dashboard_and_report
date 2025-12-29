<template>
  <div class="summary-wrapper">

    <div class="summary-card">
      <div class="summary-title">Contract Called</div>
      <div class="summary-value">
        {{ formatNumber(summary.totalContract) }}
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-title">Promise To Pay</div>
      <div class="summary-value">
        {{ formatNumber(summary.totalPTP) }}
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-title">Keep Promise</div>
      <div class="summary-value">
        {{ formatNumber(summary.totalKP) }}
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-title">Broken Promise</div>
      <div class="summary-value">
        {{ formatNumber(summary.totalBP) }}
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-title">Ratio Success Keep Promise</div>
      <div class="summary-value">
        {{ ratio }}
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-title">Ratio Broken Promise</div>
      <div class="summary-value">
        {{ ratioBroken }}
      </div>
    </div>

  </div>
</template>


<script setup>
import { computed } from "vue"

// ✅ Terima data dari parent
const props = defineProps({
  summary: {
    type: Object,
    required: true,
    default: () => ({
      totalContract: 0,
      totalPTP: 0,
      totalKP: 0,
      totalBP: 0,
    }),
  },
})

// ✅ Hitung ratio success (hindari pembagian 0)
const ratio = computed(() => {
  const { totalKP, totalBP } = props.summary
  if (!totalBP) return "0.00"
  return (totalKP / totalBP).toFixed(2)
})

// ✅ Format angka biar rapi
function formatNumber(num) {
  if (num === null || num === undefined) return "-"
  return num.toLocaleString("en-US")
}
</script>
