<template>
    <section>
        <div class="flex justify-between mb-3">
            <DropdownProduct 
                :products="products" 
                :productId="productId"
                @update="updateProduct"
            />
            <DropdownPeriode :period="period" @update="updatePeriode" />
        </div>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-2">
            <div class="flex flex-col">
                <h1 class="font-krub-bold text-[13px] mb-1">Product</h1>
                <div class="bg-white rounded-xl px-3 py-2 border h-full">
                    <OutboundMarketingCampaignProductChart
                        :period="period"
                        :productId="productId"
                    />
                </div>
            </div>
            <div class="flex flex-col h-full">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Top 5 Agents by Close Deals
                </h1>
                <ul class="flex flex-col gap-2 flex-1">
                    <CardAgentDeal :period="period" :productId="productId" />
                </ul>
            </div>
        </div>
        <div class="bg-white mt-3 px-3 py-2 rounded-xl border">
            <h1 class="font-krub-bold text-[13px] mb-1 text-center">
                Ticket by Status
            </h1>
            <OutboundCampaignProductTicketByType
                :period="period"
                :productId="productId"
            />
        </div>
    </section>
</template>
<script setup lang="ts">
import DropdownPeriode from "@/Components/Dropdown/DropdownPeriode.vue";
import DropdownProduct from "@/Components/Dropdown/DropdownProduct.vue";
import OutboundMarketingCampaignProductChart from "@/Components/Chart/Outbound/OutboundMarketingCampaignProductChart.vue";
import OutboundCampaignProductTicketByType from "@/Components/Chart/Outbound/OutboundCampaignProductTicketByType.vue";
import CardAgentDeal from "@/Components/Card/Outbound/CardAgentDeal.vue";
import { ref,onBeforeMount } from "vue";
import {
    getQueryParam,
    routeAppendParam,
} from "@/Plugins/Function/global-function";

const props = defineProps(["products"]);
const period = ref(getQueryParam("period", "today"));
const productId = ref(getQueryParam("product_id"));

const updatePeriode = (value: string) => {
    routeAppendParam({ period: value });
    period.value = value;
};

const updateProduct = (value:string) => {
    routeAppendParam({product_id : value})
    productId.value = value
}
const init = () => {
    const products = props.products;
    if (products.length) {
        productId.value = products[0].id;
    }
};

onBeforeMount(() => {
    init();
});
</script>
