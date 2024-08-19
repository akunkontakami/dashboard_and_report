<template>
    <div class="flex gap-2 mb-3 flex-wrap">
        <span v-for="menu in menus" class="whitespace-nowrap">
            <Link
                class="border px-3 py-1 text-[12px] rounded-full bg-white hover:bg-yellow hover:border-yellow hover:text-white"
                v-bind:class="{
                    'bg-yellow border-yellow text-white': category === menu.id,
                }"
                :href="route(`report.${type}.index`, menu.id)"
                v-if="menu.show && items.includes(menu.name)"
            >
                {{ menu.name }}
            </Link>
        </span>
    </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
const props = defineProps(["category", "type", "can_view", "items"]);

const menus = ref([
    {
        name: "Ticket List",
        id: "ticket-list",
        show: true,
    },
    {
        name: "Call Tracking",
        id: "call-tracking",
        show: props.can_view,
    },
    {
        name: "Agent Activity",
        id: "agent-activity",
        show: props.can_view,
    },
    {
        name: "Call Agent",
        id: "call-agent",
        show: props.can_view,
    },
]);
</script>
