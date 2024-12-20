<template>
    <div class="h-full" x-data="{popupDescription:false}">
        <FilterTicket
            :helpdesk="filter.helpdesk"
            :status="filter.status"
            :spv="filter.spv"
            :agent="filter.agent"
            :productList="filter.productList"
            :escalations="filter.escalations"
            :type="type"
            :reset="route(`report.${type}.index`, 'ticket-list')"
        />
        <AlertRequiredFilter v-if="!filtered" />
        <Table v-else :columns="columns" :paginate="paginate">
            <tr v-for="row in paginate.data.value">
                <Td class="whitespace-nowrap">
                    {{ row.date }}
                </Td>
                <Td class="whitespace-nowrap">
                    {{ row.updated_at }}
                </Td>
                <Td class="whitespace-nowrap">
                    {{ row.call_origin }}
                </Td>
                <Td class="whitespace-nowrap">
                    {{ row.ticket_number }}
                </Td>
                <Td class="whitespace-nowrap">
                    {{ row.customer_name }}
                </Td>
                <Td class="whitespace-nowrap">
                    {{ row.product_category || "General" }}
                </Td>
                <Td class="whitespace-nowrap">
                    <span v-if="type === 'inbound'">
                        {{ row.helpdesk?.name || row.helpdesk_name }}
                    </span>
                    <span v-if="type === 'outbound'">
                        {{ row.campaign?.name }}
                    </span>
                </Td>
                <Td class="whitespace-nowrap">
                    {{ row.product?.name || row.product_name || "-" }}
                </Td>
                <Td class="whitespace-nowrap">
                    <a
                        href="javascript:;"
                        class="text-[#F6A500] font-krub-semibold text-[12px] underline relative z-10"
                        x-on:click="popupDescription=true"
                        @click.stop="showSubject(row.subject?.name || '-')"
                    >
                        View Subject
                    </a>
                </Td>
                <Td class="whitespace-nowrap">
                    <div class="flex items-center gap-1" v-if="row.priority">
                        <span
                            class="w-[8px] h-[8px] rounded-full block"
                            v-bind:class="row.priority_color"
                        ></span>
                        {{ row.priority || "-" }}
                    </div>
                </Td>
                <Td class="whitespace-nowrap">
                    {{ row.escalation_team?.name || "No Division" }}
                </Td>
                <Td class="whitespace-nowrap py-[3px]">
                    <CardSla
                        :resolution="row.sla_resolution_time"
                        :response="row.sla_response_time"
                    />
                </Td>
                <Td class="whitespace-nowrap py-[3px]">
                    <DivisionSla :time="row.sla_division" />
                </Td>
                <Td class="whitespace-nowrap">
                    <div class="flex items-center gap-1" v-if="row.status">
                        <span
                            class="w-[8px] h-[8px] rounded-full block"
                            v-bind:class="row.status_color"
                        ></span>
                        {{ row.status || "-" }}
                    </div>
                </Td>
                <Td class="whitespace-nowrap" v-if="type == 'outbound'">
                    {{ row.is_broadcasted }}
                </Td>
                <Td class="whitespace-nowrap">
                    <span v-if="row.agent">
                        {{ row.agent?.name || "-" }}
                    </span>
                    <span v-else>-</span>
                </Td>
                <Td class="whitespace-nowrap">
                    {{ row.spv?.name || "-" }}
                </Td>
            </tr>
        </Table>

        <span class="bg-blue"></span>
        <PopupDescription title="Subject" :description="descriptionSubject" />
    </div>
</template>
<script setup lang="ts">
import AlertRequiredFilter from "../AlertRequiredFilter.vue";
import FilterTicket from "../Filter/FilterTicket.vue";
import Table from "@/Components/Table/Table.vue";
import Td from "@/Components/Table/Td.vue";
import PopupDescription from "@/Components/Popup/PopupDescription.vue";
import CardSla from "@/Components/Card/Ticket/CardSla.vue";
import DivisionSla from "@/Components/Card/Ticket/DivisionSla.vue";
import { getQueryParam } from "@/Plugins/Function/global-function";
import { usePaginate } from "@/Plugins/Hooks/usePaginate";
import { ref, onBeforeUnmount, onMounted } from "vue";

const props = defineProps(["type", "filter"]);
const hasStartOrEnd = ref(
    getQueryParam("filter[created_start]") ||
        getQueryParam("filter[created_end]")
);
const filtered = ref(hasStartOrEnd.value ? true : false);
const columns: any = ref([]);
const descriptionSubject = ref("");
const paginate = usePaginate({
    route: route(`report.${props.type}.data-table`, "ticket-list"),
});

const setColumns = () => {
    var cols = [
        "Created Date",
        "Modified Date",
        "Call Origin",
        "Ticket Number",
        "Customer Name",
        "Product Category",
        props.type === "inbound" ? "Helpdesk Category" : "Marketing Campaign",
        "Product Name",
        "Subject",
        "Priority",
        "Transfer To",
        "Ticket SLA",
        "Division SLA",
        "Status",
    ];
    if (props.type == "outbound") {
        cols.push("Broadcast Response");
    }
    cols.push("Agent");
    cols.push("SPV");
    columns.value = cols;
};

const handleFilter = () => {
    filtered.value = true;
};

const showSubject = (subject: string) => {
    descriptionSubject.value = subject;
};
onBeforeUnmount(() => {
    window.removeEventListener("changeUrlParameter", handleFilter);
});
onMounted(() => {
    window.addEventListener("changeUrlParameter", handleFilter);
    setColumns();
});
</script>
