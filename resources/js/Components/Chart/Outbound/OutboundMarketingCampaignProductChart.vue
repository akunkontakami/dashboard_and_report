<template>
    <div class="grid grid-cols-3 gap-1 h-full" v-if="haveData && !loading">
        <div class="col-span-2">
            <VueApexCharts
                type="bar"
                :height="250"
                :options="chart.options"
                :series="chart.series"
                v-if="chart && haveData && !loading"
            ></VueApexCharts>
        </div>
        <div class="flex justify-center items-center">
            <div
                class="border rounded-xl shadow-lg w-fit flex flex-col items-center px-4 py-3"
            >
                <h1 class="text-4xl font-bold">
                    {{ higher.total }}
                </h1>
                <p class="text-[11px]">
                    {{ higher.label }}
                </p>
            </div>
        </div>
    </div>
    <div
        class="flex flex-col justify-center items-center py-3 min-h-[265px]"
        v-else
    >
        <EmptyState class="w-[100px] h-[100px]" v-if="!loading" />
        <span class="text-[12px] mt-3 block" v-if="!loading"
            >Data not found</span
        >
        <span class="text-[12px] mt-3 block" v-if="loading">Loading ...</span>
    </div>
</template>
<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import EmptyState from "../../Icon/Etc/EmptyState.vue";
import axios from "axios";
import { ref, onMounted, watch } from "vue";

const props = defineProps(["period", "campaignId", "productId"]);
const loading = ref(true);
const haveData = ref(false);
const chart: any = ref(null);
const higher: any = ref(null);

const chartConfig = {
    series: [
        {
            name: "Total",
            data: [],
            label: [],
        },
    ],
    options: {
        chart: {
            type: "bar",
            toolbar: {
                show: false,
            },
        },
        plotOptions: {
            bar: {
                distributed: true,
                borderRadius: 4,
                borderRadiusApplication: "end",
                dataLabels: {
                    position: "top",
                },
            },
        },
        colors: [],
        dataLabels: {
            enabled: true,
            style: {
                fontSize: "10px",
                fontFamily: "Helvetica, Arial, sans-serif",
                fontWeight: "normal",
                color: "black",
            },
            background: {
                enabled: true,
                foreColor: "#000",
                borderColor: "#fff",
                opacity: 0.9,
            },
        },
        xaxis: {
            categories: [],
        },
        yaxis: {
            labels: {
                align: "left",
                style: {
                    fontSize: "11px",
                    width: "4000px",
                },
                offsetX: -10,
            },
        },
        legend: {
            show: false,
        },
    },
};
const fetchData = () => {
    loading.value = true;
    axios
        .get(
            route("dashboard.outbound.data.campaign.chart-campaign", {
                periode: props.period,
                campaign_id: props.campaignId,
                product_id: props.productId,
            })
        )
        .then((result) => {
            const data = result.data;
            const items = data.items;
            higher.value = data.higher;
            loading.value = false;
            chartConfig.series[0] = {
                name: "Total",
                data: items.map((row: any) => row.value),
                label: items.map((row: any) => row.label),
            };
            chartConfig.options.xaxis.categories = items.map(
                (row: any) => row.label
            );
            chartConfig.options.colors = items.map((row: any) => row.color);
            chart.value = chartConfig;
            if (items.length) {
                haveData.value = true;
            }
        });
};

onMounted(() => {
    if (props.campaignId || props.productId) {
        fetchData();
    }
});

watch(
    () => props.period,
    (period, value) => {
        fetchData();
    }
);
watch(
    () => props.campaignId,
    (period, value) => {
        fetchData();
    }
);

watch(
    () => props.productId,
    (period, value) => {
        fetchData();
    }
);
</script>
