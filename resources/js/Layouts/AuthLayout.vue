<template>
     <main
         class="bg-auth h-full flex justify-end md:py-6 md:px-6 py-2 px-2 min-h-screen"
     >
         <Head :title="title" />
         <section
             class="bg-white rounded-xl lg:max-w-[40%] md:max-w-[55%] w-full md:w-[80%] py-7 flex items-center justify-center"
         >
             <slot />
         </section>
     </main>
 </template>
 <script setup lang="ts">
 import { Head } from "@inertiajs/vue3";
 
 defineProps(["title"]);
 
 </script>
 