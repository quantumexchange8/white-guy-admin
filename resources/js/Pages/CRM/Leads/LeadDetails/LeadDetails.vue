<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm } from "@inertiajs/vue3";
import { h, ref, watch, watchEffect } from "vue";
import { IconChevronRight } from '@tabler/icons-vue';
import Button from '@/Components/Button.vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import { wTrans } from "laravel-vue-i18n";
import LeadDetailInfo from '@/Pages/CRM/Leads/LeadDetails/Partials/LeadDetailInfo.vue';
import LeadFrontInfo from '@/Pages/CRM/Leads/LeadDetails/Partials/LeadFrontInfo.vue';
import LeadNotes from '@/Pages/CRM/Leads/LeadDetails/Partials/LeadNotes.vue';
import LeadActionHistory from '@/Pages/CRM/Leads/LeadDetails/Partials/LeadActionHistory.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    lead: Object,
})

const leadDetail = ref();
const leadFront = ref();
const isLoading = ref(false); 

const getLeadData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/crm/lead/getLeadData?id=` + props.lead.id);

        leadDetail.value = response.data.leadDetail;
        leadFront.value = response.data.leadFront;
    } catch (error) {
        console.error('Error get network:', error);
    } finally {
        isLoading.value = false; // Set loading state to false after data fetch (success or failure)
    }
};

getLeadData();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getUserData();
    }
});

const tabs = ref([
    {   
        title: wTrans('lead_notes'),
        type: 'lead_notes',
        component: h(LeadNotes),
    },
    {   
        title: wTrans('lead_history'),
        type: 'lead_history',
        component: h(LeadActionHistory),
    },
]);

const selectedType = ref('lead_notes');
const activeIndex = ref(tabs.value.findIndex(tab => tab.type === selectedType.value));

// Sync selectedType to activeIndex
watch(selectedType, (newType) => {
    // console.log('Selected type changed:', newType);
    const index = tabs.value.findIndex(tab => tab.type === newType);
    // console.log('Found index:', index);
    if (index >= 0) {
        activeIndex.value = index;
    }
});

// Sync activeIndex to selectedType
watch(activeIndex, (newIndex) => {
    if (newIndex >= 0 && newIndex < tabs.value.length) {
        selectedType.value = tabs.value[newIndex].type;
        // console.log('Active index changed:', newIndex, 'Updated selectedType:', selectedType.value);
    }
});

</script>

<template>
    <AuthenticatedLayout :title="$t('public.lead_details')">
        <div class="w-full flex flex-col items-center gap-5">
            <!-- Breadcrumb -->
            <div class="flex flex-wrap md:flex-nowrap items-center gap-2 self-stretch text-gray-700 dark:text-gray-100">
                <Button
                    external
                    type="button"
                    variant="primary-text"
                    size="sm"
                    :href="route('crm.lead.index')"
                >
                    {{ $t('public.lead_listing') }}
                </Button>
                <IconChevronRight
                    :size="16"
                    stroke-width="1.25"
                />
                <div class="flex px-4 py-2 items-center justify-center rounded text-sm font-medium">{{ `${props.lead.last_name}&nbsp;${props.lead.first_name}` }} - {{ $t('public.view_details') }}</div>
            </div>

            <div class="w-full h-full grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="w-full grid col-span-1 md:col-span-2 gap-5">
                    <LeadDetailInfo
                        :leadDetail="leadDetail"
                        :isLoading="isLoading"
                    />
                    <LeadFrontInfo
                        :leadFront="leadFront"
                        :isLoading="isLoading"
                    />
                </div>
                <div class="w-full grid col-span-1">
                    <div class="w-full flex flex-col items-center p-3 gap-3 self-stretch rounded-lg bg-white dark:bg-gray-800 shadow-card md:px-6 md:py-5">
                        <Tabs v-model:value="activeIndex" class="w-full" >
                            <TabList>
                                <Tab 
                                    v-for="(tab, index) in tabs" 
                                    :key="tab.title"
                                    :value="index"
                                >
                                    {{ tab.title }}
                                </Tab>
                            </TabList>

                            <TabPanels class="py-2">
                                <TabPanel :key="activeIndex" :value="activeIndex">
                                    <component 
                                        :is="tabs[activeIndex].component" 
                                        :key="tabs[activeIndex].type" 
                                        :lead_id="props.lead.id" 
                                        :isLoading="isLoading" 
                                        v-if="tabs[activeIndex].component"
                                    />
                                </TabPanel>
                            </TabPanels>
                        </Tabs>

                        <!-- <LeadNotes
                            :lead_id="props.lead.id"
                            :isLoading="isLoading"
                        /> -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>