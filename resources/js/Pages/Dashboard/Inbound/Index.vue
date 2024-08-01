<template>
    <AppLayout title="Dashboard" header="Dashboard">
        <template v-slot:tab>
            <TabCategory tab="inbound" />
        </template>
        <TabMenu :active="type" />
        <LiveDaily v-if="type=='live-daily'"/>
        <Kpi v-if="type=='kpi'"/>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import TabCategory from "../TabCategory.vue";
import TabMenu from "./TabMenu.vue";
import LiveDaily from "./LiveDaily.vue";
import Kpi from "./Kpi.vue";

defineProps(["type"]);
</script>
