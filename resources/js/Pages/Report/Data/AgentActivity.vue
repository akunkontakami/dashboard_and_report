<template>
    <div class="h-full">
        <FilterDefault
            :spv="filter.spv"
            :agent="filter.agent"
            :type="type"
            :reset="route(`report.${type}.index`, 'agent-activity')"
        />
        <AlertRequiredFilter v-if="!filtered" />
        <Table v-else :columns="columns" :paginate="paginate">
            <tr v-for="row in paginate.data.value">
                <Td class="whitespace-nowrap">{{ row.date }}</Td>
                <Td class="whitespace-nowrap">{{ row.name }}</Td>
                <Td class="whitespace-nowrap">{{ row.role }}</Td>
                <Td class="whitespace-nowrap">{{ row.login_time }}</Td>
                <Td class="whitespace-nowrap">{{ row.available_time }}</Td>
                <Td class="whitespace-nowrap">{{ row.talktime }}</Td>
                <Td class="whitespace-nowrap">{{ row.offline }}</Td>
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
    "Name",
    "Role",
    "Login Time",
    "Available Time",
    "Talk Time",
    "Offline",
]);
const hasStartOrEnd = ref(
    getQueryParam("filter[created_start]") ||
        getQueryParam("filter[created_end]")
);
const filtered = ref(hasStartOrEnd.value ? true : false);

const paginate = usePaginate({
    route: route(`report.${props.type}.data-table`, "agent-activity"),
});

const handleFilter = () => {
    filtered.value = true;
};
window.addEventListener("changeUrlParameter", handleFilter);

onBeforeUnmount(() => {
    window.removeEventListener("changeUrlParameter", handleFilter);
});
</script>
