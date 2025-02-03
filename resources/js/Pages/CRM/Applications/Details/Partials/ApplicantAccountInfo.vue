<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { h, ref, watch, watchEffect } from "vue";
import { IconChevronRight } from '@tabler/icons-vue';
import Button from '@/Components/Button.vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import { trans, wTrans } from "laravel-vue-i18n";
import BasicInfoTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/BasicInfoTab.vue";
import PrimaryApplicantInfoTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/PrimaryApplicantInfoTab.vue";
import SecondaryApplicantInfoTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/SecondaryApplicantInfoTab.vue";
import ProfessionalInfoTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/ProfessionalInfoTab.vue";
import ApplicationSignaturesTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/ApplicationSignaturesTab.vue";

const props = defineProps({
    applicationDetail: Object,
    isLoading: Boolean,
})

const tabs = ref([
    {   
        title: wTrans('public.basic_info'),
        type: 'basic_info',
        component: h(BasicInfoTab, {application: props.applicationDetail}),
    },
    {   
        title: wTrans('public.primary_applicant_info'),
        type: 'primary_applicant_info',
        component: h(PrimaryApplicantInfoTab, {application: props.applicationDetail}),
    },
    {   
        title: wTrans('public.secondary_applicant_info'),
        type: 'secondary_applicant_info',
        component: h(SecondaryApplicantInfoTab, {application: props.applicationDetail}),
    },
    {   
        title: wTrans('public.professional_info'),
        type: 'professional_info',
        component: h(ProfessionalInfoTab, {application: props.applicationDetail}),
    },
    {   
        title: wTrans('public.application_signatures'),
        type: 'application_signatures',
        component: h(ApplicationSignaturesTab, {application: props.applicationDetail}),
    },
]);

const selectedType = ref('basic_info');
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
    <div class="w-full flex flex-col items-center p-3 gap-3 self-stretch rounded-lg bg-white dark:bg-gray-800 shadow-card md:px-6 md:py-5">
        <div class="w-full grid col-span-1 md:col-span-2">
            <Tabs v-model:value="activeIndex" class="w-full gap-3">
                <TabList>
                    <Tab 
                        v-for="(tab, index) in tabs" 
                        :key="tab.title"
                        :value="index"
                    >
                        {{ `${tab.title}` }}
                </Tab>
                </TabList>
                <TabPanels>
                    <TabPanel :key="activeIndex" :value="activeIndex">
                        <component :is="tabs[activeIndex].component" :key="tabs[activeIndex].type" :application="props.applicationDetail" v-if="tabs[activeIndex].component"/>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </div>
</template>