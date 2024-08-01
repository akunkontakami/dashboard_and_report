<template>
    <AppLayout title="Dashboard" header="Dashboard">
        <template v-slot:tab>
            <TabCategory tab="outbound" />
        </template>
        <TabMenu :active="type" />
        <MarketingCampaign v-if="type=='marketing-campaign'"/>
        <Product v-if="type=='product'"/>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import TabCategory from "../TabCategory.vue";
import TabMenu from "./TabMenu.vue";
import MarketingCampaign from "./MarketingCampaign.vue";
import Product from "./Product.vue";

defineProps(["type"]);
</script>
