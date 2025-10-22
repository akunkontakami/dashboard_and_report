<template>
    <span v-for="menu in menus" class="block mb-2">
        <Link
            v-bind:class="
                type === menu.key
                    ? 'text-yellow border-b-[3px] border-yellow'
                    : ''
            "
            :href="menu.url"
            class="pb-2 hover:border-b-[3px] hover:text-yellow hover:border-yellow"
            v-if="menu.show"
        >
            {{ menu.name }}
        </Link>
    </span>
</template>

<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";
defineProps(['type'])

const menus = [
    {
        key: "inbound",
        name: "Inbound",
        url: route("report.inbound.index", "ticket-list"),
        show: true,
    },
    {
        key: "outbound",
        name: "Outbound",
        url: route("report.outbound.index", "ticket-list"),
        show: true,
    },
];
</script>
