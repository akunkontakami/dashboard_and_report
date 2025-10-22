<template>
     <div
         class="flex items-center gap-2 bg-white rounded-md border px-2 py-2 my-1 shadow-sm"
     >
         <p
             class="my-0 text-[13px] font-krub-bold"
             v-bind:class="{
                 'text-offline': parseInt(time) < 0 || time.includes('-0'),
                 'text-online': parseInt(time) >= 0 && !time.includes('-0'),
             }"
         >
             {{ time?.replace("-", "") }}
         </p>
         <IconSlaGreen v-if="parseInt(time) >=0 && !time.includes('-0')"/>
         <IconSlaRed v-else/>
     </div>
 </template>
 <script setup lang="ts">
 import IconSlaGreen from "@/Components/Icon/Etc/IconSlaGreen.vue";
 import IconSlaRed from "@/Components/Icon/Etc/IconSlaRed.vue";
 defineProps(["time"]);
 </script>
 