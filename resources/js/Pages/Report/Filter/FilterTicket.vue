<template>
    <Filter class="md:max-w-lg overflow-auto" :reset="reset">
        <div class="max-h-[65vh] overflow-auto -mx-6 px-6">
            <MultipleSelect
                label="Call Origin"
                placeholder="Choose Call Origin"
                :items="callOrigin.map((row:any)=>{
                 return {
                     id : row,
                     value : row
                 }
             })"
                v-model="filter.origins"
                v-if="type == 'inbound'"
            />

            <MultipleSelect
                label="Product Category"
                placeholder="Choose Product Category"
                :items="productCategory.map((row:any)=>{
                 return {
                         id : row,
                         value : row
                     }
                 })"
                v-model="filter.categories"
            />
            <MultipleHelpdesk
                label="Helpdesk Category"
                :helpdesk="helpdesk"
                v-model="filter.helpdesk_id"
                v-if="type === 'inbound'"
            />
            <MultipleCampaign
                label="Marketing Campaign"
                :campaigns="helpdesk"
                v-model="filter.campaign_id"
                v-if="type === 'outbound'"
            />
            <MultipleSelect
                label="Product Name"
                placeholder="Choose Product Name"
                :items="productList.map((row:any)=>{
                 return {
                     id : row,
                     value : row
                 }
             })"
                v-model="filter.product_name"
            />

            

            <MultipleSelect
                label="Escalation Division"
                placeholder="Choose Escalation Division"
                :items="escalations.map((row:any)=>{
                    return {
                        id : row.id,
                        value : row.name
                    }
                })"
                v-model="filter.escalation_id"
            />

            <div v-if="type == 'outbound'">
                <Select
                    label="Broadcast Response"
                    placeholder=""
                    v-model="filter.broadcast_response"
                >
                    <option value="">Choose Broadcast Response</option>
                    <option v-for="item in broadcastResponse" :value="item">
                        {{ item }}
                    </option>
                </Select>
            </div>

            <MultipleSelect
                label="Status"
                placeholder="Choose Status"
                :items="status.map((row:any)=>{
                return {
                     id : row.label,
                     value : row.label,
                     html : row.html,
                 }
             })"
                v-model="filter.status"
            />

            <MultipleSelect
                label="SPV"
                placeholder="Choose SPV"
                :items="spv"
                v-model="filter.spv_id"
            />
            <MultipleSelect
                label="Agent"
                placeholder="Choose Agent"
                :items="itemAgent"
                v-model="filter.agent_id"
            />
            <MultipleSelect
                label="Priority"
                placeholder="Choose Priority"
                :items="priorities.map((row:any)=>{
                 return {
                         id : row,
                         value : row
                     }
                 })"
                v-model="filter.priority"
            />
            <MultipleSelect
                label="Ticket SLA"
                placeholder="Choose Ticket SLA"
                :items="sla.map((row:any)=>{
                 return {
                         id : row.toLowerCase(),
                         value : row
                     }
                 })"
                v-model="filter.ticket_sla"
            />
            <MultipleSelect
                label="Division SLA"
                placeholder="Choose Division SLA"
                :items="sla.map((row:any)=>{
                 return {
                         id : row.toLowerCase(),
                         value : row
                     }
                 })"
                v-model="filter.division_sla"
            />
            <MultipleSelect
                label="Response Time"
                placeholder="Choose Response Time"
                :items="responseTime.map((row:any)=>{
                  return {
                          id : row.toLowerCase(),
                          value : row
                      }
                  })"
                v-model="filter.response_time"
            />

            <label class="text-[13px] text-dark font-krub-semibold mb-1 block">
                Created Date :
                <span class="text-red">*</span>
            </label>
            <div class="grid grid-cols-2 gap-4">
                <DatePicker
                    name="created_start"
                    label="Start date"
                    required
                    v-model="filter.created_start"
                    :value="filter.created_start"
                />
                <DatePicker
                    name="created_end"
                    label="End date"
                    required
                    v-model="filter.created_end"
                    :value="filter.created_end"
                    :min="filter.created_start"
                />
            </div>

            <label class="text-[13px] text-dark font-krub-semibold mb-1 block">
                Modified Date :
                <span class="text-red">*</span>
            </label>
            <div class="grid grid-cols-2 gap-4">
                <DatePicker
                    name="modify_start"
                    label="Start date"
                    required
                    v-model="filter.modify_start"
                    :value="filter.modify_start"
                />
                <DatePicker
                    name="modify_end"
                    label="End date"
                    required
                    v-model="filter.modify_end"
                    :value="filter.modify_end"
                    :min="filter.modify_start"
                />
            </div>
        </div>

        <div class="flex gap-3 justify-end mt-4">
            <ButtonOutlineGrey
                class="w-[100px] py-3"
                x-on:click="filter=false"
                id="cancel-filter"
            >
                Cancel
            </ButtonOutlineGrey>
            <ButtonYellow
                class="w-[100px] py-3"
                type="button"
                @click="filterData"
            >
                Submit
            </ButtonYellow>
        </div>
    </Filter>
</template>
<script setup lang="ts">
import Filter from "@/Components/Popup/Filter.vue";
import DatePicker from "@/Components/Input/DatePicker.vue";
import ButtonYellow from "@/Components/Button/ButtonYellow.vue";
import ButtonOutlineGrey from "@/Components/Button/ButtonOutlineGrey.vue";
import MultipleHelpdesk from "@/Components/Input/Select/MultipleHelpdesk.vue";
import MultipleSelect from "@/Components/Input/Select/MultipleSelect.vue";
import MultipleCampaign from "@/Components/Input/Select/MultipleCampaign.vue";
import Select from "@/Components/Input/Select.vue";
import { ref, watch } from "vue";
import {
    closeFilter,
    removeAllUrlParameter,
    routeAppendParam,
    showAlert,
    validateMinimumDateRange,
} from "@/Plugins/Function/global-function";

const props = defineProps([
    "helpdesk",
    "status",
    "reset",
    "type",
    "spv",
    "agent",
    "productList",
    "escalations"
]);
const itemAgent = ref(props.agent);
const filter = ref({
    created_start: "",
    created_end: "",
    modify_start: "",
    modify_end: "",
    broadcast_response: null,
    origins: [],
    categories: [],
    helpdesk_id: [],
    status: [],
    campaign_id: [],
    spv_id: [],
    agent_id: [],
    priority: [],
    division_sla: [],
    ticket_sla: [],
    product_name: [],
    response_time: [],
    escalation_id: [],
});

const broadcastResponse = ref(["Yes", "No"]);
const callOrigin = ref([
    "From Web",
    "From SIP",
    "From Whatsapp Bot",
    "From Web Bot",
    "From Email",
    "From Incoming SIP",
    "From Whatsapp",
    "From Kontakami",
    "From Walk-In",
]);
const productCategory = ref([
    "General",
    "Schedule Professional",
    "Schedule Other",
    "Other",
]);

const priorities = ref(["Critical", "High", "Medium", "Low"]);
const sla = ref(["Fulfilled", "Breached"]);
const responseTime = ref(["Fulfilled", "Breached"]);
const filterData = async () => {
    const param = filter.value;
    if (
        ((!param.created_start || !param.created_end) &&
            (!param.modify_start || !param.modify_end)) ||
        (param.created_end && param.modify_end)
    ) {
        showAlert("Please select created or modified date");
        return;
    }

    if (
        validateMinimumDateRange(param.created_start, param.created_end) &&
        validateMinimumDateRange(param.modify_start, param.modify_end)
    ) {
        var filterParam: any = {
            "filter[created_start]": param.created_start || "",
            "filter[created_end]": param.created_end || "",
            "filter[modify_start]": param.modify_start || "",
            "filter[modify_end]": param.modify_end || "",
        };
        param.origins.forEach((origin, index) => {
            filterParam[`filter[origins][${index}]`] = origin;
        });
        param.categories.forEach((category, index) => {
            filterParam[`filter[category][${index}]`] = category;
        });
        param.helpdesk_id.forEach((id, index) => {
            filterParam[`filter[helpdesk_id][${index}]`] = id;
        });
        param.campaign_id.forEach((id, index) => {
            filterParam[`filter[campaign_id][${index}]`] = id;
        });
        param.status.forEach((status, index) => {
            filterParam[`filter[status][${index}]`] = status;
        });
        param.agent_id.forEach((agent_id, index) => {
            filterParam[`filter[agent_id][${index}]`] = agent_id;
        });
        param.spv_id.forEach((spv_id, index) => {
            filterParam[`filter[spv_id][${index}]`] = spv_id;
        });
        param.priority.forEach((priority, index) => {
            filterParam[`filter[priority][${index}]`] = priority;
        });
        param.division_sla.forEach((division_sla, index) => {
            filterParam[`filter[division_sla][${index}]`] = division_sla;
        });
        param.ticket_sla.forEach((ticket_sla, index) => {
            filterParam[`filter[ticket_sla][${index}]`] = ticket_sla;
        });
        param.response_time.forEach((response_time, index) => {
            filterParam[`filter[response_time][${index}]`] = response_time;
        });
        param.product_name.forEach((product_name, index) => {
            filterParam[`filter[product_name][${index}]`] = product_name;
        });
        param.escalation_id.forEach((id, index) => {
            filterParam[`filter[escalation_id][${index}]`] = id;
        });
        if (param.broadcast_response) {
            filterParam[`filter[broadcast_response]`] =
                param.broadcast_response;
        }
        removeAllUrlParameter();
        routeAppendParam(filterParam, false);
        closeFilter();
    }
};

watch(
    () => filter.value.spv_id,
    (spvId, id) => {
        const spvIds: any = filter.value.spv_id;
        itemAgent.value = props.agent;
        if (spvIds.length) {
            const agent = props.agent.filter((row: any) =>
                spvIds.includes(row.spv_id)
            );
            itemAgent.value = agent;
        }
    }
);
</script>
