<template>
    <div class="bg-white border rounded-lg pt-3 pr-3">
        <VueApexCharts
            type="bar"
            :height="400"
            :options="chart.options"
            :series="chart.series"
            v-if="chart && haveData && !loading"
        ></VueApexCharts>
        <div v-if="!haveData || loading" class="flex flex-col justify-center items-center py-3 min-h-[265px]">
            <EmptyState class="w-[100px] h-[100px]"v-if="!loading"/>
            <span class="text-[12px] mt-3 block" v-if="!loading">Data not found</span>
            <span class="text-[12px] mt-3 block" v-if="loading">Loading ...</span>
        </div>
    </div>
</template>
<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
import EmptyState from "../../Icon/Etc/EmptyState.vue";
import { ref, onMounted, watch } from "vue";

const props = defineProps(["period"]);
const loading = ref(true);
const haveData = ref(false)
const chart: any = ref(null);
const chartConfig = {
    options: {
        chart: {
            type: "bar",
            toolbar: {
                show: false
            }
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
            offsetX: 20,
            background: {
                enabled: true,
                foreColor: "#000",
                borderColor: "#fff",
                opacity: 0.9,
            },
        },
        xaxis: {
            categories: ['#','#','#','#','#','#','#','#','#','#'],
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
            data: [0,0,0,0,0,0,0,0,0,0],
        },
    ],
};
const fetchData = () => {
    loading.value = true;
    axios
        .get(
            route("dashboard.outbound.data.live-daily.ticket-solved2", {
                 periode: props.period,
             })
            )
        .then((result) => {
            loading.value = false;
            console.log('nilai dari res agentClose',result);

            const items = result.data;
            chartConfig.series[0] = {
                name: "Total",
                data: items.map((row: any) => row.total),
            };
            chartConfig.options.xaxis.categories = items.map((row:any)=>row.name)
            chart.value = chartConfig
            console.log('nilai item',items);

            if(items.length){
                haveData.value = true
            }
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
