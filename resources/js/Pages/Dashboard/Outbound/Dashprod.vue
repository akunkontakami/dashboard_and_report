<script setup>
import { ref, reactive, onMounted, watch } from "vue"
import './style.css'
// ✅ Import store reactive variable
import {
  contractPromisePerOverdue,
  keepPromisePerformance,
  contractPromisePerDescoll,
  summaryContractPromisePerDescoll,
  keepPromiseRatio,
  comparasionPerform,
  descollLeadPerform,
  detailContractPromise,
} from "./stores/index.js"

// ✅ Import komponen
import Alert from "./components/animation/Alert.vue"
import Loading from "./components/animation/Loading.vue"
import Navbar from "./components/Navbar.vue"
import KeepPromisePerformance from "./components/KeepPromisePerformance.vue"
import KeepPromiseRatio from "./components/KeepPromiseRatio.vue"
import SummaryContractPromisePerDescoll from "./components/SummaryContractPromisePerDescoll.vue"
import ContractPromisePerDescoll from "./components/ContractPromisePerDescoll.vue"
import ListAgent from "./components/ListAgent.vue"
import ComparasionPerformance from "./components/ComparasionPerformance.vue"
import DescollLeadPerformance from "./components/DescollLeadPerformance.vue"
import DetailContractPromise from "./components/DetailContractPromise.vue"
import ContractPromisePerOverdue from "./components/ContractPromisePerOverdue.vue"

// === STATE lokal ===
const loadingLocal = ref(false)
const notif = reactive({
  show: false,
  status: "",
  text: "",
})

// === LOAD DATA ===
async function loadDatalama(year, month, profile) {
  // ⛔ Stop kalau filter belum lengkap
  if (!year || !month || !profile) {
    console.warn("⛔ Filter belum lengkap → loadData dibatalkan", {
      year, month, profile
    })
    return
  }

  const url = `/data/CONTRACT_CALL_AND_KEEP_PROMISE_RATIO_PER_DESCOLL_${year}_${month}_${profile}.json`

  console.group("📥 LOAD DATA JSON")
  console.log("➡️ Fetch URL:", url)

  // 🔄 Reset data lama (penting!)
  contractPromisePerDescoll.value = []
  keepPromiseRatio.labels = []
  keepPromiseRatio.datasets[0].data = []
  comparasionPerform.labels = []
  comparasionPerform.datasets[0].data = []

  try {
    loadingLocal.value = true

    const res = await fetch(url)
    if (!res.ok) throw new Error(`HTTP ${res.status}`)

    const json = await res.json()
    console.log("📦 Response JSON:", json)

    // ===== Contract Promise per Descoll =====
    contractPromisePerDescoll.value = json.results ?? []

    // ===== Summary =====
    summaryContractPromisePerDescoll.totalContract = json.totalContractCalled ?? 0
    summaryContractPromisePerDescoll.totalPTP = json.totalPTP ?? 0
    summaryContractPromisePerDescoll.totalKP = json.totalKP ?? 0
    summaryContractPromisePerDescoll.totalBP = json.totalBP ?? 0
    summaryContractPromisePerDescoll.totalRKP = json.totalRKP ?? 0

    // ===== Keep Promise Performance =====
    keepPromisePerformance.month = [`${month}/${year}`]
    keepPromisePerformance.kp = [json.totalKP ?? 0]
    keepPromisePerformance.bp = [json.totalBP ?? 0]
    keepPromisePerformance.rkp = [json.totalRKP ?? 0]

    // ===== Ratio Chart =====
    keepPromiseRatio.labels = json.agent ?? []
    keepPromiseRatio.datasets[0].data = json.ratio ?? []

    // ===== Comparison Chart =====
    comparasionPerform.labels = json.agent ?? []
    comparasionPerform.datasets[0].data = json.ratio ?? []

    console.log("✅ Data berhasil dimuat")

  } catch (err) {
    console.error("❌ Gagal load data:", err)

    notif.show = true
    notif.status = "error"
    notif.text = "Gagal memuat data"
  } finally {
    loadingLocal.value = false
    console.groupEnd()
  }
}
async function loadData(year, month, profile) {
  if (!year || !month || !profile) return

  const urlSummary = `/data/CONTRACT_CALL_AND_KEEP_PROMISE_RATIO_PER_DESCOLL_${year}_${month}_${profile}.json`
  const urlDetail  = `/data/CONTRACT_CALL_AND_KEEP_PROMISE_RATIO_TOTAL_${year}_${month}_${profile}.json`
  const urlPrms  = `/data/CONTRACT_PROMISE_PEROVERDUE_${year}_${month}_${profile}.json`

  console.group("📥 LOAD DATA")

  try {
    loadingLocal.value = true

    // ================= SUMMARY =================
    const resSummary = await fetch(urlSummary)
    if (!resSummary.ok) throw new Error("Summary JSON error")
    const jsonSummary = await resSummary.json()

    contractPromisePerDescoll.value = jsonSummary.results ?? []

    summaryContractPromisePerDescoll.totalContract = jsonSummary.totalContractCalled ?? 0
    summaryContractPromisePerDescoll.totalPTP = jsonSummary.totalPTP ?? 0
    summaryContractPromisePerDescoll.totalKP = jsonSummary.totalKP ?? 0
    summaryContractPromisePerDescoll.totalBP = jsonSummary.totalBP ?? 0
    summaryContractPromisePerDescoll.totalRKP = jsonSummary.totalRKP ?? 0

    keepPromisePerformance.month = [`${month}/${year}`]
    keepPromisePerformance.kp = [jsonSummary.totalKP ?? 0]
    keepPromisePerformance.bp = [jsonSummary.totalBP ?? 0]
    keepPromisePerformance.rkp = [jsonSummary.totalRKP ?? 0]

    keepPromiseRatio.labels = jsonSummary.agent ?? []
    keepPromiseRatio.datasets[0].data = jsonSummary.ratio ?? []

    comparasionPerform.labels = jsonSummary.agent ?? []
    comparasionPerform.datasets[0].data = jsonSummary.ratio ?? []

    // ================= DETAIL =================
    const resDetail = await fetch(urlDetail)
    if (!resDetail.ok) throw new Error("Detail JSON error")
    const jsonDetail = await resDetail.json()

    // 🔥 INI YANG SEBELUMNYA HILANG
    detailContractPromise.value = jsonDetail.results ?? {}

    console.log("✅ Detail loaded:", detailContractPromise.value)
    
    // ================= PROMISE OVERDUE =================
    const resPrms = await fetch(urlPrms)
    if (!resPrms.ok) throw new Error("Promise Overdue JSON error")
    const jsonPrms = await resPrms.json()
    // 🔥 INI YANG SEBELUMNYA HILANG
    contractPromisePerOverdue.value = jsonPrms.results ?? {}
    console.log("✅ Promise Overdue loaded:", contractPromisePerOverdue.value)

  } catch (err) {
    console.error("❌ Load data gagal:", err)
    notif.show = true
    notif.status = "error"
    notif.text = "Gagal memuat data"
  } finally {
    loadingLocal.value = false
    console.groupEnd()
  }
}





onMounted(() => {
  console.log("🚀 App mounted — menunggu filter dipilih")
})
// === EVENT DARI NAVBAR ===
async function handleFilterSubmit({ year, month, profile }) {
  console.group("📌 FILTER SUBMIT DARI NAVBAR")
  console.log("Year:", year)
  console.log("Month:", month)
  console.log("Profile:", profile)
  console.groupEnd()

  await loadData(year, month, profile)
}



// === Watch debug (boleh dihapus kalau sudah stabil) ===
watch(contractPromisePerOverdue, val => {
  console.log("🔄 contractPromisePerOverdue:", val)
}, { deep: true })

watch(detailContractPromise, val => {
  console.log("🔍 detailContractPromise:", val)
}, { deep: true })

// === Event dari ListAgent ===
const updateComparasionPerform = (data) => {
  comparasionPerform.labels = data.labels
  comparasionPerform.datasets = data.datasets
}

const updateDescollLeadPerform = (data) => {
  descollLeadPerform.value = data || []
}
</script>

<template>
  <div>
    <!-- Alert & Loading -->
    <Alert v-if="notif.show" :status="notif.status" :text="notif.text" />
    <Loading v-if="loadingLocal" />

    <main>
      <!-- Navbar (FILTER) -->
      <Navbar @submit="handleFilterSubmit" />

      <div class="summary-wrapper p-4">
          <SummaryContractPromisePerDescoll
            :summary="summaryContractPromisePerDescoll"
          />
        </div>
        
       <!-- Chart Utama (Split KIRI & KANAN) -->
        <div class="flex gap-6 p-4">          
            <div class="chart-wrapper">
              <KeepPromisePerformance
                      :listKeepPromisePerform="keepPromisePerformance"
                    />
              
            </div>
            <div class="chart-wrapper">
             <ContractPromisePerOverdue
                v-if="
                  contractPromisePerOverdue?.['1-30'] ||
                  contractPromisePerOverdue?.['31-60'] ||
                  contractPromisePerOverdue?.['61-90']
                "
              />
             
            </div>
        </div>
      
        <!-- Chart Utama (Split KIRI & KANAN) -->
        <div class="flex gap-6 p-4">          
            <div class="chart-wrapper">
              <ContractPromisePerDescoll :data="contractPromisePerDescoll" />=
                           
            </div>
            <div class="chart-wrapper">
               <KeepPromiseRatio
                    :keepPromiseRatio="keepPromiseRatio"
                  />
            </div>
        </div>

     
      <!-- List Agent + Chart -->
      <div class="w-full flex flex-wrap md:flex-nowrap mt-6 space-x-4 p-2">
        <div class="w-2/6">
          <ListAgent
            :listData="contractPromisePerDescoll"
            :keepPromiseRatio="keepPromiseRatio"
            @update:comparasionPerform="updateComparasionPerform"
            @update:descollLeadPerform="updateDescollLeadPerform"
          />
        </div>

        <div class="descoll-main">
          <DescollLeadPerformance
            :listContractPromisePerDescoll="
              descollLeadPerform?.length
                ? descollLeadPerform
                : contractPromisePerDescoll
            "
          />
        </div>

      </div>

      <!-- Detail -->
      <DetailContractPromise />
      <!-- <ComparasionPerformance :data="comparasionPerform" /> --> 
    </main>
  </div>
</template>

<!-- <style scoped>
main {
  background-color: #e9f0eb;
  min-height: 100vh;
  color: #0b3c6e;
}
</style> -->
