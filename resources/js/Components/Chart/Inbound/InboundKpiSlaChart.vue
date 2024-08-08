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
             },
         },
         dataLabels: {
             enabled: false,
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
             colors: ["#00C44C", "#FFBD44"],
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
             y: {
                 formatter: function (value: any, series: any) {
                     const label = chartConfig.series[series.seriesIndex].label;
                     return label[series.dataPointIndex];
                 },
             },
         },
     },
     series: [
         {
             name: "Total",
             color: "black",
             data: [],
             label : []
         },
     ],
 };
 const fetchData = () => {
     loading.value = true;
     axios
         .get(
             route("dashboard.inbound.data.kpi.sla-chart", {
                 periode: props.period,
             })
         )
         .then((result) => {
             loading.value = false;
             const items = result.data;
             chartConfig.series[0] = {
                 name: "Response Time",
                 color: "#00C44C",
                 data: items.map((row: any) => row.response.value),
                 label: items.map((row: any) => row.response.label),
             };
             chartConfig.series[1] = {
                 name: "Resolution",
                 color: "#FFBD44",
                 data: items.map((row: any) => row.resolution.value),
                 label: items.map((row: any) => row.resolution.label),
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
 