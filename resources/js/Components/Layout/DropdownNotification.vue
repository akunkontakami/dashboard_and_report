<template>
    <button
        type="button"
        class="mt-[6px] rounded-full px-2 py-1 pb-0 relative"
        x-ref="dropdownNotification"
        x-on:click="dropdownNotification=true"
    >
        <i class="isax-b icon-notification text-[20px]"></i>
        <span
            class="bg-red border-2 border-white text-white text-[8px] absolute right-0 top-0 px-[5px] py-[1px] flex items-center justify-center rounded-full"
            v-if="notification.items.length"
        >
            {{ notification.total }}
        </span>
    </button>
    <div
        x-show="dropdownNotification"
        x-clock
        x-on:click.away="dropdownNotification=false"
        x-anchor.bottom-end="$refs.dropdownNotification"
        class="absolute z-50 mt-1 w-fit whitespace-nowrap"
    >
        <div
            class="py-2 px-3 bg-white border rounded-md shadow-sm border-neutral-200/70 min-w-72 text-neutral-700"
        >
            <div class="border-b py-2 text-[12px] font-krub-semibold">
                <p>Notification</p>
            </div>
            <ul
                class="overflow-auto max-h-[300px] -mx-3 px-3 flex flex-col gap-2 py-3"
            >
                <li
                    class="flex flex-col items-center justify-center"
                    v-if="!notification.items.length"
                >
                    <EmptyState class="h-[100px] w-[100px]" />
                    <span class="inline-block mt-4 text-[13px]">
                        No Results Found
                    </span>
                </li>
                <li v-for="notif in notification.items">
                    <div class="flex gap-2 items-center">
                        <img
                            :src="notif.logo"
                            :alt="notif.name"
                            class="h-[40px] w-[40px] rounded-full object-cover border"
                        />
                        <div class="flex flex-col">
                            <p
                                class="text-[#333030] font-krub-bold text-[13px] mb-0"
                            >
                                {{ notif.name }}
                            </p>
                            <span
                                class="text-[#888888] text-[12px] font-krub-regular"
                            >
                                invitation to
                                <strong class="text-dark font-krub-bold">
                                    Kontakami
                                </strong>
                                self service help desk
                            </span>
                        </div>
                    </div>
                    <div class="ms-[50px] mt-1 mb-2">
                        <Link
                            :href="
                                route('company.add-kontak', {
                                    id: notif.id,
                                    type: 'add',
                                })
                            "
                            class="text-[10px] text-white bg-yellow rounded-md px-3 py-[6px] w-fit"
                        >
                            View Detail
                        </Link>
                    </div>
                </li>
            </ul>
            <div class="border-t py-2 text-[12px] font-krub-semibold mt-2" v-if="notification.items.length">
                <Link
                    :href="route('notification.index')"
                    class="text-yellow underline"
                >
                    View All Notification
                </Link>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import EmptyState from "../Icon/Etc/EmptyState.vue";
import { onMounted, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

const notification: any = ref({
    total: 0,
    items: [],
});
const fetchNotification = () => {
    axios.get(route("notification.top-notif")).then((result) => {
        const data = result.data;
        notification.value = {
            total: data.total,
            items: data.list,
        };
    });
};

onMounted(() => {
    fetchNotification();
});
</script>
