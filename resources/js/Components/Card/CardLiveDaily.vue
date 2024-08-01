<template>
    <div class="bg-white rounded-xl border">
        <h1 class="border-b text-center py-[7px] text-[12px] font-krub-bold">
            {{ title }}
        </h1>
        <h1 class="border-b text-center py-2 text-3xl font-krub-bold">
            {{ value }}
        </h1>
        <div class="flex flex-col justify-center items-center py-2">
            <p class="text-[11px] text-[#404040] font-krub-semibold">
                to yesterday
            </p>
            <strong class="text-[#F94144] text-[13px] flex items-center justify-center gap-1" v-if="yesterday < 0">
                <i class="isax-b icon-arrow-down"></i>
                {{ yesterday }}
            </strong>
            <strong class="text-[#76B958] text-[13px] flex items-center justify-center gap-1" v-else-if="yesterday > 0">
                <i class="isax-b icon-arrow-up-1"></i>
                {{ yesterday }}
            </strong>
            <strong v-else> 0 </strong>
        </div>
    </div>
</template>
<script setup lang="ts">
defineProps(["title", "value", "yesterday"]);
</script>
