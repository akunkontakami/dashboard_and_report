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
                <InboundTicketSolverBtAgent />
            </div>
            <div class="col-span-3">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    First Response Time (FRT) Last 7 days
                </h1>
                <div
                    class="bg-white border rounded-lg px-3 py-2 h-[200px]"
                ></div>
                <h1 class="font-krub-bold text-[13px] mb-1 mt-3">
                    First Resolution Time Last 7 days
                </h1>
                <div
                    class="bg-white border rounded-lg px-3 py-2 h-[200px]"
                ></div>
            </div>
        </div>
    </section>
</template>
<script setup lang="ts">
import CardLiveDaily from "@/Components/Card/CardLiveDaily.vue";
import InboundTicketSolverBtAgent from "@/Components/Chart/InboundTicketSolverBtAgent.vue";
import axios from "axios";
import { ref, onMounted } from "vue";
</script>
