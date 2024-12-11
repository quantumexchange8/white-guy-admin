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
import PaymentSubmissionActions from "@/Pages/CRM/PaymentSubmissions/Partials/PaymentSubmissionActions.vue";

dayjs.extend(utc);
dayjs.extend(timezone);

const { formatAmount } = transactionFormat();

const user = usePage().props.auth.user;

const visible = ref(false);
const loading = ref(false);
const dt = ref(null);
const paymentSubmissions = ref([]);
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
        let url = `/crm/paymentSubmission/getPaymentSubmissions?rows=${rows.value}&page=${page.value}`;

        if (filters.value.global) {
            url += `&search=${filters.value.global}`;
        }

        if (sortField.value && sortOrder.value !== null) {
            url += `&sortField=${sortField.value}&sortOrder=${sortOrder.value}`;
        }

        // Make the API request
        const response = await axios.get(url);
        
        // Update the data and total records with the response
        paymentSubmissions.value = response.data.data;
        totalRecords.value = response.data.totalRecords;
    } catch (error) {
        console.error('Error fetching leads data:', error);
        paymentSubmissions.value = [];
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
    <AuthenticatedLayout :title="`${$t('public.payment_submissions')}`">
        <div class="flex flex-col justify-center items-center px-3 py-5 self-stretch rounded-lg bg-white dark:bg-gray-900 shadow-card md:p-6 md:gap-6">
            <div class="flex flex-col pb-3 gap-3 items-center self-stretch md:flex-row md:gap-0 md:justify-between md:pb-0">
                <span class="text-gray-950 dark:text-gray-100 font-semibold self-stretch md:self-auto">{{ $t('public.payment_submissions') }}</span>
                <div class="flex flex-col gap-3 items-center self-stretch md:flex-row md:gap-5">
                    <Button variant="primary-outlined" @click="exportXLSX()" :disabled="paymentSubmissions.length <= 0" class="w-full md:w-auto">
                        <IconDownload size="20" stroke-width="1.25" />
                        {{ $t('public.export') }}
                    </Button>
                </div>
            </div>
            <DataTable
                ref="dt"
                :loading="loading"
                :value="paymentSubmissions"
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
                <template v-if="paymentSubmissions?.length > 0">
                    <Column field="created_at" :header="`${$t('public.date')}`" sortable class="hidden md:table-cell w-[20%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.created_at }}
                            </div>
                        </template>
                    </Column>
                    <Column field="name" :header="$t('public.name')" class="w-3/4 md:w-[20%] max-w-0 px-3">
                        <template #body="slotProps">
                            <div class="flex flex-col items-start max-w-full">
                                <div class="text-gray-950 dark:text-gray-100 font-semibold truncate max-w-full">
                                    {{ slotProps.data?.user?.full_name ? slotProps.data?.user?.full_name : '-' }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-300 text-xs truncate max-w-full">
                                    {{ slotProps.data?.user?.email ? slotProps.data?.user?.email : '-' }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="status" :header="`${$t('public.status')}`" class="hidden md:table-cell w-[15%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data.status ? slotProps.data.status : '-' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="payment_method" :header="`${$t('public.payment_method')}`" class="hidden md:table-cell w-[15%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data?.payment_method?.title ? slotProps.data?.payment_method?.title : '-' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="converted_amount" :header="`${$t('public.amount')}`" class="hidden md:table-cell w-[15%] max-w-0">
                        <template #body="slotProps">
                            <div class="text-gray-950 dark:text-gray-100 text-sm">
                                {{ slotProps.data?.converted_amount ? formatAmount(slotProps.data?.converted_amount) : formatAmount(0) }}
                            </div>
                        </template>
                    </Column>
                    <Column field="action" :header="`${$t('public.action')}`" class="w-1/4 md:w-[15%] max-w-0 px-3">
                        <template #body="slotProps">
                            <PaymentSubmissionActions 
                                :payment_submission="slotProps.data"
                            />
                        </template>
                    </Column>
                </template>
            </DataTable>
        </div>
    </AuthenticatedLayout>

    <Dialog v-model:visible="visible" modal :header="$t('public.payment_submission_details')" class="dialog-xs md:dialog-md">
        <div class="flex flex-col justify-center items-center gap-3 self-stretch pt-4 md:pt-6">
            <div class="flex flex-col-reverse justify-between items-center p-3 gap-1 self-stretch bg-gray-50 dark:bg-gray-700 md:flex-row">
                <div class="flex flex-col items-start w-full truncate">
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 font-semibold">{{ data?.user?.full_name }}</span>
                    <span class="w-full truncate text-gray-500 dark:text-gray-300 text-sm">{{ data?.user?.email }}</span>
                </div>
                <div class="flex flex-col w-full truncate">
                    <span class="w-full truncate md:text-right text-xl text-gray-950 dark:text-gray-100 font-bold">$&nbsp;{{ formatAmount(data?.converted_amount) }}</span>
                </div>
            </div>

            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.date') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.created_at ? data?.created_at : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.payment_method') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.payment_method?.title ? data?.payment_method?.title : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.status') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.status ? data?.status : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.approved_at') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.approved_at ? data?.approved_at : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.amount') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">$ {{ data?.amount ? data?.amount : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.converted_amount') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">$ {{ data?.converted_amount ? data?.converted_amount : '-' }}</span>
                </div>
            </div>
            
            <div class="flex flex-col items-center p-3 gap-3 self-stretch bg-gray-50 dark:bg-gray-700">
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.user_memo') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.user_memo ? data?.user_memo : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.admin_memo') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.admin_memo ? data?.admin_memo : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1 md:flex-row">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.admin_remark') }}</span>
                    <span class="w-full truncate text-gray-950 dark:text-gray-100 text-sm font-medium">{{ data?.admin_remark ? data?.admin_remark : '-' }}</span>
                </div>
            </div>
        </div>
    </Dialog>

</template>