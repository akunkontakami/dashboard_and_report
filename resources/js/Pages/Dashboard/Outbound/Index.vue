<template>
    <AppLayout title="Dashboard" header="Dashboard">
        <template v-slot:tab>
            <TabCategory tab="outbound" />
        </template>
        <TabMenu :active="type" />
        <MarketingCampaign
            v-if="type == 'marketing-campaign'"
            :campaigns="campaigns"
        />
        <Product
            v-if="type == 'product'"
            :products="products"
        />
        <SalesCalls
            v-if="type == 'salescalls'"
            :salescalls="salescalls"
        />
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import TabCategory from "../TabCategory.vue";
import TabMenu from "./TabMenu.vue";
import MarketingCampaign from "./MarketingCampaign.vue";
import Product from "./Product.vue";
import SalesCalls from "./SalesCalls.vue";

defineProps(["type", "campaigns", "products", "salescalls"]);
</script>
