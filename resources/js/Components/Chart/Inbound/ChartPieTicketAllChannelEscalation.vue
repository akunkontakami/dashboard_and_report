<template>
    <!-- Occupancy Rate -->
    <CardPieChannel
        label="Occupancy Rate"
        :loading="loading.ticket_channel"
        :isEmpty="sumArray(ticketByChannelSeries) == 0"
    >
        <VueApexCharts
            type="radialBar"
            :width="230"
            :options="{
                ...chartOptionsradialBar,

            }"
            :series="ticketByChannelSeries"
        ></VueApexCharts>
    </CardPieChannel>

    <!-- Net Promoter Score -->
    <CardPieChannel
        label="Net Promoter Score"
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


</template>
<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
import CardPieChannel from "@/Components/Chart/Inbound/CardPieChannel.vue";
import { ref, onMounted, watch } from "vue";

const props = defineProps(["period"]);
const ticketByChannelSeries = ref([]);
const webCallSeries = ref([]);

const loading = ref({
    ticket_channel: true,
    // voice_pstn: true,
    web_call: true,
    // csat: true,
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
    ["#3942B7", "Kontakami"],
    ["#C6BD48", "Walk-In"],
]);

const webCallBadges = ref([["#00CA4E", "Missed Call"]]);

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
const chartOptionsradialBar = ref({
    chart: {
              type: 'radialBar',
              offsetY: -20,
              offsetX: 50,
              sparkline: {
                enabled: true
              }
            },
            plotOptions: {
              radialBar: {
                startAngle: -120,
                endAngle: 120,
                track: {
                  background: "#e7e7e7",
                  strokeWidth: '97%',
                  margin: 5, // margin is in pixels
                  dropShadow: {
                    enabled: true,
                    top: 2,
                    left: 0,
                    color: '#999',
                    opacity: 1,
                    blur: 2
                  }
                },
                dataLabels: {
                  name: {
                    show: false
                  },
                  value: {
                    offsetY: 40,
                    fontSize: '22px'
                  }
                }
              }
            },
            grid: {
              padding: {
                top: -10
              }
            },
            fill: {
            //   type: 'gradient',
            //   gradient: {
            //     shade: 'light',
            //     shadeIntensity: 0.4,
            //     inverseColors: false,
            //     opacityFrom: 1,
            //     opacityTo: 1,
            //     stops: [0, 50, 53, 91]
            //   },
            },
            labels: ['Average Results'],





});
const fetchData = () => {
    loading.value = {
        ticket_channel: true,
        // voice_pstn: true,
        web_call: true,
        // csat: true,
    };
    ticketByChannel();
    webCall();
    // csatRating();
    // voicePstn()
};

const ticketByChannel = () => {
    axios
        .get(
            route("dashboard.inbound.data.escalation.ticket-channel", {
                periode: props.period,
            })
        )
        .then((result) => {
            const data = result.data || [];
            ticketByChannelSeries.value = data.map((row: any) => row.total);
            loading.value = {
                ...loading.value,
                ticket_channel: false,
            };
        });
};
const webCall = () => {
    axios
        .get(
            route("dashboard.inbound.data.escalation.web-call", {
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
