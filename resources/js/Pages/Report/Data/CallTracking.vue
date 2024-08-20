<template>
    <div class="h-full" x-data="{popupDescription:false}">
        <FilterCallTracking
            :spv="filter.spv"
            :agent="filter.agent"
            :type="type"
            :reset="route(`report.${type}.index`, 'call-tracking')"
        />
        <AlertRequiredFilter v-if="!filtered" />
        <Table v-else :columns="columns" :paginate="paginate">
            <tr v-for="row in paginate.data.value">
                <Td class="whitespace-nowrap">
                    {{ row.agent_code }}
                    -
                    {{ row.agent_name }}
                </Td>
                <Td class="whitespace-nowrap">{{ row.spv_name }}</Td>
                <Td class="whitespace-nowrap">{{ row.total_customer }}</Td>
                <Td class="whitespace-nowrap">{{ row.total_ticket }}</Td>
                <Td class="whitespace-nowrap" v-for="status in filter.status">
                    {{ row.ticket_status[status.slug] || 0 }}
                </Td>
            </tr>
        </Table>
    </div>
</template>
<script setup lang="ts">
import AlertRequiredFilter from "../AlertRequiredFilter.vue";
import FilterCallTracking from "../Filter/FilterCallTracking.vue";
import Table from "@/Components/Table/Table.vue";
import Td from "@/Components/Table/Td.vue";
import { getQueryParam } from "@/Plugins/Function/global-function";
import { usePaginate } from "@/Plugins/Hooks/usePaginate";
import { ref, onBeforeUnmount, onMounted } from "vue";

const props = defineProps(["filter", "type"]);
const columns = ref(["Agent", "SPV", "Customer Count", "Ticket Count"]);
const hasStartOrEnd = ref(
    getQueryParam("filter[created_start]") ||
        getQueryParam("filter[created_end]")
);
const filtered = ref(hasStartOrEnd.value ? true : false);

const paginate = usePaginate({
    route: route(`report.${props.type}.data-table`, "call-tracking"),
});

const buildColumns = () => {
    var columnList = columns.value;
    for (var row of props.filter.status) {
        columnList.push(row.label);
    }
    columns.value = columnList;
};

const handleFilter = () => {
    filtered.value = true;
};
window.addEventListener("changeUrlParameter", handleFilter);

onMounted(() => {
    buildColumns();
});
onBeforeUnmount(() => {
    window.removeEventListener("changeUrlParameter", handleFilter);
});
</script>
