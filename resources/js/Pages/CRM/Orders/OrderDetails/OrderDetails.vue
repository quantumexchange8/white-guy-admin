<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm } from "@inertiajs/vue3";
import { h, ref, watch, watchEffect } from "vue";
import { IconChevronRight } from '@tabler/icons-vue';
import Button from '@/Components/Button.vue';
import OrderDetailInfo from '@/Pages/CRM/Orders/OrderDetails/Partials/OrderDetailInfo.vue';
import OrderNotes from '@/Pages/CRM/Orders/OrderDetails/Partials/OrderNotes.vue';
import OrderActionHistory from '@/Pages/CRM/Orders/OrderDetails/Partials/OrderActionHistory.vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import { wTrans } from "laravel-vue-i18n";

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

const tabs = ref([
    {   
        title: wTrans('order_notes'),
        type: 'order_notes',
        component: h(OrderNotes),
    },
    {   
        title: wTrans('order_history'),
        type: 'order_history',
        component: h(OrderActionHistory),
    },
]);

const selectedType = ref('order_notes');
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
                <!-- <OrderActionHistory
                    :order="props.order"
                    :isLoading="isLoading"
                />
                <OrderNotes
                    :order="props.order"
                    :isLoading="isLoading"
                /> -->
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
                                :order="props.order" 
                                :isLoading="isLoading" 
                                v-if="tabs[activeIndex].component"
                            />
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
        </div>
    </AuthenticatedLayout>
</template>