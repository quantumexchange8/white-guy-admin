<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { IconCircleXFilled, IconSearch, IconDownload, IconFilterOff } from "@tabler/icons-vue";
import { ref, watch, watchEffect, onMounted, nextTick } from "vue";
import Loader from "@/Components/Loader.vue";
import Dialog from "primevue/dialog";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Column from "primevue/column";
import Button from '@/Components/Button.vue';
import Select from "primevue/select";
import Empty from "@/Components/Empty.vue";
import { transactionFormat, formatToUserTimezone } from "@/Composables/index.js";
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { trans, wTrans } from "laravel-vue-i18n";
import DatePicker from 'primevue/datepicker';
import debounce from "lodash/debounce.js";
import OrderActions from "@/Pages/CRM/Orders/Partials/OrderActions.vue";

dayjs.extend(utc);
dayjs.extend(timezone);

const { formatAmount } = transactionFormat();

const user = usePage().props.auth.user;

let skipProcess = false;
const visible = ref(false);
const loading = ref(false);
const dt = ref(null);
const orders = ref([]);
const totalRecords = ref(0);
const rows = ref(10);
const page = ref(0);
const sortField = ref(null);  
const sortOrder = ref(null);  // (1 for ascending, -1 for descending)

// Get current date
const today = new Date();

// Define minDate and maxDate
const minDate = ref(new Date(today.getFullYear(), today.getMonth(), 1));
const maxDate = ref(today);

// Reactive variable for selected date range
const selectedDate = ref([minDate.value, maxDate.value]);

// Clear date selection
const clearDate = () => {
    selectedDate.value = [];
};

const filters = ref({
    global: null,
});

const clearFilterGlobal = () => {
    filters.value.global = null;
}

const clearFilter = () => {
    skipProcess = true;
    filters.value.global = null;
    sortField.value = null;
    sortOrder.value = null;
    rows.value = 10;
    skipProcess = false;
};

const getResults = async (dateRanges = null) => {
    loading.value = true;
    try {
        // Define the base URL
        let url = `/crm/order/getOrders?rows=${rows.value}&page=${page.value}`;

        // If date ranges are provided, append startDate and endDate to the URL
        if (dateRanges) {
            const [startDate, endDate] = dateRanges;
            url += `&startDate=${dayjs(startDate).format('YYYY-MM-DD')}&endDate=${dayjs(endDate).format('YYYY-MM-DD')}`;
        }

        if (filters.value.global) {
            url += `&search=${filters.value.global}`;
        }

        if (sortField.value && sortOrder.value !== null) {
            url += `&sortField=${sortField.value}&sortOrder=${sortOrder.value}`;
        }

        // Make the API request
        const response = await axios.get(url);
        
        // Update the data and total records with the response
        orders.value = response.data.data;
        totalRecords.value = response.data.totalRecords;
    } catch (error) {
        console.error('Error fetching leads data:', error);
        orders.value = [];
    } finally {
        loading.value = false;
    }
};

// Load initial data on component mount
onMounted(() => {
    getResults(selectedDate.value);
});


watch(selectedDate, (newDateRange) => {
    if (Array.isArray(newDateRange)) {
        const [startDate, endDate] = newDateRange;

        if (startDate && endDate) {
            // Call getResults with the date range and current sorting parameters
            getResults([startDate, endDate]);
        } else if (startDate || endDate) {
            getResults([startDate || endDate, endDate || startDate]);
        } else {
            getResults(null);  // No date range, just pass sorting
        }
    } else {
        console.warn('Invalid date range format:', newDateRange);
    }
});

const onPageChange = async (event) => {
    rows.value = event.rows;
    page.value = event.page;
    const dateRange = selectedDate.value.length ? selectedDate.value : null;

    getResults(dateRange);
};

const onSort = (event) => {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;  // Store ascending or descending order
    
    // Only pass date if it's not empty
    const dateRange = selectedDate.value.length ? selectedDate.value : null;

    getResults(dateRange);
};

watch(filters.value, debounce(() => {
    page.value = 0;
    const dateRange = selectedDate.value.length ? selectedDate.value : null;
    getResults(dateRange);  // Trigger debounced API call
}, 300), { deep: true });  // Watch the 'filters' object deeply

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults();
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
    <AuthenticatedLayout :title="`${$t('public.orders')}`">
        <div class="flex flex-col justify-center items-center px-3 py-5 self-stretch rounded-lg bg-white dark:bg-gray-800 shadow-card md:p-6 md:gap-6">
            <div class="flex flex-col pb-3 gap-3 items-center self-stretch md:flex-row md:gap-0 md:justify-between md:pb-0">
                <span class="text-gray-950 dark:text-gray-100 font-semibold self-stretch md:self-auto">{{ $t('public.orders') }}</span>
                <div class="flex flex-col gap-3 items-center self-stretch md:flex-row md:gap-5">
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
                    <Button variant="primary-outlined" @click="exportXLSX()" :disabled="orders.length <= 0" class="w-full md:w-auto">
                        <IconDownload size="20" stroke-width="1.25" />
                        {{ $t('public.export') }}
                    </Button>
                </div>
            </div>
            <DataTable
                ref="dt"
                :loading="loading"
                :value="orders"
                lazy
                removableSort
                :paginator="true"
                :rows="rows"
                :totalRecords="totalRecords"
                :rowsPerPageOptions="[10, 20, 50, 100]"
                paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
                :currentPageReportTemplate="$t('public.paginator_caption')"
                selectionMode="single"
                @page="onPageChange"
                @row-click="(event) => openDialog(event.data)"
                @sort="onSort"
                :sortField="sortField"
                :sortOrder="sortOrder"
            >                
                <template #header>
                    <div class="flex flex-col justify-between items-center pb-5 gap-3 self-stretch md:flex-row md:pb-6">
                        <div class="flex flex-col items-center gap-3 self-stretch md:flex-row md:gap-5">
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
                                    class="absolute top-[11px] right-3 flex justify-center items-center text-gray-400 select-none cursor-pointer bg-white dark:bg-gray-900 w-6 h-6 "
                                    @click="clearDate"
                                >
                                    <IconCircleXFilled size="20" />
                                </div>
                            </div>
                            <!-- <Select
                                v-model="filters['account_type'].value"
                                :options="accountTypeOption"
                                optionLabel="name"
                                optionValue="value"
                                :placeholder="$t('public.filter_by_account_type')"
                                class="w-full md:w-60 font-normal"
                                scroll-height="236px"
                            /> -->
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
                </template>
                <template #empty>
                    <Empty 
                        :title="$t('public.empty_record_title')" 
                        :message="$t('public.empty_record_message')" 
                    />
                </template>
                <template #loading>
                    <div class="flex flex-col gap-2 items-center justify-center">
                        <Loader />
                        <span class="text-sm text-gray-700 dark:text-gray-100">{{ $t('public.loading') }}</span>
                    </div>
                </template>
                <template v-if="orders?.length > 0">
                    <Column field="created_at" :header="`${$t('public.date')}`" sortable class="hidden md:table-cell w-[20%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.created_at || '-' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="trade_id" :header="$t('public.trade_id')" sortable class="w-3/4 md:w-[15%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm truncate max-w-full hidden md:block">
                                {{ slotProps.data.trade_id ? slotProps.data.trade_id : '-' }}
                            </div>
                            <div class="flex flex-col items-start max-w-full md:hidden">
                                <div class="text-gray-950 dark:text-gray-100 font-semibold truncate max-w-full">
                                    {{ slotProps.data.trade_id }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-300 text-xs truncate max-w-full">
                                    {{ slotProps.data.created_at }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="action_type" :header="`${$t('public.action')}`" class="hidden md:table-cell w-[15%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.action_type ? slotProps.data.action_type : '-' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="status" :header="`${$t('public.status')}`" class="hidden md:table-cell w-[20%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.status ? slotProps.data.status : '-' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="amount" :header="`${$t('public.amount')}&nbsp;($)`" class="hidden md:table-cell w-[20%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.action_type === 'SELL' ? formatAmount(slotProps.data.profit) : (slotProps.data.action_type === 'BUY' ? formatAmount(slotProps.data.unit_price * slotProps.data.quantity) : '-') }}
                            </div>
                        </template>
                    </Column>
                    <Column field="action" :header="`${$t('public.action')}`" class="w-1/4 md:w-[10%] max-w-0 px-3">
                        <template #body="slotProps">
                            <OrderActions 
                                :order="slotProps.data"
                            />
                        </template>
                    </Column>
                </template>
            </DataTable>
        </div>
    </AuthenticatedLayout>

    <Dialog v-model:visible="visible" modal :header="$t('public.orders_details')" class="dialog-xs md:dialog-md">
        <div class="flex flex-col justify-center items-center gap-3 self-stretch pt-4 md:pt-6">
            <div class="flex flex-col-reverse justify-between items-center p-3 gap-1 self-stretch bg-gray-50 dark:bg-gray-700 md:flex-row">
                <div class="flex flex-col items-start w-full truncate">
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 font-semibold">{{ data.user?.full_name }} {{ data.user?.site?.name ? ` (${data.user?.site.name})` : '' }}</span>
                    <span class="w-full truncate text-gray-500 dark:text-gray-300 text-sm">{{ data.user?.email }}</span>
                </div>
                <div class="flex flex-col w-full truncate">
                    <span class="w-full truncate md:text-right text-xl text-gray-950 dark:text-gray-100 font-bold">$&nbsp;{{ data.action_type === 'SELL' ? formatAmount(data.profit) : (data.action_type === 'BUY' ? formatAmount(data.unit_price * data.quantity) : '-') }}</span>
                </div>
            </div>
            
            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.trade_id') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.trade_id ? data?.trade_id : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.date') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.created_at ? data.created_at : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.action_type') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.action_type ? data.action_type : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.action_type') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.action_type ? data.action_type : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.stage') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.limb_stage ? data.limb_stage : '-' }}</span>
                </div>
            </div>

            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.stock') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.stock ? data.stock : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.stock_type') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.stock_type ? data.stock_type : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.quantity') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.quantity ? data.quantity : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.unit_price') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.unit_price ? data.unit_price : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.current_unit_price') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.current_unit_price ? data.current_unit_price : '-' }}</span>
                </div>
            </div>

            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.confirmation_name') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.confirmation_name ? data.confirmation_name : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.confirmed_at') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.confirmed_at ? data.confirmed_at : '-' }}</span>
                </div>
            </div>
        </div>
    </Dialog>

</template>