<template>
     <Filter class="md:max-w-lg" :reset="reset">
         <MultipleSelect
             label="SPV"
             placeholder="Choose SPV"
             :items="spv"
             v-model="filter.spv_id"
         />
         <MultipleSelect
             label="Agent"
             placeholder="Choose Agent"
             :items="itemAgent"
             v-model="filter.agent_id"
         />
         <label class="text-[13px] text-dark font-krub-semibold mb-1 block mt-3">
             Date :
             <span class="text-red">*</span>
         </label>
         <div class="grid grid-cols-2 gap-4">
             <DatePicker
                 name="created_start"
                 label="Start date"
                 required
                 v-model="filter.created_start"
                 :value="filter.created_start"
             />
             <DatePicker
                 name="created_end"
                 label="End date"
                 required
                 v-model="filter.created_end"
                 :value="filter.created_end"
                 :min="filter.created_start"
             />
         </div>
         <div class="flex gap-3 justify-end mt-5">
             <ButtonOutlineGrey
                 class="w-[100px] py-3"
                 x-on:click="filter=false"
                 id="cancel-filter"
             >
                 Cancel
             </ButtonOutlineGrey>
             <ButtonYellow
                 class="w-[100px] py-3"
                 type="button"
                 @click="filterData"
             >
                 Submit
             </ButtonYellow>
         </div>
     </Filter>
 </template>
 <script setup lang="ts">
 import Filter from "@/Components/Popup/Filter.vue";
 import DatePicker from "@/Components/Input/DatePicker.vue";
 import ButtonYellow from "@/Components/Button/ButtonYellow.vue";
 import ButtonOutlineGrey from "@/Components/Button/ButtonOutlineGrey.vue";
 import MultipleSelect from "@/Components/Input/Select/MultipleSelect.vue";
 import { ref, watch } from "vue";
import { closeFilter, removeAllUrlParameter, routeAppendParam, showAlert, validateMinimumDateRange } from "@/Plugins/Function/global-function";

 
 const props = defineProps(["spv", "agent", "status", "reset"]);
 const filter = ref({
     created_start: "",
     created_end: "",
     spv_id: [],
     agent_id: [],
 });
 
 const itemAgent = ref(props.agent);
 const filterData = async () => {
     const param = filter.value;
     if (!param.created_start || !param.created_end) {
         showAlert("Please select date");
         return;
     }
     if(param.spv_id.length && !param.agent_id.length){
         showAlert("Please select Agent");
         return;
     }
     if (validateMinimumDateRange(param.created_start, param.created_end)) {
         var filterParam: any = {
             "filter[created_start]": param.created_start || "",
             "filter[created_end]": param.created_end || "",
         };
 
         param.spv_id.forEach((spv_id, index) => {
             filterParam[`filter[spv_id][${index}]`] = spv_id;
         });
 
         param.agent_id.forEach((agent_id, index) => {
             filterParam[`filter[agent_id][${index}]`] = agent_id;
         });
         removeAllUrlParameter();
         routeAppendParam(filterParam, false);
         closeFilter();
     }
 };
 watch(
     () => filter.value.spv_id,
     (spvId, id) => {
         const spvIds: any = filter.value.spv_id;
         itemAgent.value = props.agent;
         if (spvIds.length) {
             const agent = props.agent.filter((row: any) =>
                 spvIds.includes(row.spv_id)
             );
             itemAgent.value = agent;
         }
     }
 );
 </script>
 