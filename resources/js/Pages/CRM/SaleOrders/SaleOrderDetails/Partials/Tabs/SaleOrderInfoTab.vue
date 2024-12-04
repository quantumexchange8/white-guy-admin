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
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.written_date') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.site') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 my-2"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.created_by') }}</div>
            <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.currency_pair') }}</div>
            <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.exchange_rate') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.exit_time_frame') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.sell_price') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.trade_plus') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.settlement_date') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.pay_via') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.balance_due') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.exchanged_balance_due') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
    </div>

    <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.written_date') }}</div>
            <!-- <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.written_date ? formatToUserTimezone(props.saleOrderDetail.written_date, user.timezone, true) : '-' }}</div> -->
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.written_date ? props.saleOrderDetail.written_date : '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.site') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.site?.name || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.created_by') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.creator?.name || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.currency_pair') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.currency_pair || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.exchange_rate') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.exchange_rate || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.exit_time_frame') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.exit_time_frame || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.sell_price') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.sell_price || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.trade_plus') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.trade_plus || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.settlement_date') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.settlement_date || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.pay_via') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.pay_via || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.balance_due') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.balance_due || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.exchanged_balance_due') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.exchanged_balance_due || '-' }}</div>
        </div>
    </div>
</template>
