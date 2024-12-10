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
import NotificationDetailInfo from '@/Pages/CRM/Notifications/NotificationDetails/Partials/NotificationDetailInfo.vue';
import NotificationActionHistory from '@/Pages/CRM/Notifications/NotificationDetails/Partials/NotificationActionHistory.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    notification: Object,
})

const notificationDetail = ref();
const isLoading = ref(false); 

const getNotificationData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/crm/notification/getNotificationData?id=` + props.notification.id);

        notificationDetail.value = response.data.notificationDetail;
    } catch (error) {
        console.error('Error get Sale Order Data:', error);
    } finally {
        isLoading.value = false; // Set loading state to false after data fetch (success or failure)
    }
};

getNotificationData();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getUserData();
    }
});

</script>

<template>
    <AuthenticatedLayout :title="$t('public.notification_details')">
        <div class="w-full flex flex-col items-center gap-5">
            <!-- Breadcrumb -->
            <div class="flex flex-wrap md:flex-nowrap items-center gap-2 self-stretch text-gray-700 dark:text-gray-100">
                <Button
                    external
                    type="button"
                    variant="primary-text"
                    size="sm"
                    :href="route('crm.saleOrder.index')"
                >
                    {{ $t('public.notification_listing') }}
                </Button>
                <IconChevronRight
                    :size="16"
                    stroke-width="1.25"
                />
                <div class="flex px-4 py-2 items-center justify-center rounded text-sm font-medium">{{ `${props.notification.title}` }} - {{ $t('public.view_details') }}</div>
            </div>

            <div class="w-full h-full grid grid-cols-1 gap-5">
                <NotificationDetailInfo
                    :notificationDetail="notificationDetail"
                    :isLoading="isLoading"
                />
                <NotificationActionHistory
                    :notification="props.notification"
                    :isLoading="isLoading"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>