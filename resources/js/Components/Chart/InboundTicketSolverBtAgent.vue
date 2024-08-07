<template>
    <div class="bg-white border rounded-lg pt-3 pr-3">
        <VueApexCharts
            type="bar"
            :height="400"
            :options="chart.options"
            :series="chart.series"
            v-if="chart"
        ></VueApexCharts>
    </div>
</template>
<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
import { ref, onMounted } from "vue";

const loading = ref(true);
const chart: any = ref(null);
const chartConfig = {
    options: {
        chart: {
            type: "bar",
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                borderRadiusApplication: "end",
                horizontal: true,
                dataLabels: {
                    position: "top",
                },
            },
        },
        dataLabels: {
            enabled: true,
            style: {
                fontSize: "10px",
                fontFamily: "Helvetica, Arial, sans-serif",
                fontWeight: "bold",
                color: "black",
            },
            offsetX: 40,
            background: {
                enabled: true,
                foreColor: "#000",
                borderColor: "#fff",
                opacity: 0.9,
            },
        },
        xaxis: {
            categories: [],
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
        fill: {
            colors: ["#76B958"],
        },
    },
    series: [
        {
            name: "Total",
            data: [],
        },
    ],
};
const fetchLiveDailyCard = () => {
    loading.value = true;
    axios
        .get(route("dashboard.inbound.data.live-daily.ticket-solved"))
        .then((result) => {
            const items = result.data;
            loading.value = false;
            chartConfig.series[0] = {
                name: "Total",
                data: items.map((row: any) => row.total),
            };
            chartConfig.options.xaxis.categories = items.map((row:any)=>row.name)
            chart.value = chartConfig
        });
};

onMounted(() => {
    fetchLiveDailyCard();
});
</script>
