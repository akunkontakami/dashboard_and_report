<template>
    <div class="bg-white border rounded-xl">
        <h1 class="border-b py-2 text-[13px] font-krub-bold text-center">
            {{ label }}
        </h1>
        <div class="flex px-2 py-4 justify-center items-center">
            <div class="w-[60%] flex justify-center items-center">
                <span
                    class="text-[12px] mt-3 block"
                    v-if="loading"
                >
                    Loading ...
                </span>
                <div
                    v-else-if="
                        !loading &&
                        isEmpty
                    "
                    class="flex flex-col justify-center items-center"
                >
                    <EmptyState class="w-[100px] h-[100px]" />
                    <span class="text-[11px] mt-3 block"> No Data Found </span>
                </div>
                <div v-else>
                    <slot />
                </div>
            </div>
            <div class="w-[40%] flex-1 ps-2">
                <ul class="flex flex-col gap-1 items-start justify-start">
                    <li
                        class="text-[9px] flex gap-1 items-center justify-center"
                        v-for="badge in badges"
                    >
                        <span
                            class="block w-[10px] h-[10px] rounded-sm"
                            :style="`background-color:${badge[0]}`"
                        ></span>
                        <span>{{ badge[1] }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import EmptyState from "@/Components/Icon/Etc/EmptyState.vue";
defineProps(["label", "badges", "loading", "isEmpty"]);
</script>
