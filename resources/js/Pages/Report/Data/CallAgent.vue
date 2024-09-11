<template>
    <div class="h-full">
        <FilterDefault
            :spv="filter.spv"
            :agent="filter.agent"
            :type="type"
            :reset="route(`report.${type}.index`, 'call-agent')"
        />
        <AlertRequiredFilter v-if="!filtered" />
        <Table v-else :columns="columns" :paginate="paginate">
            <tr v-for="row in paginate.data.value">
                <Td class="whitespace-nowrap">{{ row.date }}</Td>
                <Td class="whitespace-nowrap">
                    {{ row.agent_code }}
                    -
                    {{ row.agent_name }}
                </Td>
                <Td class="whitespace-nowrap">{{ row.spv_name }}</Td>
                <!-- <Td>
                    {{ row.incoming || 0 }}
                </Td>
                <Td>
                    {{ row.outgoing || 0 }}
                </Td>
                <Td>
                    {{ row.missed || 0 }}
                </Td>
                <Td>
                    {{ row.callback || 0 }}
                </Td>
                <Td>0</Td> -->
                <Td class="whitespace-nowrap">{{ row.talktime }}</Td>
                <Td class="whitespace-nowrap">{{ row.avg_talktime }}</Td>
            </tr>
        </Table>
    </div>
</template>
<script setup lang="ts">
import AlertRequiredFilter from "../AlertRequiredFilter.vue";
import FilterDefault from "../Filter/FilterDefault.vue";
import Table from "@/Components/Table/Table.vue";
import Td from "@/Components/Table/Td.vue";
import { getQueryParam } from "@/Plugins/Function/global-function";
import { usePaginate } from "@/Plugins/Hooks/usePaginate";
import { ref, onBeforeUnmount } from "vue";

const props = defineProps(["filter", "type"]);
const columns = ref([
    "Date",
    "Agent",
    "SPV",
    // "Incoming Call",
    // "Outgoing Call",
    // "Missed Call",
    // "Callback",
    // "Outgoing Campaign",
    "Talk Time",
    "Avg Talktime Per Call",
]);
const hasStartOrEnd = ref(
    getQueryParam("filter[created_start]") ||
        getQueryParam("filter[created_end]")
);
const filtered = ref(hasStartOrEnd.value ? true : false);

const paginate = usePaginate({
    route: route(`report.${props.type}.data-table`, "call-agent"),
});

const handleFilter = () => {
    filtered.value = true;
};
window.addEventListener("changeUrlParameter", handleFilter);

onBeforeUnmount(() => {
    window.removeEventListener("changeUrlParameter", handleFilter);
});
</script>
