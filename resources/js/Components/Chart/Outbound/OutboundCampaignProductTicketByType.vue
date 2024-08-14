<template>
     <div>
         <ul class="flex  gap-1 items-center justify-center">
             <li
                 class="text-[10px] flex gap-1 items-center justify-center"
                 v-for="badge in categories"
             >
                 <span
                     class="block w-[10px] h-[10px] rounded-sm"
                     :style="`background-color:${badge[1]}`"
                 ></span>
                 <span>{{ badge[0] }}</span>
             </li>
         </ul>
         <VueApexCharts
             type="bar"
             :height="400"
             :options="chart.options"
             :series="chart.series"
             v-if="chart && haveData && !loading"
         ></VueApexCharts>
         <div
             v-if="!haveData || loading"
             class="flex flex-col justify-center items-center py-3 min-h-[265px]"
         >
             <EmptyState class="w-[100px] h-[100px]" v-if="!loading" />
             <span class="text-[12px] mt-3 block" v-if="!loading"
                 >Data not found</span
             >
             <span class="text-[12px] mt-3 block" v-if="loading"
                 >Loading ...</span
             >
         </div>
     </div>
 </template>
 <script setup lang="ts">
 import VueApexCharts from "vue3-apexcharts";
 import axios from "axios";
 import EmptyState from "../../Icon/Etc/EmptyState.vue";
 import { ref, onMounted, watch } from "vue";
 
 const props = defineProps(["period","campaignId","productId"]);
 const loading = ref(true);
 const haveData = ref(false);
 const chart: any = ref(null);
 
 const categories = ref([
     ["New", "#FF605C"],
     ["Open", "#FFBD44"],
     ["Solved", "#26C0F1"],
     ["Closed", "#00CA4E"],
 ]);
 
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
         },
         plotOptions: {
             bar: {
                 distributed: true,
                 horizontal: true,
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
             offsetX: 30,
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
         legend: {
             show: false,
         },
     },
 };
 const fetchData = () => {
     loading.value = true;
     axios
         .get(
             route("dashboard.outbound.data.campaign.ticket-by-type", {
                 periode: props.period,
                 campaign_id : props.campaignId,
                 product_id : props.productId
             })
         )
         .then((result) => {
             const items = result.data;
             loading.value = false;
             chartConfig.series[0] = {
                 name: "Total",
                 data: items.map((row: any) => row.total),
                 label: items.map((row: any) => row.status_category),
             };
             chartConfig.options.xaxis.categories = items.map(
                 (row: any) => row.status
             );
             chartConfig.options.colors = items.map((row: any) => row.color);
             chart.value = chartConfig;
             if (items.length) {
                 haveData.value = true;
             }
         });
 };
 
 onMounted(() => {
     if(props.campaignId || props.productId){
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
 