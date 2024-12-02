<template>
    <AppLayout title="Dashboard" header="Dashboard">
        <template v-slot:tab>
            <TabCategory tab="inbound" />
        </template>
        <TabMenu :active="type" />
        <LiveDaily v-if="type=='live-daily'"/>
        <Kpi v-if="type=='kpi'"/>
        <TeamPerformance v-if="type=='team-performance'"/>
        <Sla v-if="type=='sla'"/>
        <EscalationPerformance v-if="type=='escalation-performance'"/>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import TabCategory from "../TabCategory.vue";
import TabMenu from "./TabMenu.vue";
import LiveDaily from "./LiveDaily.vue";
import Kpi from "./Kpi.vue";
import TeamPerformance from "./TeamPerformance.vue";
import Sla from "./Sla.vue";
import EscalationPerformance from "./EscalationPerformance.vue";

defineProps(["type"]);
</script>
