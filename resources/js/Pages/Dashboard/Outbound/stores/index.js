import { reactive, ref } from 'vue'
import { generateColor } from '../utils'

// === other ===
export const session = ref(null)
export const loading = ref(false)
export const alert = reactive({
  status: false,
  text: '',
  show: false
})

// === filter ===
export const month = ref(new Date().getMonth() + 1)
export const year = ref(new Date().getFullYear())
// export const branch = ref('')

// === data ===
export const keepPromisePerformance = reactive({
  month: [],
  kp: [],
  bp: [],
  rkp: []
})

export const contractPromisePerDescoll = ref([])

export const summaryContractPromisePerDescoll = reactive({
  totalContract: 0,
  totalPTP: 0,
  totalKP: 0,
  totalBP: 0,
  totalRKP: 0
})

export const keepPromiseRatio = reactive({
  labels: [],
  datasets: [
    {
      label: 'Keep Promise Ratio Performance (%)',
      data: [],
      fill: true,
      backgroundColor: generateColor(),
      borderColor: generateColor(),
      tension: 0.4,
    },
  ],
})

export const comparasionPerform = reactive({
  labels: [],
  datasets: [
    {
      label: 'Ratio',
      data: [],
      fill: true,
      backgroundColor: generateColor(),
      borderColor: generateColor(),
      tension: 0.4,
    },
  ],
})

export const descollLeadPerform = ref([])
export const contractPromisePerOverdue = ref({})
export const detailContractPromise = ref({})
