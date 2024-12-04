<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm } from "@inertiajs/vue3";
import { ref, watchEffect } from "vue";
import { IconChevronRight } from '@tabler/icons-vue';
import Button from '@/Components/Button.vue';
import OrderDetailInfo from '@/Pages/CRM/Orders/OrderDetails/Partials/OrderDetailInfo.vue';
import OrderActionHistory from '@/Pages/CRM/Orders/OrderDetails/Partials/OrderActionHistory.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    order: Object,
})

const orderDetail = ref();
const isLoading = ref(false); 

const getOrderData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/crm/order/getOrderData?id=` + props.order.id);

        orderDetail.value = response.data.orderDetail;
    } catch (error) {
        console.error('Error get network:', error);
    } finally {
        isLoading.value = false; // Set loading state to false after data fetch (success or failure)
    }
};

getOrderData();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getUserData();
    }
});

</script>

<template>
    <AuthenticatedLayout :title="$t('public.order_details')">
        <div class="w-full flex flex-col items-center gap-5">
            <!-- Breadcrumb -->
            <div class="flex flex-wrap md:flex-nowrap items-center gap-2 self-stretch text-gray-700 dark:text-gray-100">
                <Button
                    external
                    type="button"
                    variant="primary-text"
                    size="sm"
                    :href="route('crm.order.index')"
                >
                    {{ $t('public.order_listing') }}
                </Button>
                <IconChevronRight
                    :size="16"
                    stroke-width="1.25"
                />
                <div class="flex px-4 py-2 items-center justify-center rounded text-sm font-medium">{{ `${props.order.trade_id}` }} - {{ $t('public.view_details') }}</div>
            </div>

            <div class="w-full h-full grid grid-cols-1 gap-5">
                <OrderDetailInfo
                    :orderDetail="orderDetail"
                    :isLoading="isLoading"
                />
                <OrderActionHistory
                    :order="props.order"
                    :isLoading="isLoading"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>