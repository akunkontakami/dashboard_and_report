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
                    <CardKpiTicketStatus :period="period"/>
                    <!-- <CardKpi value="0" label="New" textClass="text-[#FF605C]" />
                    <CardKpi
                        value="0"
                        label="Open"
                        textClass="text-[#FFBD44]"
                    />
                    <CardKpi
                        value="0"
                        label="Solved"
                        textClass="text-[#26C0F1]"
                    />
                    <CardKpi
                        value="0"
                        label="Closed"
                        textClass="text-[#00CA4E]"
                    /> -->
                </ul>
            </div>
            <div class="md:col-span-2 w-full md:mt-0 mt-3">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    First Response Time & First Resolution Time
                </h1>
                <div
                    class="grid lg:grid-cols-2 sm:grid-cols-2 grid-cols-1 gap-2"
                >
                    <CardKpi value="0" label="average first response time" />
                    <CardKpi value="0" label="average first resolution time" />
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-5 grid-cols-1 md:gap-2 mt-3">
            <div class="col-span-3 flex flex-col">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Daily Ticket Activity
                </h1>
                <div class="bg-white rounded-xl px-3 py-2 border h-full min-h-[200px]">
              
                </div>
            </div>
            <div class="md:col-span-2 w-full md:mt-0 mt-3 flex flex-col">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    First Response Time & First Resolution Time
                </h1>
                <div class="bg-white rounded-xl px-3 py-2 border h-full min-h-[200px]">
                </div>
            </div>
        </div>
        <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-2 mt-3">
            <CardPieChannel
                label="Ticket by Channel"
                :badges="ticketChannelBadges"
            >
                <div class="bg-[#ddd] w-[120px] h-[120px] rounded-full"></div>
            </CardPieChannel>
            <CardPieChannel label="Voice PSTN" :badges="voicePstnBadges">
                <div class="bg-[#ddd] w-[120px] h-[120px] rounded-full"></div>
            </CardPieChannel>
            <CardPieChannel label="Web Call" :badges="webCallBadges">
                <div class="bg-[#ddd] w-[120px] h-[120px] rounded-full"></div>
            </CardPieChannel>
            <CardPieChannel label="CSAT" :badges="csatBadges">
                <div class="bg-[#ddd] w-[120px] h-[120px] rounded-full"></div>
            </CardPieChannel>
        </div>
        <div class="bg-white mt-3 px-3 py-2 rounded-xl border">
            <h1 class="font-krub-bold text-[13px] mb-1 text-center">Ticket by Type</h1>
            <div class="h-[300px]"></div>
        </div>
    </section>
</template>
<script setup lang="ts">
import DropdownPeriode from "@/Components/Dropdown/DropdownPeriode.vue";
import CardKpi from "@/Components/Card/CardKpi.vue";
import CardPieChannel from "@/Components/Card/CardPieChannel.vue";
import CardKpiTicketStatus from "@/Components/Card/CardKpiTicketStatus.vue";
import { ref } from "vue";

const period = ref("today");
const ticketChannelBadges = ref([
    ["bg-[#2D9CDB]", "Web Call"],
    ["bg-[#F8961E]", "Web Chat"],
    ["bg-[#F9C74F]", "Web BOT"],
    ["bg-[#90BE6D]", "Whatsapp"],
    ["bg-[#F3722C]", "Whatsapp Bot"],
    ["bg-[#7A7E80]", "Email"],
    ["bg-[#E99C00]", "Instagram"],
    ["bg-[#E4BEBE]", "Facebook"],
]);
const voicePstnBadges = ref([
    ["bg-[#FF605C]", "Abandoned call"],
    ["bg-[#F3722C]", "Missed Call"],
]);
const webCallBadges = ref([["bg-[#F94144]", "Missed Call"]]);
const csatBadges = ref([
    ["bg-[#90BE6D]", "Good"],
    ["bg-[#F94144]", "Bad"],
]);


const updatePeriode = (value: string) => {
    period.value = value;
};
</script>
