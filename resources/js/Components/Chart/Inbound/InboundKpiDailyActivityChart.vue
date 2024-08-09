<template>
    <div class="bg-white border rounded-lg pt-3 pr-3">
        <VueApexCharts
            type="bar"
            :height="300"
            :options="chart.options"
            :series="chart.series"
            v-if="chart && !loading"
        ></VueApexCharts>
        <div
            v-if="loading"
            class="flex flex-col justify-center items-center py-3 min-h-[205px]"
        >
            <span class="text-[12px] mt-3 block">Loading ...</span>
        </div>
    </div>
</template>
<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
import { ref, onMounted, watch } from "vue";

const props = defineProps(["period"]);
const loading = ref(true);
const chart: any = ref(null);
const chartConfig = {
    options: {
        chart: {
            type: "bar",
            stacked: true,
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                borderRadiusApplication: "end",
                dataLabels: {
                    position: "center",
                },
            },
        },
        dataLabels: {
            enabled: true,
            textAnchor: "middle",
            style: {
                fontSize: "10px",
                fontFamily: "Helvetica, Arial, sans-serif",
                fontWeight: "normal",
                color: "black",
            },
        },
        xaxis: {
            categories: ["#", "#", "#", "#", "#", "#", "#"],
            labels: {
                show: true,
                style: {
                    fontSize: "9px",
                    fontFamily: "Helvetica, Arial, sans-serif",
                    fontWeight: "bold",
                    color: "black",
                },
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
            show: false,
            labels: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        fill: {
            colors: ["#FF605C", "#FFBD44", "#26C0F1", "#00CA95"],
        },
        legend: {
            position: "top",
        },
        tooltip: {
            shared: true,
            intersect: false,
            style: {
                fontSize: "10px",
                fontFamily: "Helvetica, Arial, sans-serif",
            },
            followCursor: true,
        },
    },
    series: [
        {
            name: "Total",
            color: "black",
            data: [0, 0, 0, 0, 0, 0, 0],
        },
    ],
};
const fetchData = () => {
    loading.value = true;
    axios
        .get(
            route("dashboard.inbound.data.kpi.ticket-activity", {
                periode: props.period,
            })
        )
        .then((result) => {
            loading.value = false;
            const items = result.data;
            chartConfig.series[0] = {
                name: "New",
                color: "#FF605C",
                data: items.map((row: any) => row.new),
            };
            chartConfig.series[1] = {
                name: "Open",
                color: "#FFBD44",
                data: items.map((row: any) => row.open),
            };
            chartConfig.series[2] = {
                name: "Solved",
                color: "#26C0F1",
                data: items.map((row: any) => row.solved),
            };
            chartConfig.series[3] = {
                name: "Closed",
                color: "#00CA95",
                data: items.map((row: any) => row.closed),
            };
            chartConfig.options.xaxis.categories = items.map(
                (row: any) => row.date
            );
            chart.value = chartConfig;
        });
};

onMounted(() => {
    fetchData();
});

watch(
    () => props.period,
    (period, value) => {
        fetchData();
    }
);
</script>
