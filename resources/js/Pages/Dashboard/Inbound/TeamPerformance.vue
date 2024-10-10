<template>
    <section>
        <div class="flex justify-end md:mt-[-40px]">
            <DropdownPeriode :period="period" @update="updatePeriode" />
        </div>
        <div class="grid md:grid-cols-5 grid-cols-1 md:gap-2 mt-3">
            <div class="col-span-3">
                <h1 class="font-krub-bold text-[13px] mb-1">Ticket Status</h1>
                <ul
                    class="grid lg:grid-cols-3 md:grid-cols-3 grid-cols-2 gap-2 list-none"
                >
                    <CardTicketPerformance :period="period" />
                </ul>
                <div class="grid md:grid-cols-2 lg:grid-cols-2 md:gap-2 mt-3">
                    <ChartPieTicketAllChannelTeamPerformance :period="period" />
                </div>
            </div>
            <div class="md:col-span-2 w-full md:mt-0 mt-3">
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
                Ticket by Type
            </h1>
            <InboundTeamPerformanceTicketByType :period="period" />
        </div>
    </section>
</template>
<script setup lang="ts">
import DropdownPeriode from "@/Components/Dropdown/DropdownPeriode.vue";
import InboundTeamPerformanceTicketByType from "@/Components/Chart/Inbound/InboundTeamPerformanceTicketByType.vue";
import CardTicketPerformance from "@/Components/Card/Inbound/CardTicketPerformance.vue";
import ChartPieTicketAllChannelTeamPerformance from "@/Components/Chart/Inbound/ChartPieTicketAllChannelTeamPerformance.vue";
import { ref } from "vue";
import { getQueryParam, routeAppendParam } from "@/Plugins/Function/global-function";
import CardAgentDeal from "@/Components/Card/Inbound/CardAgentDeal.vue";

const props = defineProps(["products"]);
const period = ref(getQueryParam("period", "today"));
const productId = ref(getQueryParam("product_id"));

const updatePeriode = (value: string) => {
    routeAppendParam({ period: value });
    period.value = value;
    productId.value = value
};
const init = () => {
    const products = props.products;
    if (products.length) {
        productId.value = products[0].id;
    }
};
</script>
