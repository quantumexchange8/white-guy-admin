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
import SecurityAndPublicCompanyTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/SecurityAndPublicCompanyTab.vue";
import FinancialInfoTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/FinancialInfoTab.vue";
import AccountAndGoalsDecisionMakingTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/AccountAndGoalsDecisionMakingTab.vue";
import InvestmentExperienceTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/InvestmentExperienceTab.vue";
import DocumentsTab from "@/Pages/CRM/Applications/Details/Partials/Tabs/DocumentsTab.vue";

const props = defineProps({
    applicationDetail: Object,
    isLoading: Boolean,
})

const tabs = ref([
    {   
        title: wTrans('public.security_and_public_company'),
        type: 'security_and_public_company',
        component: h(SecurityAndPublicCompanyTab, {application: props.applicationDetail}),
    },
    {   
        title: wTrans('public.financial_info'),
        type: 'financial_info',
        component: h(FinancialInfoTab, {application: props.applicationDetail}),
    },
    {   
        title: wTrans('public.account_goals_and_decision_making'),
        type: 'account_goals_and_decision_making',
        component: h(AccountAndGoalsDecisionMakingTab, {application: props.applicationDetail}),
    },
    {   
        title: wTrans('public.investment_experience'),
        type: 'investment_experience',
        component: h(InvestmentExperienceTab, {application: props.applicationDetail}),
    },
    {   
        title: wTrans('public.documents'),
        type: 'documents',
        component: h(DocumentsTab, {application: props.applicationDetail}),
    },
]);

const selectedType = ref('security_and_public_company');
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