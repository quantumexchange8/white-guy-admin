<script setup>
import { defineProps } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { formatToUserTimezone } from "@/Composables/index.js";
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc);
dayjs.extend(timezone);

const props = defineProps({
    saleOrderDetail: Object,
    isLoading: Boolean,
});

const user = usePage().props.auth.user;

</script>

<!-- saleOrdernfoTab.vue -->
<template>
    <div v-if="isLoading" class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5 animate-pulse">
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.contact_name') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.email') }}</div>
            <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.date_of_birth') }}</div>
            <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.home_number') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.mobile_number') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.address_1') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.address_2') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.city') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.country') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.office_number_1') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.office_number_2') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.fax_number') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
    </div>

    <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.contact_name') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.contact_name ? props.saleOrderDetail.contact_name : '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.email') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.email || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.date_of_birth') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.date_of_birth || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.home_number') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.home_number || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.mobile_number') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.mobile_number || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.address_1') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.address_1 || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.address_2') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.address_2 || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.city') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.city || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.country') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.country || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.office_number_1') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.office_number_1 || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.office_number_2') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.office_number_2 || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.fax_number') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.fax_number || '-' }}</div>
        </div>
    </div>
</template>
