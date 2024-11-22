<script setup>
import { ref, watchEffect } from "vue";
import { usePage } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { transactionFormat } from "@/Composables/index.js";
import Loader from "@/Components/Loader.vue";
import Empty from "@/Components/Empty.vue";
import dayjs from "dayjs";
import Dialog from "primevue/dialog";

const props = defineProps({
    user_id: Number
})

const visible = ref(false);
const loading = ref(false);
const dt = ref(null);
const orders = ref([]);
const { formatAmount } = transactionFormat();

const getMemberOrders = async () => {
    loading.value = true;

    try {
        const response = await axios.get(`/crm/member/getMemberOrders?id=${props.user_id}`);
        orders.value = response.data;
    } catch (error) {
        console.error('Error fetching adjustment history data:', error);
    } finally {
        loading.value = false;
    }
}
getMemberOrders();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getMemberOrders();
    }
});

// dialog
const data = ref({});
const openDialog = (rowData) => {
    visible.value = true;
    data.value = rowData;
};

</script>

<template>
    <!-- data table -->
    <div class="flex flex-col items-center justify-center p-6 gap-6 self-stretch rounded-lg shadow-card h-[400px] bg-white dark:bg-gray-800">
        <DataTable
            :value="orders"
            ref="dt"
            :loading="loading"
            removableSort
            selectionMode="single"
            class="hidden md:block h-full w-full"
            scrollable
            scrollHeight="350px"
            @row-click="(event) => openDialog(event.data)"
        >
            <template #empty>
                <Empty :message="$t('public.no_history_yet')">
                    <template #image>
                        <div class="w-60 h-[100px]"></div>
                    </template>
                </Empty>
            </template>
            <template #loading>
                <div class="flex flex-col gap-2 items-center justify-center">
                    <Loader />
                    <span class="text-sm text-gray-700">{{ $t('public.loading') }}</span>
                </div>
            </template>
            <Column field="created_at" sortable style="width: 20%" headerClass="hidden md:table-cell">
                <template #header>
                    <span class="hidden md:block">{{ $t('public.date') }}</span>
                </template>
                <template #body="slotProps">
                    <div class="text-gray-950 dark:text-gray-100 text-sm">
                        {{ dayjs(slotProps.data.created_at).format('YYYY/MM/DD') }}
                    </div>
                </template>
            </Column>
            <Column field="trade_id" style="width: 20%" headerClass="hidden md:table-cell">
                <template #header>
                    <span class="hidden md:block">{{ $t('public.trade_ids') }}</span>
                </template>
                <template #body="slotProps">
                    <div class="text-gray-950 dark:text-gray-100 text-sm">
                        {{ slotProps.data.trade_id }}
                    </div>
                </template>
            </Column>
            <Column field="action_type" style="width: 20%" headerClass="hidden md:table-cell">
                <template #header>
                    <span class="hidden md:block">{{ $t('public.type') }}</span>
                </template>
                <template #body="slotProps">
                    {{ $t(`${slotProps.data.action_type}`) }}
                </template>
            </Column>
            <Column field="profit" sortable style="width: 20%" headerClass="hidden md:table-cell">
                <template #header>
                    <span class="hidden md:block">{{ `${$t('public.amount')} ($)` }}</span>
                </template>
                <template #body="slotProps">
                    {{ formatAmount(slotProps.data.profit) }}
                </template>
            </Column>
            <Column field="limb_stage" style="width: 20%" headerClass="hidden md:table-cell">
                <template #header>
                    <span class="hidden md:block">{{ $t('public.stage') }}</span>
                </template>
                <template #body="slotProps">
                    {{ slotProps.data.limb_stage }}
                </template>
            </Column>
        </DataTable>

        <div v-if="orders?.length <= 0" class="flex flex-col items-center flex-1 self-stretch md:hidden">
            <Empty :message="$t('public.no_history_yet')">
                <template #image>
                </template>
            </Empty>
        </div>
    </div>

    <Dialog v-model:visible="visible" modal :header="$t('public.order_details')" class="dialog-xs md:dialog-md">
        <div class="flex flex-col justify-center items-center gap-3 self-stretch pt-4 md:pt-6">
            <div class="flex flex-col justify-between items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700 md:flex-row">
                <div class="flex flex-col items-start w-full truncate">
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-xxl font-semibold">
                        {{ data.action_type === 'BUY' ? formatAmount(data.unit_price * data.quantity) : (data.action_type === 'SELL' ? formatAmount(data.profit) : '-') }}
                    </span>
                    <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                        <span class="truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.trade_id') }}:</span>
                        <span class="truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.trade_id ?? '-' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.date') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.created_at ?? '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.action_type') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.action_type ?? '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.stock_type') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.stock_type ? data.stock_type : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.stock') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.stock ? data.stock : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.stage') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.limb_stage ? data.limb_stage : '-' }}</span>
                </div>
            </div>

            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.unit_price') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.unit_price ? formatAmount(data.unit_price) : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.quantity') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.quantity ? formatAmount(data.quantity) : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.current_unit_price') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.current_unit_price ? formatAmount(data.current_unit_price) : '-' }}</span>
                </div>
                <div v-if="data.action_type === 'SELL'" class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.profit') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.profit ? formatAmount(data.profit) : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.confirmation_name') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.confirmation_name !== '' ? data.confirmation_name : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.confirmed_at') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.confirmed_at !== '' ? data.confirmed_at : '-' }}</span>
                </div>
            </div>
        </div>
    </Dialog>

</template>
