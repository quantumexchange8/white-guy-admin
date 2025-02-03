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
import { trans, wTrans } from "laravel-vue-i18n";
import MemberDetailInfo from '@/Pages/CRM/Member/Details/Partials/MemberDetailInfo.vue';
import MemberAccountInfo from '@/Pages/CRM/Member/Details/Partials/MemberAccountInfo.vue';
import ApplicantAccountInfo from '@/Pages/CRM/Applications/Details/Partials/ApplicantAccountInfo.vue';
import FinancialInvestmentInfo from '@/Pages/CRM/Applications/Details/Partials/FinancialInvestmentInfo.vue';
import ApplicationActionHistory from '@/Pages/CRM/Applications/Details/Partials/ApplicationActionHistory.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    application: Object,
})

const applicationDetail = ref();
const isLoading = ref(false);

const getApplicationData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/crm/application/getApplicationData?id=` + props.application.id);

        applicationDetail.value = response.data.applicationDetail;
    } catch (error) {
        console.error('Error get network:', error);
    } finally {
        isLoading.value = false; // Set loading state to false after data fetch (success or failure)
    }
};

getApplicationData();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getApplicationData();
    }
});

</script>

<template>
    <AuthenticatedLayout :title="$t('public.application_details')">
        <div class="w-full flex flex-col items-center gap-5">
            <!-- Breadcrumb -->
            <div class="flex flex-wrap md:flex-nowrap items-center gap-2 self-stretch text-gray-700 dark:text-gray-100">
                <Button
                    external
                    type="button"
                    variant="primary-text"
                    size="sm"
                    :href="route('crm.application.index')"
                >
                    {{ $t('public.application_listing') }}
                </Button>
                <IconChevronRight
                    :size="16"
                    stroke-width="1.25"
                />
                <div class="flex px-4 py-2 items-center justify-center rounded text-sm font-medium">{{ `${props.application.full_name}` }} - {{ $t('public.view_details') }}</div>
            </div>

            <div class="w-full h-full grid grid-cols-1 gap-5">
                <ApplicantAccountInfo
                    :applicationDetail="applicationDetail"
                    :isLoading="isLoading"
                />
                <FinancialInvestmentInfo
                    :applicationDetail="applicationDetail"
                    :isLoading="isLoading"
                />
                <ApplicationActionHistory
                    :application="applicationDetail"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>