<template>
    <section>
        <ul class="grid lg:grid-cols-6 md:grid-cols-3 grid-cols-2 gap-2">
            <CardLiveDaily />
        </ul>

        <div class="grid grid-cols-5 gap-2 mt-3">
            <div class="col-span-2">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Tickets Solved by Agent (Top 10)
                </h1>
                <InboundTicketSolvedByAgent />
            </div>
            <div class="col-span-3">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    First Response Time (FRT) Last 7 days
                </h1>
                <InboundFirstResponseTime />

                <h1 class="font-krub-bold text-[13px] mb-1 mt-3">
                    First Resolution Time Last 7 days
                </h1>
                <InboundFirstResolutionTime />
            </div>
        </div>
    </section>
</template>
<script setup lang="ts">
import CardLiveDaily from "@/Components/Card/Inbound/CardLiveDaily.vue";
import InboundTicketSolvedByAgent from "@/Components/Chart/Inbound/InboundTicketSolvedByAgent.vue";
import InboundFirstResponseTime from "@/Components/Chart/Inbound/InboundFirstResponseTime.vue";
import InboundFirstResolutionTime from "@/Components/Chart/Inbound/InboundFirstResolutionTime.vue";
</script>
