<template>
    <div class="h-full" x-data="{filter:false}">
        <div class="flex justify-between">
            <TableSearch />
            <div class="flex gap-2">
                <ButtonOutlineGrey
                    type="button"
                    class="flex items-center gap-2"
                    x-on:click="filter=true"
                >
                    <i class="isax icon-setting-4 text-[13px]"></i>
                    Filter
                </ButtonOutlineGrey>
                <ButtonOutlineGrey
                    type="button"
                    @click="downloadReport()"
                    :loading="exportLoading"
                    :disabled="exportLoading"
                    v-if="filtered && category != 'ticket-list'"
                    class="border-online border bg-white flex items-center gap-2 rounded-lg py-2 px-3 text-[12px] text-online h-fit font-krub-semibold"
                >
                    <i class="isax-b icon-document-download"></i>
                    Export
                </ButtonOutlineGrey>
                <div
                    v-if="filtered && category == 'ticket-list'"
                    x-data="{dropdownOpen:false}"
                >
                    <ButtonOutlineGrey
                        type="button"
                        :loading="exportLoading"
                        :disabled="exportLoading"
                        x-on:click="dropdownOpen=true"
                        x-ref="buttonDotDropdown"
                        class="border-online border bg-white flex items-center gap-2 rounded-lg py-2 px-3 text-[12px] text-online h-fit font-krub-semibold"
                    >
                        <i class="isax-b icon-document-download"></i>
                        Export Data
                    </ButtonOutlineGrey>
                    <Dropdown x-anchor.bottom-start="$refs.buttonDotDropdown">
                        <DropdownMenu @click="downloadReport()">
                            Ticket List
                        </DropdownMenu>
                        <DropdownMenu @click="downloadReportForm">
                            Ticket Form (Excel)
                        </DropdownMenu>
                        <DropdownMenu
                            @click="downloadReportFormPDF"
                            v-if="!queueLog"
                        >
                            Ticket Form (PDF)
                        </DropdownMenu>
                        <DropdownMenu @click="downloadReportTicketChat">
                            Ticket Call History
                        </DropdownMenu>
                    </Dropdown>
                </div>
            </div>
        </div>

        <div
            class="bg-[#FFF5DA] flex px-3 py-3 pb-2 rounded-md mb-2 mt-[-5px] border-yellow"
            v-if="queueLog"
        >
            <div class="me-3">
                <i class="isax-b icon-info-circle text-yellow text-[20px]"></i>
            </div>
            <div>
                <p
                    class="text-[12px] font-krub-semibold"
                    v-if="!queueLog.done_at"
                >
                    The Export Ticket Form is still in process, please wait a
                    moment.
                </p>
                <p
                    class="text-[12px] font-krub-semibold"
                    v-if="queueLog.done_at"
                >
                    The Export Ticket Form is completed, please
                    <a
                        :href="
                            route(
                                'report.inbound.ticket.export-form.download',
                                queueLog.id
                            )
                        "
                        class="underline text-blue"
                    >
                        click here
                    </a>
                    to download the file
                </p>
            </div>
        </div>

        <TicketList
            :type="type"
            :filter="filter"
            v-if="category === 'ticket-list'"
        />
        <CallTracking
            :type="type"
            :filter="filter"
            v-if="category === 'call-tracking'"
        />
        <AgentActivity
            :type="type"
            :filter="filter"
            v-if="category === 'agent-activity'"
        />
        <CallAgent
            :type="type"
            :filter="filter"
            v-if="category === 'call-agent'"
        />
    </div>
</template>
<script setup lang="ts">
import TableSearch from "@/Components/Table/TableSearch.vue";
import ButtonOutlineGrey from "@/Components/Button/ButtonOutlineGrey.vue";
import DropdownMenu from "@/Components/Dropdown/DropdownMenu.vue";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import TicketList from "./Data/TicketList.vue";
import CallTracking from "./Data/CallTracking.vue";
import AgentActivity from "./Data/AgentActivity.vue";
import CallAgent from "./Data/CallAgent.vue";
import { ref, onBeforeUnmount, onMounted } from "vue";
import {
    getAllQueryParameter,
    getQueryParam,
    showAlert,
} from "@/Plugins/Function/global-function";
import axios from "axios";

const props = defineProps([
    "category",
    "queueLog",
    "type",
    "filter",
    "export_url",
]);
const hasStartOrEnd = ref(
    getQueryParam("filter[created_start]") ||
        getQueryParam("filter[created_end]")
);
const filtered = ref(hasStartOrEnd.value ? true : false);
const allParam = ref([]);
const exportLoading = ref(false);

const downloadReport = (exportUrl?: string) => {
    exportLoading.value = true;
    try {
        var formData = new FormData();
        for (var key in allParam.value) {
            formData.append(key, allParam.value[key]);
        }
        axios({
            method: "post",
            url:
                exportUrl ||
                route(`report.${props.type}.export`, props.category),
            data: formData,
            responseType: "blob",
        })
            .then((result) => {
                if (result.headers.filename) {
                    const blob = new Blob([result.data], {
                        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    });
                    const blobURL = URL.createObjectURL(blob);
                    const anchor = document.createElement("a");
                    anchor.href = blobURL;
                    anchor.download = result.headers.filename;
                    anchor.click();
                    URL.revokeObjectURL(blobURL);
                } else {
                    showAlert("Failed to export report");
                }
                exportLoading.value = false;
            })
            .catch((error) => {
                exportLoading.value = false;
                showAlert("Failed to export report");
            });
    } catch (err) {
        console.log(err);
        exportLoading.value = false;
        showAlert("Failed to export report");
    }
};

const downloadReportForm = () => {
    const downloadLink =
        props.type == "inbound"
            ? route("report.inbound.ticket.export-form")
            : route("report.outbound.ticket.export-form");
    downloadReport(downloadLink);
};

const downloadReportTicketChat = () => {
    const downloadLink =
        props.type == "inbound"
            ? route("report.inbound.ticket.export-chat")
            : route("report.outbound.ticket.export-chat");
    downloadReport(downloadLink);
};

const downloadReportFormPDF = () => {
    exportLoading.value = true;
    const downloadLink =
        props.type == "inbound"
            ? route("report.inbound.ticket.export-form")
            : route("report.outbound.ticket.export-form");
    try {
        var formData = new FormData();
        for (var key in allParam.value) {
            formData.append(key, allParam.value[key]);
        }
        formData.append("type", "pdf");
        axios({
            method: "post",
            url: downloadLink,
            data: formData,
        })
            .then((result) => {
                setTimeout(() => {
                    window.location.reload();
                    exportLoading.value = false;
                }, 2000);
            })
            .catch((error) => {
                exportLoading.value = false;
                showAlert("Failed to export report");
            });
    } catch (err) {
        console.log(err);
        exportLoading.value = false;
        showAlert("Failed to export report");
    }
};

const getAllParam = () => {
    allParam.value = getAllQueryParameter();
    filtered.value = Object.keys(allParam.value).length ? true : false;
};
window.addEventListener("changeUrlParameter", getAllParam);
onMounted(() => {
    getAllParam();
});
onBeforeUnmount(() => {
    window.removeEventListener("changeUrlParameter", getAllParam);
});
</script>
