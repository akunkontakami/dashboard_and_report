<template>
    <li v-for="row in data" v-if="!loading">
        <div
            class="bg-white border rounded-xl flex flex-col justify-center items-center py-2 px-4"
        >
            <h1 class="text-3xl font-krub-bold" :style="`color: ${row.color};`">
                {{ row.total }}
            </h1>
            <p class="text-[12px] font-krub-semibold">
                {{ row.name }}
            </p>
        </div>
    </li>

    <li v-for="n in 4" v-if="loading">
        <div class="bg-white rounded-xl border">
            <div class="border-b text-center py-2 font-krub-bold">
                <span
                    class="animate-pulse h-6 bg-slate-200 rounded inline-block w-[40%]"
                ></span>
            </div>
            <div class="flex flex-col justify-center items-center py-2">
                <span
                    class="animate-pulse h-3 bg-slate-200 rounded inline-block w-[40%]"
                ></span>
            </div>
        </div>
    </li>
</template>
<script setup lang="ts">
import axios from "axios";
import { ref, onMounted, watch } from "vue";
const props = defineProps(["period"]);

const loading = ref(true);
const data: any = ref([]);
const fetchData = () => {
    loading.value = true;
    axios
        .get(
            route("dashboard.inbound.data.escalation.ticket-status", {
                periode: props.period,
            })
        )
        .then((result) => {
            data.value = result.data;
            loading.value = false;
        });
};

onMounted(() => {
    fetchData();
});

watch(
    () => props.period,
    (periode, value) => {
        fetchData();
    }
);
</script>
