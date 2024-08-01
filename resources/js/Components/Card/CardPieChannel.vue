<template>
    <div class="bg-white border rounded-xl">
        <h1 class="border-b py-2 text-[13px] font-krub-bold text-center">
            {{ label }}
        </h1>
        <div class="flex px-2 py-4 justify-center items-center">
            <div class="w-[60%] flex justify-center items-center">
                <slot />
            </div>
            <div class="w-[40%] flex-1 ps-2">
                <ul class="flex flex-col gap-1 items-start justify-start">
                    <li
                        class="text-[9px] flex gap-1 items-center justify-center"
                        v-for="badge in badges"
                    >
                        <span
                            class="block w-[10px] h-[10px] rounded-sm"
                            v-bind:class="badge[0]"
                        ></span>
                        <span>{{ badge[1] }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
defineProps(["label", "badges"]);
</script>
