<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { IconCircleXFilled, IconSearch, IconDownload, IconFilterOff, IconCopy } from "@tabler/icons-vue";
import { ref, watch, watchEffect, onMounted } from "vue";
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
import ApplicationActions from "@/Pages/CRM/Applications/Partials/ApplicationActions.vue";

dayjs.extend(utc);
dayjs.extend(timezone);

const { formatAmount } = transactionFormat();

const user = usePage().props.auth.user;

const visible = ref(false);
const loading = ref(false);
const dt = ref(null);
const applications = ref([]);
const totalRecords = ref(0);
const rows = ref(10);
const page = ref(0);
const sortField = ref(null);  
const sortOrder = ref(null);  // (1 for ascending, -1 for descending)
const filters = ref({
    global: null,
});

const clearFilterGlobal = () => {
    filters.value.global = null;
}

const clearFilter = () => {
    filters.value.global = null;
    sortField.value = null;
    sortOrder.value = null;
    rows.value = 10;
};

const getResults = async () => {
    loading.value = true;
    try {
        // Define the base URL
        let url = `/crm/application/getApplications?rows=${rows.value}&page=${page.value}`;

        if (filters.value.global) {
            url += `&search=${filters.value.global}`;
        }

        if (sortField.value && sortOrder.value !== null) {
            url += `&sortField=${sortField.value}&sortOrder=${sortOrder.value}`;
        }

        // Make the API request
        const response = await axios.get(url);
        
        // Update the data and total records with the response
        applications.value = response.data.data;
        totalRecords.value = response.data.totalRecords;
    } catch (error) {
        console.error('Error fetching leads data:', error);
        applications.value = [];
    } finally {
        loading.value = false;
    }
};

// Load initial data on component mount
onMounted(() => {
    getResults();
});


const onPageChange = async (event) => {
    rows.value = event.rows;
    page.value = event.page;

    getResults();
};

const onSort = (event) => {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;  // Store ascending or descending order

    getResults();
};

watch(filters.value, debounce(() => {
    page.value = 0;
    getResults();
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

const tooltipText = ref('copy');
const copyToClipboard = (text) => {
    const textToCopy = text;

    const textArea = document.createElement('textarea');
    document.body.appendChild(textArea);

    textArea.value = textToCopy;
    textArea.select();

    try {
        const successful = document.execCommand('copy');

        tooltipText.value = 'copied';
        setTimeout(() => {
            tooltipText.value = 'copy';
        }, 1500);
    } catch (err) {
        console.error('Copy to clipboard failed:', err);
    }

    document.body.removeChild(textArea);
}

</script>

<template>
    <AuthenticatedLayout :title="`${$t('public.applications')}`">
        <div class="flex flex-col justify-center items-center px-3 py-5 self-stretch rounded-lg bg-white dark:bg-gray-900 shadow-card md:p-6 md:gap-6">
            <div class="flex flex-col pb-3 gap-3 items-center self-stretch md:flex-row md:gap-0 md:justify-between md:pb-0">
                <span class="text-gray-950 dark:text-gray-100 font-semibold self-stretch md:self-auto">{{ $t('public.applications') }}</span>
                <div class="flex flex-col gap-3 items-center self-stretch md:flex-row md:gap-5">
                    <Button variant="primary-outlined" @click="exportXLSX()" :disabled="applications.length <= 0" class="w-full md:w-auto">
                        <IconDownload size="20" stroke-width="1.25" />
                        {{ $t('public.export') }}
                    </Button>
                </div>
            </div>
            <DataTable
                ref="dt"
                :loading="loading"
                :value="applications"
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
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $t('public.loading') }}</span>
                    </div>
                </template>
                <template v-if="applications?.length > 0">
                    <Column field="created_at" :header="$t('public.date')" sortable class="w-3/4 md:w-[20%] max-w-0 px-3">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.created_at }}
                            </div>
                        </template>
                    </Column>
                    <Column field="name" :header="`${$t('public.name')}`" class="hidden md:table-cell w-[30%] max-w-0">
                        <template #body="slotProps">
                            <div class="flex flex-col items-start max-w-full">
                                <div class="text-gray-950 dark:text-gray-100 font-semibold truncate max-w-full">
                                    {{ slotProps.data?.acc_full_name }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-300 text-xs truncate max-w-full">
                                    {{ slotProps.data?.acc_email }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="public_id" :header="`${$t('public.public_id')}`" class="hidden md:table-cell w-[30%] max-w-0">
                        <template #body="slotProps">
                            <div class="flex flex-col items-start max-w-full">
                                <div class="text-gray-950 dark:text-gray-100 text-sm">
                                    {{ slotProps.data.public_id }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="action" :header="`${$t('public.action')}`" class="w-1/4 md:w-[20%] max-w-0 px-3">
                        <template #body="slotProps">
                            <ApplicationActions 
                                :application="slotProps.data"
                            />
                        </template>
                    </Column>
                </template>
            </DataTable>
        </div>
    </AuthenticatedLayout>

    <Dialog v-model:visible="visible" modal :header="$t('public.application_details')" class="dialog-xs md:dialog-md">
        <div class="flex flex-col justify-center items-center gap-3 self-stretch pt-4 md:pt-6">
            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.date') }}</span>
                    <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ data?.created_at ? data?.created_at : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.name') }}</span>
                    <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-all">{{ data?.acc_full_name ? data?.acc_full_name : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.email') }}</span>
                    <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-all">{{ data?.acc_email ? data?.acc_email : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.public_id') }}</span>
                    <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-all">{{ data?.public_id ? data?.public_id : '-' }}</span>
                </div>
            </div>            
        </div>
    </Dialog>

</template>