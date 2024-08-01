<template>
    <div class="flex gap-2 mb-2">
        <Link
            v-for="menu in menus"
            class="bg-white border px-3 py-1 text-[12px] rounded-full hover:bg-yellow hover:border-yellow hover:text-white"
            v-bind:class="{
                'bg-yellow border-yellow text-white': active === menu.id,
            }"
            :href="route('dashboard.inbound.index', menu.id)"
        >
            {{ menu.name }}
        </Link>
    </div>
</template>
<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
defineProps(["active"]);

const menus = ref([
    {
        id: "live-daily",
        name: "Live Daily",
    },
    {
        id: "kpi",
        name: "KPI",
    },
]);
</script>
