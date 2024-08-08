<template>
    <div>
        <CardPieChannel label="Ticket by Channel" :badges="ticketChannelBadges">
            <span class="text-[12px] mt-3 block" v-if="loading.ticket_channel">Loading ...</span>
            <VueApexCharts
                type="donut"
                :width="230"
                :options="{
                    ...chartOptions,
                    fill: {
                        colors: ticketChannelBadges.map((row) => row[0]),
                    },
                    colors : ticketChannelBadges.map((row) => row[0]),
                    labels: ticketChannelBadges.map((row) => row[1])
                }"
                :series="ticketByChannelSeries"
                v-else
            ></VueApexCharts>
        </CardPieChannel>
    </div>
</template>
<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
import CardPieChannel from "@/Components/Chart/Inbound/CardPieChannel.vue";
import { ref, onMounted, watch } from "vue";

const props = defineProps(["period"]);
const loading = ref({
    ticket_channel: true,
    voice_pstn: true,
    web_call: true,
    csat: true,
});
const ticketChannelBadges = ref([
    ["#F94144", "Voice PSTN"],
    ["#2D9CDB", "Web Call"],
    ["#F8961E", "Web Chat"],
    ["#F9C74F", "Web BOT"],
    ["#90BE6D", "Whatsapp"],
    ["#F3722C", "Whatsapp Bot"],
    ["#7A7E80", "Email"],
    ["#E99C00", "Instagram"],
    ["#E4BEBE", "Facebook"],
]);
const chartOptions = ref({
    chart: {
        type: "donut",
    },
    legend: {
        show: false,
    },
    dataLabels: {
        enabled: true,
        formatter: function (val: any, opt: any) {
            return ticketByChannelSeries.value[opt.seriesIndex];
        },
        style: {
            fontSize: "11px",
            fontFamily: "Helvetica, Arial, sans-serif",
            fontWeight: "normal",
        },
        dropShadow: {
            enabled: false,
        },
    },
});
const ticketByChannelSeries = ref([]);
const fetchData = () => {
    loading.value = {
        ticket_channel: true,
        voice_pstn: true,
        web_call: true,
        csat: true,
    };
    ticketByChannel()
};

const ticketByChannel = () => {
    axios
        .get(
            route("dashboard.inbound.data.kpi.ticket-channel", {
                periode: props.period,
            })
        )
        .then((result) => {
            const data = result.data || []
            ticketByChannelSeries.value = data.map((row:any)=>row.total)
            loading.value  = {
                ...loading.value,
                ticket_channel : false
            }
        });
}

onMounted(() => {
    fetchData();
});
watch(
    () => props.period,
    (period, value) => {
        fetchData();
    }
);
</script>
