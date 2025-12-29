<template>
  <div class="w-full max-w-md h-96 overflow-y-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">DESK COLL</h2>

    <!-- Input pencarian -->
    <input
      type="text"
      placeholder="Search..."
      v-model="searchText"
      class="w-full px-4 py-2 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500"
    />

    <!-- List checkbox -->
    <div class="flex flex-col space-y-2">
      <label
        v-for="item in filteredItems"
        :key="item.id"
        class="flex items-center space-x-2"
      >
        <input
          type="checkbox"
          class="form-checkbox h-5 w-5 text-indigo-600"
          :checked="checkedItems.includes(item.id)"
          @change="handleCheckboxChange(item.id)"
        />
        <span>{{ item.DescollName }}</span>
      </label>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"

// Props dari parent
const props = defineProps({
  listData: {
    type: Array,
    default: () => [],
  },
})

// Emit ke parent
const emit = defineEmits(["update:comparasionPerform", "update:descollLeadPerform"])

// Local state
const searchText = ref("")
const checkedItems = ref([])

// Filter by search
const filteredItems = computed(() => {
  if (!props.listData.length) return []
  return props.listData.filter((item) =>
    item.DescollName?.toLowerCase().includes(searchText.value.toLowerCase())
  )
})

// Fungsi menghitung ratio berdasarkan listData
function calculateRatio(dataList) {
  const labels = []
  const ratios = []

  for (const item of dataList) {
    const bp = parseInt(item.totalPTPBP) || 0
    const kp = parseInt(item.totalPTPKP) || 0
    const ratio = bp > 0 ? (kp / bp).toFixed(2) : 0
    labels.push(item.DescollName)
    ratios.push(Number(ratio))
  }

  return {
    labels,
    datasets: [
      {
        label: "Ratio Keep Promise",
        data: ratios,
        backgroundColor: ["#3b82f6", "#22c55e", "#ef4444"],
      },
    ],
  }
}

// Saat checkbox diubah
function handleCheckboxChange(id) {
  checkedItems.value = checkedItems.value.includes(id)
    ? checkedItems.value.filter((i) => i !== id)
    : [...checkedItems.value, id]

  const selected = props.listData.filter((val) =>
    checkedItems.value.includes(val.id)
  )

  const chartData = calculateRatio(selected.length > 0 ? selected : props.listData)

  emit("update:comparasionPerform", chartData)
  emit("update:descollLeadPerform", selected.length > 0 ? selected : props.listData)
}

// ✅ Saat pertama kali load, langsung kirim data chart
onMounted(() => {
  const chartData = calculateRatio(props.listData)
  emit("update:comparasionPerform", chartData)
  emit("update:descollLeadPerform", props.listData)
})
</script>
