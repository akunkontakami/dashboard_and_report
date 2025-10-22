<template>
    <li v-for="card in cardLiveDaily" v-if="!loading">
        <div class="bg-white rounded-xl border">
            <h1
                class="border-b text-center py-[7px] text-[12px] font-krub-bold"
            >
                {{ card.label }}
            </h1>
            <h1 class="border-b text-center py-2 text-3xl font-krub-bold">
                {{ card.today }}
            </h1>
            <div class="flex flex-col justify-center items-center py-2">
                <strong
                    class="text-[#F94144] text-[13px] flex items-center justify-center gap-1"
                    v-if="card.yesterday < 0"
                >
                    <i class="isax-b icon-arrow-down"></i>
                    {{ card.yesterday }}
                </strong>
            </div>
        </div>
    </li>
    <li v-for="n in 6" v-if="loading">
        <div class="bg-white rounded-xl border">
            <div
                class="border-b text-center py-[7px] text-[12px] font-krub-bold"
            >
                <span class="animate-pulse h-3 bg-slate-200 rounded inline-block w-[40%]"></span>
            </div>
            <div class="border-b text-center py-2 font-krub-bold">
                <span class="animate-pulse h-6 bg-slate-200 rounded inline-block w-[40%]"></span>
            </div>
            <div class="flex flex-col justify-center items-center py-2">
                <span class="animate-pulse h-3 bg-slate-200 rounded inline-block w-[40%]"></span>
                <span class="animate-pulse h-3 bg-slate-200 rounded inline-block w-[20%] mt-2"></span>
            </div>
        </div>
    </li>
</template>
<script setup lang="ts">
import axios from "axios";
import { ref, onMounted } from "vue";

const loading = ref(true)
const cardLiveDaily: any = ref([]);
const fetchLiveDailyCard2 = () => {
    loading.value = true
    axios
        .get(route("dashboard.outbound.data.live-daily.card2"))
        .then((result) => {
            cardLiveDaily.value = result.data;
            loading.value = false
        });
};

onMounted(() => {
    fetchLiveDailyCard2();
});
</script>
