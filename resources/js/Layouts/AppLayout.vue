<template>
    <Head :title="title" />
    <Sidebar :menus="menus" />
    <FlashAlert />
    <section class="app-wrapper md:ps-sidebar flex flex-col h-full">
        <Header :header="header" :headerBackUrl="headerBackUrl">
            <template v-slot:tab>
                <slot name="tab"></slot>
            </template>
        </Header>
        <main class="flex-1 px-4 py-3">
            <slot />
            <div class="md:h-0 h-[65px]"></div>
        </main>
        <BottomNavigation :menus="menus" />
    </section>
</template>
<script setup lang="ts">
import Sidebar from "@/Components/Layout/Sidebar.vue";
import FlashAlert from "@/Components/Layout/FlashAlert.vue";
import BottomNavigation from "@/Components/Layout/BottomNavigation.vue";
import Header from "@/Components/Layout/Header.vue";
import IconMenuReport from "@/Components/Icon/Menu/IconMenuReport.vue";
import IconMenuDashboard from "@/Components/Icon/Menu/IconMenuDashboard.vue";
import { showAlert } from "@/Plugins/Function/global-function";
import { ref, shallowRef, watch, onMounted } from "vue";
import { Head, usePage } from "@inertiajs/vue3";

defineProps(["title", "header", "headerBackUrl"]);
const menus = ref([
    {
        name: "Dashboard",
        route: route("dashboard.inbound.index","live-daily"),
        active: "dashboard.*",
        icon: shallowRef(IconMenuDashboard),
        enable: true,
        show: true,
    },
    {
        name: "Report",
        route: "",
        active: "contact.*",
        icon: shallowRef(IconMenuReport),
        enable: true,
        show: true,
    },
]);

const page = usePage();

const watchAlert = () => {
    const alert = page.props.flash;
    if (alert?.success) {
        showAlert(alert.success, "success");
    }
    if (alert?.error) {
        showAlert(alert.error);
    }
};
watch(
    () => page.props.flash,
    (newValue, oldValue) => {
        watchAlert();
    }
);

onMounted(() => {
    watchAlert();
});
</script>
