<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm } from "@inertiajs/vue3";
import { ref, watchEffect } from "vue";
import { IconChevronRight } from '@tabler/icons-vue';
import Button from '@/Components/Button.vue';
import LeadDetailInfo from '@/Pages/CRM/Leads/LeadDetails/Partials/LeadDetailInfo.vue';
import LeadFrontInfo from '@/Pages/CRM/Leads/LeadDetails/Partials/LeadFrontInfo.vue';
import LeadNotes from '@/Pages/CRM/Leads/LeadDetails/Partials/LeadNotes.vue';

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
                    <!-- <LeadFrontInfo
                        :leadFront="leadFront"
                        :isLoading="isLoading"
                    /> -->
                </div>
                <div class="w-full grid col-span-1">
                    <!-- <LeadNotes
                        :lead_id="props.lead.id"
                        :isLoading="isLoading"
                    /> -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>