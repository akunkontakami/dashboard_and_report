<template>
    <section>
        <div class="flex justify-between mb-3">
            <DropdownCampaign
                :campaigns="campaigns"
                :campaignId="campaignId"
                @update="updateCampaign"
            />
            <DropdownPeriode :period="period" @update="updatePeriode" />
        </div>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-2">
            <div class="flex flex-col">
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Marketing Campaign
                </h1>
                <div class="bg-white rounded-xl px-3 py-2 border h-full">
                    <OutboundMarketingCampaignChart
                        :period="period"
                        :campaignId="campaignId"
                    />
                </div>
            </div>
            <div>
                <h1 class="font-krub-bold text-[13px] mb-1">
                    Top 5 Agents by Close Deals
                </h1>
                <ul class="flex flex-col gap-2">
                    <li v-for="n in 5">
                        <CardAgentDeal />
                    </li>
                </ul>
            </div>
        </div>
        <div class="bg-white mt-3 px-3 py-2 rounded-xl border">
            <h1 class="font-krub-bold text-[13px] mb-1 text-center">
                Ticket by Type
            </h1>
            <div class="h-[300px]"></div>
        </div>
    </section>
</template>
<script setup lang="ts">
import DropdownPeriode from "@/Components/Dropdown/DropdownPeriode.vue";
import DropdownCampaign from "@/Components/Dropdown/DropdownCampaign.vue";
import OutboundMarketingCampaignChart from "@/Components/Chart/Outbound/OutboundMarketingCampaignChart.vue";
import CardAgentDeal from "@/Components/Card/Outbound/CardAgentDeal.vue";
import { ref,onBeforeMount } from "vue";
import {
    getQueryParam,
    routeAppendParam,
} from "@/Plugins/Function/global-function";

const props = defineProps(["campaigns"]);
const period = ref(getQueryParam("period", "today"));
const campaignId = ref(getQueryParam("campaign_id"));

const updatePeriode = (value: string) => {
    routeAppendParam({ period: value });
    period.value = value;
};

const updateCampaign = (value: string) => {
    routeAppendParam({ campaign_id: value });
    campaignId.value = value;
};

const init = () =>{
    const campaigns = props.campaigns
    if(campaigns.length){
        campaignId.value = campaigns[0].id
    }
}

onBeforeMount(()=>{
    init()
})
</script>
