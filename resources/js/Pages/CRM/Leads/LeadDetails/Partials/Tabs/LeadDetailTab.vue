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
    leadDetail: Object,
});

const user = usePage().props.auth.user;

</script>

<!-- LeadDetailTab.vue -->
<template>
        <div v-if="props.leadDetail" class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.date_opp_in') }}</div>
                <!-- <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.date ? formatToUserTimezone(props.leadDetail.date, user.timezone, true) : '-' }}</div> -->
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.date ? props.leadDetail.date : '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.campaign_product') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.campaign_product || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.sdm') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.sdm || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.vc') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail.vc || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.occupation') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail.occupation || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.agents_book') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail.agents_book || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.account_manager') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail.account_manager || '-' }}</div>
            </div>
        </div>
        <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5 animate-pulse">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.date_opped_in') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                    <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.campaign_product') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 my-2"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.sdm') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.vc') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.occupation') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.agents_book') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.account_manager') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
        </div>
</template>
