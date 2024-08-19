<template>
    <AppLayout header="Report" title="Report">
        <template v-slot:tab>
            <TabType :type="type" />
        </template>
        <div class="h-full">
            <TabCategory
                :type="type"
                :category="category"
                :items="billing.report_items"
                :can_view="billing.can_view"
            />
            <DataTable
                :queueLog="queueLog"
                :category="category"
                :type="type"
                :filter="filter"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import TabType from "./TabType.vue";
import TabCategory from "./TabCategory.vue";
import DataTable from "./DataTable.vue";

defineProps(["type", "category", "billing", "queueLog", "filter"]);
</script>
