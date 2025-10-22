<template>
    <section>
        <div class="flex justify-end md:mt-[-40px]">
            <DropdownPeriode :period="period" @update="updatePeriode" />
        </div>
        <div class="grid md:grid-cols-5 grid-cols-1 md:gap-2 mt-3">
            <div class="md:col-span-1 w-full md:mt-0 mt-3">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    FTTR
                </h1>
                <ul
                    class="grid lg:grid-cols-1 sm:grid-cols-2 grid-cols-1 gap-2 list-none"
                >
                    <CardKpiSlaTime :period="period" />
                </ul>
            </div>
            <div class="col-span-4">
                <div class="grid md:grid-cols-2 grid-cols-1 md:gap-2 mt-3">
                    <div class="md:col-span-1 w-full md:mt-0 mt-3">
                        <h1 class="font-krub-bold text-[13px] mb-1">Priority</h1>
                        <InboundTeamPerformanceTicketByType :period="period" />
                    </div>
                    <div class="md:col-span-1 w-full md:mt-0 mt-3">
                        <h1 class="font-krub-bold text-[13px] mb-1">Ticket requiring attention by priority</h1>
                        <InboundTeamPerformanceTicketByType :period="period" />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 grid-cols-1 md:gap-2 mt-3">
                    <div class="md:col-span-1 w-full md:mt-0 mt-3">
                        <h1 class="font-krub-bold text-[13px] mb-1">Priority</h1>
                        <InboundTeamPerformanceTicketByType :period="period" />
                    </div>
                    <div class="md:col-span-1 w-full md:mt-0 mt-3">
                        <h1 class="font-krub-bold text-[13px] mb-1">Ticket requiring attention by priority</h1>
                        <InboundTeamPerformanceTicketByType :period="period" />
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-5 grid-cols-1 md:gap-2 mt-3">
            <div class="col-span-3 flex flex-col">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Daily Ticket Activity
                </h1>
                <InboundKpiDailyActivityChart :period="period" />
            </div>
            <div class="md:col-span-2 w-full md:mt-0 mt-3 flex flex-col">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    First Response Time & First Resolution Time
                </h1>
                <InboundKpiSlaChart :period="period" />
            </div>
        </div>

    </section>
</template>
<script setup lang="ts">
import DropdownPeriode from "@/Components/Dropdown/DropdownPeriode.vue";
import InboundTeamPerformanceTicketByType from "@/Components/Chart/Inbound/InboundTeamPerformanceTicketByType.vue";
import InboundKpiDailyActivityChart from "@/Components/Chart/Inbound/InboundKpiDailyActivityChart.vue";
import InboundKpiSlaChart from "@/Components/Chart/Inbound/InboundKpiSlaChart.vue";
import CardKpiSlaTime from "@/Components/Card/Inbound/CardKpiSlaTime.vue";
import { ref } from "vue";
import { getQueryParam, routeAppendParam } from "@/Plugins/Function/global-function";

const period = ref(getQueryParam('period',"today"));

const updatePeriode = (value: string) => {
    routeAppendParam({ period: value });
    period.value = value;
};
</script>
