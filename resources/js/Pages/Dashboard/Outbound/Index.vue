<template>
     <AppLayout title="Dashboard" header="Dashboard">
         <template v-slot:tab>
             <TabCategory tab="outbound" />
         </template>
         <TabMenu :active="type" />
         Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt molestias
         dolor fugit aliquid officia saepe error excepturi blanditiis, itaque,
         culpa alias quod deleniti praesentium ut fuga repellat eveniet
         reiciendis cum.
     </AppLayout>
 </template>
 <script setup lang="ts">
 import AppLayout from "@/Layouts/AppLayout.vue";
 import TabCategory from "../TabCategory.vue";
 import TabMenu from "./TabMenu.vue";
 
 defineProps(["type"]);
 </script>
 