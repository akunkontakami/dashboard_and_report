<template>
    <section>
        <div class="flex justify-end md:mt-[-40px]">
            <DropdownPeriode :period="period" @update="updatePeriode" />
        </div>
        <div class="grid md:grid-cols-5 grid-cols-1 md:gap-2 mt-3">
            <div class="col-span-3">
                <h1 class="font-krub-bold text-[13px] mb-1">Ticket Status</h1>
                <ul
                    class="grid lg:grid-cols-4 md:grid-cols-3 grid-cols-2 gap-2 list-none"
                >
                    <CardKpiTicketStatus :period="period" />
                </ul>
            </div>
            <div class="md:col-span-2 w-full md:mt-0 mt-3">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    First Response Time & First Resolution Time
                </h1>
                <ul
                    class="grid lg:grid-cols-2 sm:grid-cols-2 grid-cols-1 gap-2 list-none"
                >
                    <CardKpiSlaTime :period="period" />
                </ul>
            </div>
        </div>


        <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-2 mt-3">
            <ChartPieTicketAllChannel :period="period" />
        </div>

    </section>
</template>
<script setup lang="ts">
import DropdownPeriode from "@/Components/Dropdown/DropdownPeriode.vue";
import InboundKpiTicketByType from "@/Components/Chart/Inbound/InboundKpiTicketByType.vue";
import CardKpiTicketStatus from "@/Components/Card/Inbound/CardKpiTicketStatus.vue";
import InboundKpiDailyActivityChart from "@/Components/Chart/Inbound/InboundKpiDailyActivityChart.vue";
import InboundKpiSlaChart from "@/Components/Chart/Inbound/InboundKpiSlaChart.vue";
import ChartPieTicketAllChannel from "@/Components/Chart/Inbound/ChartPieTicketAllChannel.vue";
import CardKpiSlaTime from "@/Components/Card/Inbound/CardKpiSlaTime.vue";
import { ref } from "vue";
import { getQueryParam, routeAppendParam } from "@/Plugins/Function/global-function";

const period = ref(getQueryParam('period',"today"));

const updatePeriode = (value: string) => {
    routeAppendParam({ period: value });
    period.value = value;
};
</script>
