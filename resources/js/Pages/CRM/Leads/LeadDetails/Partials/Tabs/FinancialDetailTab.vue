<script setup>
import { defineProps } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { transactionFormat } from "@/Composables/index.js";

const { formatAmount } = transactionFormat();

const props = defineProps({
    leadFront: Object,
    isLoading: Boolean,
});

const user = usePage().props.auth.user;

</script>

<!-- FinancialDetailTab.vue -->
<template>
    <div v-if="isLoading" class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5 animate-pulse">
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.amount_of_shares') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.price_per_share') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 my-2"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.sdm') }}</div>
            <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.liquid') }}</div>
            <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.agent_commission') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.special_note') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
    </div>

    <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.amount_of_shares') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadFront?.quantity ? formatAmount(props.leadFront.quantity) : '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.price_per_share') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadFront?.price ? formatAmount(props.leadFront.price) : '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.sdm') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadFront?.sdm ? $t('public.yes') : $t('public.no') }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.liquid') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadFront?.liquid ? $t('public.yes') : $t('public.no') }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.agent_commission') }}&nbsp;%</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadFront?.commission ? formatAmount(props.leadFront.commission) : '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.special_note') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadFront?.note || '-' }}</div>
        </div>
    </div>
</template>
