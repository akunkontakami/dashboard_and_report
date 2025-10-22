<template>
     <div
         class="flex items-center gap-2 bg-white rounded-md border px-2 py-1 my-1 shadow-sm"
     >
         <div>
             <p
                 class="my-0 text-[12px] font-krub-bold"
                 v-bind:class="{
                     'text-offline':
                         parseInt(resolution) < 0 || resolution.includes('-0'),
                     'text-online':
                         parseInt(resolution) >= 0 && !resolution.includes('-0'),
                 }"
             >
                 {{ resolution?.replace("-", "") }}
             </p>
             <p class="my-0 text-[8px] mt-[-6px]">
                 Response Time :
                 <b
                     v-bind:class="{
                         'text-offline':
                             parseInt(response) < 0 || response.includes('-0'),
                         'text-online':
                             parseInt(response) >= 0 && !response.includes('-0'),
                     }"
                 >
                     {{ response?.replace("-", "") }}
                 </b>
             </p>
         </div>
         <IconSlaGreen
             v-if="parseInt(resolution) >= 0 && !resolution.includes('-0')"
         />
         <IconSlaRed v-else />
     </div>
 </template>
 <script setup lang="ts">
 import IconSlaGreen from "@/Components/Icon/Etc/IconSlaGreen.vue";
 import IconSlaRed from "@/Components/Icon/Etc/IconSlaRed.vue";
 defineProps(["resolution", "response"]);
 </script>
 