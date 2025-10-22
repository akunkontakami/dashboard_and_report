<template>
    <section>
        <div class="flex justify-between mb-3">
            <div></div>
            <DropdownPeriode :period="period" @update="updatePeriode" />
        </div>
        <div class="grid md:grid-cols-5 grid-cols-1 md:gap-3 mt-3">
            <div class="md:col-span-6 w-full md:mt-0 mt-3">

                <ul
                    class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-3 list-none"
                >
                    <CardKpiSlaTime :period="period" />
                </ul>
            </div>

        </div>

        <div class="grid grid-cols-12 gap-2 mt-3">
            <div class="col-span-4">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Outgoing calls by agent (top10)
                </h1>
                <OutboundTicketSolvedByAgentNotNew :period="period"/>
            </div>
            <div class="col-span-4">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Outgoing deals by agent (top10)
                </h1>
                <OutboundTicketSolvedByAgentClosed :period="period"/>
            </div>
            <div class="col-span-4">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Average outgoing calls by agent (top10)
                </h1>
                <OutboundTicketSolvedByAgentAverage :period="period"/>
            </div>

        </div>
    </section>
</template>
<script setup lang="ts">
import DropdownPeriode from "@/Components/Dropdown/DropdownPeriode.vue";
import OutboundTicketSolvedByAgentNotNew from "@/Components/Chart/Outbound/OutboundTicketSolvedByAgentNotNew.vue";
import OutboundTicketSolvedByAgentClosed from "@/Components/Chart/Outbound/OutboundTicketSolvedByAgentClosed.vue";
import OutboundTicketSolvedByAgentAverage from "@/Components/Chart/Outbound/OutboundTicketSolvedByAgentAverage.vue";
import CardKpiSlaTime from "@/Components/Card/Outbound/CardKpiSlaTime.vue";
import { ref,onBeforeMount } from "vue";
import {
    getQueryParam,
    routeAppendParam,
} from "@/Plugins/Function/global-function";
const period = ref(getQueryParam("period", "today"));
const updatePeriode = (value: string) => {
    routeAppendParam({ period: value });
    period.value = value;
};
</script>
