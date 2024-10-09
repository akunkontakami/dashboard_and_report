<template>

    <!-- Voice PSTN -->
    <CardPieChannel label="Voice PSTN" :badges="voicePstnBadges" :loading="loading.voice_pstn" :isEmpty="sumArray(voicePstnSeries)==0">
        <!-- <div class="bg-[#ddd] w-[150px] h-[150px] rounded-full"></div> -->
        <VueApexCharts
            type="donut"
            :width="230"
            :options="{
                ...chartOptions,
                fill: {
                    colors: webCallBadges.map((row) => row[0]),
                },
                colors: voicePstnBadges.map((row) => row[0]),
                labels: voicePstnBadges.map((row) => row[1]),
            }"
            :series="voicePstnSeries"
        ></VueApexCharts>
    </CardPieChannel>

    <!-- Web Call -->
    <CardPieChannel
        label="Web Call"
        :badges="webCallBadges"
        :loading="loading.web_call"
        :isEmpty="sumArray(webCallSeries) == 0"
    >
        <VueApexCharts
            type="donut"
            :width="230"
            :options="{
                ...chartOptions,
                fill: {
                    colors: webCallBadges.map((row) => row[0]),
                },
                colors: webCallBadges.map((row) => row[0]),
                labels: webCallBadges.map((row) => row[1]),
            }"
            :series="webCallSeries"
        ></VueApexCharts>
    </CardPieChannel>

    <!-- CSAT -->
    <CardPieChannel
        label="CSAT"
        :badges="csatBadges"
        :loading="loading.csat"
        :isEmpty="sumArray(csatSeries) == 0"
    >
        <VueApexCharts
            type="donut"
            :width="230"
            :options="{
                ...chartOptions,
                fill: {
                    colors: csatBadges.map((row) => row[0]),
                },
                colors: csatBadges.map((row) => row[0]),
                labels: csatBadges.map((row) => row[1]),
            }"
            :series="csatSeries"
        ></VueApexCharts>
    </CardPieChannel>
</template>
<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
import CardPieChannel from "@/Components/Chart/Inbound/CardPieChannel.vue";
import { ref, onMounted, watch } from "vue";

const props = defineProps(["period"]);
const webCallSeries = ref([]);
const voicePstnSeries = ref([]);
const csatSeries = ref([]);

const loading = ref({
    ticket_channel: true,
    voice_pstn: true,
    web_call: true,
    csat: true,
});
const csatBadges = ref([
    ["#90BE6D", "Good"],
    ["#F94144", "Bad"],
]);
const webCallBadges = ref([["#F94144", "Missed Call"]]);

const chartOptions = ref({
    chart: {
        type: "donut",
        toolbar: {
            show: false
        }
    },
    legend: {
        show: false,
    },
    dataLabels: {
        enabled: true,
        formatter: function (val: any, opt: any) {
            const labels = opt.w.config.labels;
            var value = opt.w.config.series[opt.seriesIndex];
            if (labels.includes("Good") && labels.includes("Bad")) {
                value = `${value}%`;
            }
            return value;
        },
        style: {
            fontSize: "7px",
            fontFamily: "Helvetica, Arial, sans-serif",
            fontWeight: "normal",
        },
        dropShadow: {
            enabled: false,
        },
    },
    tooltip: {
        y: {
            formatter: function (value: any, opt: any) {
                const labels = opt.config.labels;
                var value = opt.config.series[opt.seriesIndex];
                if (labels.includes("Good") && labels.includes("Bad")) {
                    value = `${value}%`;
                }
                return value;
            },
        },
    },
    plotOptions: {
        pie: {
            donut: {
                size: "65%",
                background: "transparent",
                labels: {
                    show: true,
                    total: {
                        show: true,
                        fontSize: "0",
                        formatter: function (w: any) {
                            var value = w.globals.seriesTotals.reduce(
                                (a: any, b: any) => {
                                    return a + b;
                                },
                                0
                            );

                            const labels = w.config.labels;
                            if (
                                labels.includes("Good") &&
                                labels.includes("Bad")
                            ) {
                                var value = w.config.series[0];
                                value = `${value}%`;
                            }
                            return value;
                        },
                    },
                    value: {
                        fontSize: "25px",
                        offsetY: -8,
                        fontFamily: "Helvetica, Arial, sans-serif",
                        fontWeight: "bold",
                        formatter: function (val: any, opt: any) {
                            const labels = opt.config.labels;
                            var value = val;
                            if (
                                labels.includes("Good") &&
                                labels.includes("Bad")
                            ) {
                                value = `${val}%`;
                            }
                            return value;
                        },
                    },
                },
            },
        },
    },
});
const fetchData = () => {
    loading.value = {
        ticket_channel: true,
        voice_pstn: true,
        web_call: true,
        csat: true,
    };

    webCall();
    csatRating();
    voicePstn()
};

const webCall = () => {
    axios
        .get(
            route("dashboard.inbound.data.team-performance.web-call", {
                periode: props.period,
            })
        )
        .then((result) => {
            const data = result.data || [];
            webCallSeries.value = data;
            loading.value = {
                ...loading.value,
                web_call: false,
            };
        });
};
const csatRating = () => {
    axios
        .get(
            route("dashboard.inbound.data.team-performance.csat", {
                periode: props.period,
            })
        )
        .then((result) => {
            const data = result.data || null;
            if (data) {
                const { good, bad } = data;
                csatSeries.value = [good, bad] as any;
            }
            loading.value = {
                ...loading.value,
                csat: false,
            };
        });
};

const voicePstn = () => {
    axios
        .get(
            route("dashboard.inbound.data.team-performance.voice-pstn", {
                periode: props.period,
            })
        )
        .then((result) => {
            const data = result.data || null;
            if (data) {
                const { abandoned, missed_call } = data;
                voicePstnSeries.value = [abandoned, missed_call] as any;
            }
            loading.value = {
                ...loading.value,
                voice_pstn: false,
            };
        });
};

const sumArray = (arr: any) => {
    return parseInt(arr.reduce((partialSum: any, a: any) => partialSum + a, 0));
};
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
