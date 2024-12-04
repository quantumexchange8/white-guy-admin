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
import SaleOrderDetailInfo from '@/Pages/CRM/SaleOrders/SaleOrderDetails/Partials/SaleOrderDetailInfo.vue';
import SaleOrderItems from '@/Pages/CRM/SaleOrders/SaleOrderDetails/Partials/SaleOrderItems.vue';
import SaleOrderActionHistory from '@/Pages/CRM/SaleOrders/SaleOrderDetails/Partials/SaleOrderActionHistory.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    sale_order: Object,
})

const tabs = ref([
    {   
        title: wTrans('public.sale_order_items'),
        type: 'sale_order_items',
        component: h(SaleOrderItems, {sale_order: props.sale_order}),
    },
    {   
        title: wTrans('public.sale_order_action_history'),
        type: 'history',
        component: h(SaleOrderActionHistory, {sale_order: props.sale_order}),
    },
]);

const selectedType = ref('sale_order_items');
const activeIndex = ref(tabs.value.findIndex(tab => tab.type === selectedType.value));

// Watch for changes in selectedType and update the activeIndex accordingly
watch(selectedType, (newType) => {
    const index = tabs.value.findIndex(tab => tab.type === newType);
    if (index >= 0) {
        activeIndex.value = index;
        getResults();
    }
});

function updateType(event) {
    const selectedTab = tabs.value[event.index];
    selectedType.value = selectedTab.type;
}

const saleOrderDetail = ref();
const isLoading = ref(false); 

const getSaleOrderData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/crm/saleOrder/getSaleOrderData?id=` + props.sale_order.id);

        saleOrderDetail.value = response.data.saleOrderDetail;
    } catch (error) {
        console.error('Error get Sale Order Data:', error);
    } finally {
        isLoading.value = false; // Set loading state to false after data fetch (success or failure)
    }
};

getSaleOrderData();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getUserData();
    }
});

</script>

<template>
    <AuthenticatedLayout :title="$t('public.sale_order_details')">
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
                    {{ $t('public.sale_order_listing') }}
                </Button>
                <IconChevronRight
                    :size="16"
                    stroke-width="1.25"
                />
                <!-- <div class="flex px-4 py-2 items-center justify-center rounded text-sm font-medium">{{ `${props.sale_order.trade_id}` }} - {{ $t('public.view_details') }}</div> -->
            </div>

            <div class="w-full h-full grid grid-cols-1 gap-5">
                <SaleOrderDetailInfo
                    :saleOrderDetail="saleOrderDetail"
                    :isLoading="isLoading"
                />
                <Tabs v-model:value="activeIndex" class="w-full gap-5" @tab-change="updateType" >
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
                            <component :is="tabs[activeIndex].component" :key="tabs[activeIndex].type" :sale_order="props.sale_order" v-if="tabs[activeIndex].component"/>
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
        </div>
    </AuthenticatedLayout>
</template>