<template>
    <div class="bg-white border rounded-xl flex flex-col justify-center items-center py-2 px-4">
          <h1
               v-bind:class="textClass"
               class="text-3xl font-krub-bold"
          >
               {{ value }}
          </h1>
          <p class="text-[13px] font-krub-semibold">
               {{ label }}
          </p>
    </div>
</template>
<script setup lang="ts">
defineProps(['value',"label","textClass"])
</script>