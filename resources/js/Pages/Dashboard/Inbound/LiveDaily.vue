<template>
    <section>
        <ul class="grid lg:grid-cols-6 md:grid-cols-3 grid-cols-2 gap-2">
            <li>
                <CardLiveDaily title="New" value="40" :yesterday="-5" />
            </li>
            <li>
                <CardLiveDaily
                    title="Unassigned Enquiry"
                    value="20"
                    :yesterday="-4"
                />
            </li>
            <li>
                <CardLiveDaily
                    title="Unassigned Ticket"
                    value="70"
                    :yesterday="20"
                />
            </li>
            <li>
                <CardLiveDaily title="Open" value="99" :yesterday="15" />
            </li>
            <li>
                <CardLiveDaily title="Solved" value="0" :yesterday="0" />
            </li>
            <li>
                <CardLiveDaily title="Escalated" value="63" :yesterday="-10" />
            </li>
        </ul>

        <div class="grid grid-cols-5 gap-2 mt-3">
            <div class="col-span-2">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Tickets Solved by Agent (Top 10)
                </h1>
                <InboundTicketSolverBtAgent />
            </div>
            <div class="col-span-3">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    First Response Time (FRT) Last 7 days
                </h1>
                <div class="bg-white border rounded-lg px-3 py-2 h-[200px]">
                </div>
                <h1 class="font-krub-bold text-[13px] mb-1 mt-3">
                    First Resolution Time Last 7 days
                </h1>
                <div class="bg-white border rounded-lg px-3 py-2 h-[200px]">
                </div>
            </div>
        </div>
    </section>
</template>
<script setup lang="ts">
import CardLiveDaily from "@/Components/Card/CardLiveDaily.vue";
import InboundTicketSolverBtAgent from "@/Components/Chart/InboundTicketSolverBtAgent.vue";
</script>
