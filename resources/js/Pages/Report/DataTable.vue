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
import { ref } from "vue";

const props = defineProps(["category", "queueLog", "type", "filter"]);
const filtered = ref(false);
const allParam = ref([]);
const exportLoading = ref(false);

const downloadReport = () => {};

const downloadReportForm = () => {};

const downloadReportFormPDF = () => {};

const downloadReportTicketChat = () => {};
</script>
