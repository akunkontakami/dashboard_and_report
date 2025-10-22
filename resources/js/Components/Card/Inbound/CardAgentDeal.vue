<template>
    <li v-for="row in data" v-if="!loading">
        <div
            class="bg-white rounded-xl px-3 py-2 border h-full flex items-center gap-2"
        >
            <img
                :src="row.profile"
                :alt="row.name"
                class="w-[30px] h-[30px] object-cover rounded-full"
            />
            <div class="flex flex-col">
                <p class="text-yellow text-[12px] font-krub-semibold">
                    {{row.name}}
                </p>
                <ul class="flex gap-3 text-[11px]">
                    <li>Open : <strong>{{row.open}}</strong></li>
                    <li>Closed : <strong>{{row.total}}</strong></li>
                </ul>
            </div>
        </div>
    </li>
    <div
        class="flex flex-col justify-center items-center py-3 h-full bg-white border rounded-lg"
        v-if="!data.length || loading"
    >
        <EmptyState class="w-[100px] h-[100px]" v-if="!loading" />
        <span class="text-[12px] mt-3 block" v-if="!loading"
            >Data not found</span
        >
        <span class="text-[12px] mt-3 block" v-if="loading">Loading ...</span>
    </div>
</template>
<script setup lang="ts">
import EmptyState from "../../Icon/Etc/EmptyState.vue";
 import axios from "axios";
 import { ref, onMounted, watch } from "vue";
 const props = defineProps(["period","campaignId","productId"]);

 const loading = ref(true);
 const data: any = ref([]);
 const fetchData = () => {
     loading.value = true;
     axios
         .get(
             route("dashboard.outbound.data.team-performance.top-closed", {
                 periode: props.period,
                 product_id : props.productId,
                 campaign_id : props.campaignId
             })
         )
         .then((result) => {
             data.value = result.data;
             loading.value = false;
         });
 };

 onMounted(() => {
    if(props.campaignId || props.productId){
        fetchData();
    }
 });

 watch(
     () => props.period,
     (periode, value) => {
         fetchData();
     }
 );
 watch(
     () => props.campaignId,
     (periode, value) => {
         fetchData();
     }
 );
 watch(
     () => props.productId,
     (periode, value) => {
         fetchData();
     }
 );
 </script>
