<template>
    <div class="bg-white border rounded-lg pt-3 pr-3">
        <VueApexCharts
            type="bar"
            :height="200"
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
                fontWeight: "normal",
                color: "black",
            },
            offsetY: -20,
            background: {
                enabled: true,
                foreColor: "#000",
                borderColor: "#fff",
                opacity: 0.9,
            },
            formatter: function (val: any, opts: any) {
                const label = opts.config.series[0].label;
                return label[opts.dataPointIndex];
            },
        },
        xaxis: {
            categories: ['#','#','#','#','#','#','#'],
            labels: {
                show: true,
                style: {
                    fontSize: "10px",
                    fontFamily: "Helvetica, Arial, sans-serif",
                    fontWeight: "bold",
                    color: "black",
                },
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
            colors: ["#76B958"],
        },
        tooltip: {
            y: {
                formatter: function (value: any, series: any) {
                    const label = chartConfig.series[0].label;
                    return label[series.dataPointIndex];
                },
            },
        },
    },
    series: [
        {
            name: "Total",
            data: [0, 0, 0, 0, 0, 0, 0],
            label: ['#','#','#','#','#','#','#'],
        },
    ],
};
const fetchLiveDailyCard = () => {
    loading.value = true;
    axios
        .get(route("dashboard.inbound.data.live-daily.first-response-time"))
        .then((result) => {
            const items = result.data;
            loading.value = false;
            chartConfig.series[0] = {
                name: "Total",
                data: items.map((row: any) => row.frt),
                label: items.map((row: any) => row.label),
            };
            chartConfig.options.xaxis.categories = items.map(
                (row: any) => row.date
            );
            chart.value = chartConfig;
        });
};

onMounted(() => {
    fetchLiveDailyCard();
});
</script>
