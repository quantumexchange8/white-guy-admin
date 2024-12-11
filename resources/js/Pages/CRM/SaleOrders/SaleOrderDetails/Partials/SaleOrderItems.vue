<script setup>
import { IconCircleXFilled, IconSearch, IconDownload, IconFilterOff } from "@tabler/icons-vue";
import { ref, watch, watchEffect, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Loader from "@/Components/Loader.vue";
import Empty from "@/Components/Empty.vue";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Button from '@/Components/Button.vue';
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { trans, wTrans } from "laravel-vue-i18n";
import DatePicker from 'primevue/datepicker';
import debounce from "lodash/debounce.js";
import { transactionFormat, formatToUserTimezone } from "@/Composables/index.js";

dayjs.extend(utc);
dayjs.extend(timezone);

const props = defineProps({
    sale_order: Object
})

const visible = ref(false);
const loading = ref(false);
const dt = ref(null);
const items = ref([]);
const { formatAmount } = transactionFormat();
const filters = ref({
    global: null,
});

const clearFilterGlobal = () => {
    filters.value.global = null;
}

const clearFilter = () => {
    filters.value.global = null;
    selectedDate.value = [minDate.value, maxDate.value];
};

// Get current date
const today = new Date();

// Define minDate and maxDate
const minDate = ref(new Date(today.getFullYear(), today.getMonth(), 1));
const maxDate = ref(today);

// Reactive variable for selected date range
const selectedDate = ref([minDate.value, maxDate.value]);

// Clear date selection
const clearDate = () => {
    selectedDate.value = null;
};

const getResults = async (dateRanges = null) => {
    loading.value = true;

    try {
        let url = `/crm/saleOrder/getSaleOrderItems?id=${props.sale_order.id}`;

        if (filters.value.global) {
            url += `&search=${filters.value.global}`;
        }

        if (dateRanges) {
            const [startDate, endDate] = dateRanges;
            url += `&startDate=${dayjs(startDate).format('YYYY-MM-DD')}&endDate=${dayjs(endDate).format('YYYY-MM-DD')}`;
        }

        const response = await axios.get(url);
        items.value = response.data;
    } catch (error) {
        console.error('Error fetching adjustment history data:', error);
    } finally {
        loading.value = false;
    }
}

getResults(selectedDate.value)

watch(selectedDate, (newDateRange) => {
    if (Array.isArray(newDateRange)) {
        const [startDate, endDate] = newDateRange;

        if (startDate && endDate) {
            getResults([startDate, endDate]);
        } else if (startDate || endDate) {
            getResults([startDate || endDate, endDate || startDate]);
        } else {
            getResults([]);
        }
    } else if (newDateRange === null) {
        // Handle the case when selectedDate is null
        getResults(selectedDate.value);
    } else {
        console.warn('Invalid date range format:', newDateRange);
    }
});

// Separate watcher for global filter to make sure it is set to null correctly
watch(() => filters.value.global, (newValue) => {
    if (!newValue?.trim()) {
      filters.value.global = null;
    }
  }
);

watch(filters, debounce(() => {
    const dateRange = selectedDate.value?.length ? selectedDate.value : null;
    getResults(dateRange);
  }, 300),
  { deep: true }
);

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults(selectedDate.value);
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
    <div class="flex flex-col items-center justify-center p-6 gap-6 self-stretch rounded-lg shadow-card overflow-auto bg-white dark:bg-gray-800">
        <DataTable
            :value="items"
            ref="dt"
            :loading="loading"
            removableSort
            selectionMode="single"
            class="h-full w-full"
            scrollable
            scrollHeight="500px"
            @row-click="(event) => openDialog(event.data)"
        >
            <!-- <template #header>
                <div class="flex flex-col justify-between items-center pb-5 gap-3 self-stretch md:flex-row">
                    <div class="flex flex-col items-center gap-3 self-stretch md:flex-row">
                        <div class="relative w-full md:w-60">
                            <div class="absolute top-2/4 -mt-[9px] left-4 text-gray-500 dark:text-gray-300">
                                <IconSearch size="20" stroke-width="1.25" />
                            </div>
                            <InputText v-model="filters['global']" :placeholder="$t('public.keyword_search')" size="search" class="font-normal w-full md:w-60" />
                            <div
                                v-if="filters['global'] !== null"
                                class="absolute top-2/4 -mt-2 right-4 text-gray-300 hover:text-gray-400 select-none cursor-pointer"
                                @click="clearFilterGlobal"
                            >
                                <IconCircleXFilled size="20" />
                            </div>
                        </div>
                        <div class="relative w-full md:w-60">
                            <DatePicker 
                                v-model="selectedDate"
                                selectionMode="range"
                                :manualInput="false"
                                :maxDate="maxDate"
                                dateFormat="dd/mm/yy"
                                showIcon
                                iconDisplay="input"
                                :placeholder="$t('public.select_date')"
                                class="font-normal w-full md:w-60"
                            />
                            <div
                                v-if="selectedDate && selectedDate.length > 0"
                                class="absolute top-[11px] right-3 flex justify-center items-center text-gray-400 select-none cursor-pointer bg-white dark:bg-gray-950 w-6 h-6 "
                                @click="clearDate"
                            >
                                <IconCircleXFilled size="20" />
                            </div>
                        </div>

                        <Select
                            v-model="filters['account_type'].value"
                            :options="accountTypeOption"
                            optionLabel="name"
                            optionValue="value"
                            :placeholder="$t('public.filter_by_account_type')"
                            class="w-full md:w-60 font-normal"
                            scroll-height="236px"
                        />
                    </div>
                    <Button
                        type="button"
                        variant="error-outlined"
                        size="base"
                        class='w-full md:w-auto'
                        @click="clearFilter"
                    >
                        <IconFilterOff size="20" stroke-width="1.25" />
                        {{ $t('public.clear') }}
                    </Button>
                </div>
            </template> -->
            <template #empty>
                <Empty :message="$t('public.no_history_yet')" />
            </template>
            <template #loading>
                <div class="flex flex-col gap-2 items-center justify-center">
                    <Loader />
                    <span class="text-sm text-gray-700">{{ $t('public.loading') }}</span>
                </div>
            </template>
            <template v-if="items?.length > 0">
                <Column field="created_at" sortable style="width: 20%" headerClass="hidden md:table-cell" class="hidden md:table-cell">
                    <template #header>
                        <span class="hidden md:block">{{ $t('public.date') }}</span>
                    </template>
                    <template #body="slotProps">
                        <div class="text-gray-950 dark:text-gray-100 text-sm">
                            {{ dayjs(slotProps.data.created_at).format('YYYY/MM/DD') }}
                        </div>
                    </template>
                </Column>
                <Column field="public_id" style="width: 15%" headerClass="hidden md:table-cell" class="hidden md:table-cell">
                    <template #header>
                        <span class="hidden md:block">{{ $t('public.public_id') }}</span>
                    </template>
                    <template #body="slotProps">
                        <div class="text-gray-950 dark:text-gray-100 text-sm">
                            {{ slotProps.data.public_id }}
                        </div>
                    </template>
                </Column>
                <Column field="order_type" style="width: 15%" headerClass="hidden md:table-cell" class="hidden md:table-cell">
                    <template #header>
                        <span class="hidden md:block">{{ $t('public.order_type') }}</span>
                    </template>
                    <template #body="slotProps">
                        <div class="text-gray-950 dark:text-gray-100 text-sm">
                            {{ slotProps.data.order_type }}
                        </div>
                    </template>
                </Column>
                <Column field="product" style="width: 15%" headerClass="hidden md:table-cell" class="hidden md:table-cell">
                    <template #header>
                        <span class="hidden md:block">{{ $t('public.product') }}</span>
                    </template>
                    <template #body="slotProps">
                        <div class="text-gray-950 dark:text-gray-100 text-sm">
                            {{ slotProps.data.product }}
                        </div>
                    </template>
                </Column>
                <Column field="total_price" sortable style="width: 15%" headerClass="hidden md:table-cell" class="hidden md:table-cell">
                    <template #header>
                        <span class="hidden md:block">{{ `${$t('public.amount')} ($)` }}</span>
                    </template>
                    <template #body="slotProps">
                        {{ formatAmount(slotProps.data.total_price) }}
                    </template>
                </Column>
                <Column field="completed_at" style="width: 20%" headerClass="hidden md:table-cell" class="hidden md:table-cell">
                    <template #header>
                        <span class="hidden md:block">{{ $t('public.completed_at') }}</span>
                    </template>
                    <template #body="slotProps">
                        {{ $t(`${slotProps.data?.completed_at || '-' }`) }}
                    </template>
                </Column>
                <Column field="created_at" headerClass="hidden" class="md:hidden">
                    <template #body="slotProps">
                        <div class="w-full grid grid-cols-2">
                            <div class="flex flex-col items-start justify-center gap-1">
                                <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-bold">{{ dayjs(slotProps.data.created_at).format('YYYY/MM/DD') }}</span>
                                <span class="w-full truncate text-gray-500 dark:text-gray-300 text-sm font-medium">{{ $t('public.order_type') }}:&nbsp;{{ slotProps.data.order_type }}</span>
                            </div>
                            <div class="flex flex-col items-start justify-center gap-1">
                                <span class="w-full truncate text-right text-gray-950 dark:text-gray-100 text-sm font-bold">$&nbsp;{{ slotProps.data.total_price }}</span>
                                <span class="w-full truncate text-right text-gray-500 dark:text-gray-300 text-sm font-medium">{{ $t('public.completed_at') }}:&nbsp;{{ slotProps.data?.completed_at || '-' }}</span>
                            </div>
                        </div>
                    </template>
                </Column>
            </template>
        </DataTable>
    </div>

    <Dialog v-model:visible="visible" modal :header="$t('public.item_details')" class="dialog-xs md:dialog-md">
        <div class="flex flex-col justify-center items-center gap-3 self-stretch pt-4 md:pt-6">
            <div class="flex flex-col justify-between items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700 md:flex-row">
                <div class="flex flex-col items-start w-full truncate">
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-xxl font-semibold">
                        $&nbsp;{{ data?.total_price? formatAmount(data.total_price) : formatAmount(0) }}
                    </span>
                    <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                        <span class="truncate text-gray-500 dark:text-gray-300 text-sm">
                            {{ $t('public.public_id') }}
                            <span class="hidden md:inline">:</span>
                        </span>
                        <span class="truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.public_id ?? '-' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.date') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.created_at ?? '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.order_type') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.order_type ?? '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.product') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.product ? data.product : '-' }}</span>
                </div>
            </div>

            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.price') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.price ? formatAmount(data.price) : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.exchanged_price') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.exchanged_price ? formatAmount(data.exchanged_price) : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.quantity') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.quantity ? formatAmount(data.quantity) : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.subtotal') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.subtotal ? formatAmount(data.subtotal) : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.commission_rate') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.commission_rate !== '' ? data.commission_rate : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.commission') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.commission !== '' ? data.commission : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.total_exchanged_price') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.total_exchanged_price !== '' ? data.total_exchanged_price : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.total_price') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.total_price !== '' ? data.total_price : '-' }}</span>
                </div>
            </div>
        </div>
    </Dialog>

</template>
