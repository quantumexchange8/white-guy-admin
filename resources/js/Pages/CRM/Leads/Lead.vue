<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { IconCircleXFilled, IconSearch, IconDownload, IconFilterOff } from "@tabler/icons-vue";
import { ref, watch, watchEffect, onMounted } from "vue";
import Loader from "@/Components/Loader.vue";
import Dialog from "primevue/dialog";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Column from "primevue/column";
import Button from '@/Components/Button.vue';
import Select from "primevue/select";
import { FilterMatchMode } from '@primevue/core/api';
import Empty from "@/Components/Empty.vue";
import { transactionFormat, formatToUserTimezone } from "@/Composables/index.js";
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import { trans, wTrans } from "laravel-vue-i18n";
import DatePicker from 'primevue/datepicker';
import debounce from "lodash/debounce.js";
import LeadActions from "@/Pages/CRM/Leads/Partials/LeadActions.vue";

dayjs.extend(utc);
dayjs.extend(timezone);

const { formatAmount } = transactionFormat();

const user = usePage().props.auth.user;

const visible = ref(false);
const loading = ref(false);
const dt = ref(null);
const search = ref(null);
const transactions = ref([]);
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

const getResults = async (dateRanges = null) => {
    loading.value = true;
    try {
        // Define the base URL
        let url = `/data/leads?rows=${rows.value}&page=${page.value}`;

        // If date ranges are provided, append startDate and endDate to the URL
        if (dateRanges) {
            const [startDate, endDate] = dateRanges;
            url += `&startDate=${dayjs(startDate).format('YYYY-MM-DD')}&endDate=${dayjs(endDate).format('YYYY-MM-DD')}`;
        }

        if (sortField.value && sortOrder.value !== null) {
            url += `&sortField=${sortField.value}&sortOrder=${sortOrder.value}`;
        }

        if (search.value) {
            url += `&search=${search.value}`;
        }

        // Make the API request
        const response = await axios.get(url);
        
        // Update the data and total records with the response
        transactions.value = response.data.data;
        totalRecords.value = response.data.totalRecords;
    } catch (error) {
        console.error('Error fetching leads data:', error);
        transactions.value = [];
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


const filters = ref({
    global: null,
});

const clearFilterGlobal = () => {
    search.value = null;
}

const clearFilter = () => {
    search.value = null;
    selectedDate.value = [minDate.value, maxDate.value];
    sortField.value = null;
    sortOrder.value = null;
    rows.value = 10;
};

watch(search, debounce(() => {
    const dateRange = selectedDate.value.length ? selectedDate.value : null;
    getResults(dateRange);  // Trigger debounced API call
}, 300));

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults();
    }
});

const exportXLSX = () => {
    // Retrieve the array from the reactive proxy
    const data = filteredValue.value;

    // Specify the headers
    const headers = [
        trans('public.name'),
        trans('public.email'),
        trans('public.date'),
        trans('public.account_type'),
        trans('public.volume') + ' (Ł)',
        trans('public.amount') + ' ($)'
    ];

    // Map the array data to XLSX rows
    const rows = data.map(obj => {
        return [
            obj.name !== undefined ? obj.name : '',
            obj.email !== undefined ? obj.email : '',
            obj.execute_at !== undefined ? dayjs(obj.execute_at).format('YYYY/MM/DD') : '',
            obj.account_type !== undefined ? trans('public.' + obj.account_type) : '',
            obj.volume !== undefined ? obj.volume : '',
            obj.rebate !== undefined ? obj.rebate : ''
        ];
    });

    // Combine headers and rows into a single data array
    const sheetData = [headers, ...rows];

    // Create the XLSX content
    let csvContent = "data:text/xlsx;charset=utf-8,";
    
    sheetData.forEach((rowArray) => {
        const row = rowArray.join("\t"); // Use tabs for column separation
        csvContent += row + "\r\n"; // Add a new line after each row
    });

    // Create a temporary link element
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "export.xlsx");

    // Append the link to the document and trigger the download
    document.body.appendChild(link);
    link.click();

    // Clean up by removing the link
    document.body.removeChild(link);
};

// dialog
const data = ref({});
const openDialog = (rowData) => {
    visible.value = true;
    data.value = rowData;
};
</script>

<template>
    <AuthenticatedLayout :title="`${$t('public.leads')}`">
        <div class="flex flex-col justify-center items-center px-3 py-5 self-stretch rounded-lg bg-white dark:bg-gray-700 shadow-card md:p-6 md:gap-6">
            <div class="flex flex-col pb-3 gap-3 items-center self-stretch md:flex-row md:gap-0 md:justify-between md:pb-0">
                <span class="text-gray-950 dark:text-gray-100 dark:text-gray-100 font-semibold self-stretch md:self-auto">{{ $t('public.leads') }}</span>
                <div class="flex flex-col gap-3 items-center self-stretch md:flex-row md:gap-5">
                    <div class="relative w-full md:w-60">
                        <div class="absolute top-2/4 -mt-[9px] left-4 text-gray-500 dark:text-gray-300">
                            <IconSearch size="20" stroke-width="1.25" />
                        </div>
                        <InputText v-model="search" :placeholder="$t('public.keyword_search')" size="search" class="font-normal w-full md:w-60" />
                        <div
                            v-if="search !== null"
                            class="absolute top-2/4 -mt-2 right-4 text-gray-300 hover:text-gray-400 select-none cursor-pointer"
                            @click="clearFilterGlobal"
                        >
                            <IconCircleXFilled size="16" />
                        </div>
                    </div>
                    <Button variant="primary-outlined" @click="exportXLSX()" :disabled="transactions.length <= 0" class="w-full md:w-auto">
                        <IconDownload size="20" stroke-width="1.25" />
                        {{ $t('public.export') }}
                    </Button>
                </div>
            </div>
            <DataTable
                ref="dt"
                :loading="loading"
                :value="transactions"
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
                                    class="absolute top-[11px] right-3 flex justify-center items-center text-gray-400 select-none cursor-pointer bg-white dark:bg-gray-800 w-6 h-6 "
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
                        :title="$t('public.empty_rebate_payout_record_title')" 
                        :message="$t('public.empty_rebate_payout_record_message')" 
                    />
                </template>
                <template #loading>
                    <div class="flex flex-col gap-2 items-center justify-center">
                        <Loader />
                        <span class="text-sm text-gray-700 dark:text-gray-100">{{ $t('public.loading') }}</span>
                    </div>
                </template>
                <template v-if="transactions?.length > 0">
                    <Column field="last_name" sortable :header="$t('public.name')" class="w-3/4 md:w-[20%] max-w-0 px-3">
                        <template #body="slotProps">
                            <div class="flex flex-col items-start max-w-full">
                                <div class="text-gray-950 dark:text-gray-100 font-semibold truncate max-w-full">
                                    {{ slotProps.data.last_name }}&nbsp;{{ slotProps.data.first_name }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-300 text-xs truncate max-w-full">
                                    {{ slotProps.data.email }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="created_at" :header="$t('public.date')" sortable class="hidden md:table-cell w-full md:w-[15%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm truncate max-w-full">
                                <!-- {{ slotProps.data.created_at ? formatToUserTimezone(slotProps.data.created_at, user.timezone, true) : '-' }} -->
                                {{ slotProps.data.created_at ? slotProps.data.created_at : '-' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="assignee" :header="$t('public.assignee')" class="hidden md:table-cell w-[15%]">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.assignee?.username || '-' }}{{ slotProps.data.assignee?.site?.name ? ` (${slotProps.data.assignee.site.name})` : '' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="contacted_at" :header="`${$t('public.contacted_at')}`" sortable class="hidden md:table-cell w-[20%]">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.contacted_at ? dayjs(slotProps.data.contacted_at).format('YYYY/MM/DD') : '-' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="give_up_at" :header="`${$t('public.give_up')}`" sortable class="hidden md:table-cell w-[20%]">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.give_up_at ? dayjs(slotProps.data.give_up_at).format('YYYY/MM/DD') : '-' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="action" :header="`${$t('public.action')}`" class="w-1/4 md:w-[10%] px-3">
                        <template #body="slotProps">
                            <LeadActions 
                                :lead="slotProps.data"
                            />
                        </template>
                    </Column>
                </template>
            </DataTable>
        </div>
    </AuthenticatedLayout>

    <Dialog v-model:visible="visible" modal :header="$t('public.lead_details')" class="dialog-xs md:dialog-md">
        <div class="flex flex-col justify-center items-center gap-3 self-stretch pt-4 md:pt-6">
            <div class="flex flex-col justify-between items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700 md:flex-row">
                <div class="flex flex-col items-start w-full truncate">
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 font-semibold">{{ data.last_name }}&nbsp;{{ data.first_name }}</span>
                    <span class="w-full truncate text-gray-500 dark:text-gray-300 text-sm">{{ data.email }}</span>
                </div>
            </div>
            
            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[140px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.phone_number') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.phone_number ?? '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[140px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.contacted_at') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.contacted_at ? dayjs(data.contacted_at).format('YYYY/MM/DD') : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[140px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.give_up') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.give_up_at ? dayjs(data.give_up_at).format('YYYY/MM/DD') : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[140px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.country') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.country !== '' ? data.country : '-' }}</span>
                </div>
            </div>

            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[140px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.vc') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.vc !== '' ? data.vc : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[140px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.data_type') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.data_type !== '' ? data.data_type : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[140px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.data_source') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.data_source !== '' ? data.data_source : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[140px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.data_code') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data.data_code !== '' ? data.data_code : '-' }}</span>
                </div>
            </div>
        </div>
    </Dialog>

</template>