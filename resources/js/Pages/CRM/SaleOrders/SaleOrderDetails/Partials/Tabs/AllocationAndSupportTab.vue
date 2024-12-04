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
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.sm') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.gm') }}</div>
            <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.fm') }}</div>
            <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.lm') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.ao_1') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.ao_2') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.se') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.docs_received') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.tc_sent') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.tt_received') }}</div>
            <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
        </div>
    </div>

    <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.sm') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.sm_number ? props.saleOrderDetail.sm_number : '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.gm') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.gm_number || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.fm') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.fm_number || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.lm') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.lm || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.ao_1') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium flex flex-col items-start">
                <div class="flex flex-col items-start max-w-full">
                    <div class="font-semibold truncate max-w-full">
                        {{ props.saleOrderDetail?.ao_1 }}
                    </div>
                    <div class="text-xs truncate max-w-full">
                        {{ props.saleOrderDetail?.ao_1_name }}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.ao_2') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium flex flex-col items-start">
                <div class="flex flex-col items-start max-w-full">
                    <div class="font-semibold truncate max-w-full">
                        {{ props.saleOrderDetail?.ao_2 }}
                    </div>
                    <div class="text-xs truncate max-w-full">
                        {{ props.saleOrderDetail?.ao_2_name }}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.ao_2') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium flex flex-col items-start">
                <div class="flex flex-col items-start max-w-full">
                    <div class="font-semibold truncate max-w-full">
                        {{ props.saleOrderDetail?.se_number }}
                    </div>
                    <div class="text-xs truncate max-w-full">
                        {{ props.saleOrderDetail?.se_name }}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.docs_received') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.docs_received || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.tc_sent') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.tc_sent || '-' }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.tt_received') }}</div>
            <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.saleOrderDetail?.tt_received || '-' }}</div>
        </div>
    </div>
</template>
