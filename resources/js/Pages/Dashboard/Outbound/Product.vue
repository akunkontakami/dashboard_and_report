<template>
     <section>
         <div class="flex justify-between mb-3">
             <DropdownProduct />
             <DropdownPeriode :period="period" @update="updatePeriode" />
         </div>
         <div class="grid md:grid-cols-2 grid-cols-1 gap-2">
             <div class="flex flex-col">
                 <h1 class="font-krub-bold text-[13px] mb-1">
                     Product
                 </h1>
                 <div class="bg-white rounded-xl px-3 py-2 border h-full">
                     <OutboundMarketingCampaignChart/>
                 </div>
             </div>
             <div>
                 <h1 class="font-krub-bold text-[13px] mb-1">
                     Top 5 Agents by Close Deals
                 </h1>
                 <ul class="flex flex-col gap-2">
                     <li v-for="n in 5">
                         <CardAgentDeal />
                     </li>
                 </ul>
             </div>
         </div>
         <div class="bg-white mt-3 px-3 py-2 rounded-xl border">
             <h1 class="font-krub-bold text-[13px] mb-1 text-center">Ticket by Status</h1>
             <div class="h-[300px]"></div>
         </div>
     </section>
 </template>
 <script setup lang="ts">
 import DropdownPeriode from "@/Components/Dropdown/DropdownPeriode.vue";
 import DropdownProduct from "@/Components/Dropdown/DropdownProduct.vue";
 import OutboundMarketingCampaignChart from "@/Components/Chart/OutboundMarketingCampaignChart.vue";
 import CardAgentDeal from "@/Components/Card/CardAgentDeal.vue";
 import { ref } from "vue";
 
 const period = ref("today");
 const updatePeriode = (value: string) => {
     period.value = value;
 };
 </script>
 