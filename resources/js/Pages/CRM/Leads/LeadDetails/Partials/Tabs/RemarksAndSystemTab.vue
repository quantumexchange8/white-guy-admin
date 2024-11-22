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

<!-- RemarksAndSystemTab.vue -->
<template>
        <div v-if="props.leadDetail" class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.private_remark') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.private_remark || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.remark') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.remark || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.created_by') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail.lead_creator?.username || '-' }}{{ props.leadDetail.lead_creator?.site?.name ? ` (${props.leadDetail.lead_creator.site.name})` : '' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.deleted_at') }}</div>
                <!-- <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.deleted_at ? formatToUserTimezone(props.leadDetail.deleted_at, user.timezone, true) : '-' }}</div> -->
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.deleted_at ? props.leadDetail.deleted_at : '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.deleted_note') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.deleted_note || '-' }}</div>
            </div>
        </div>
        <div v-else class="w-full grid grid-cols-3 gap-5 animate-pulse">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.data_source') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                    <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.data_code') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 my-2"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.data_type') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.appointment_label') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.appointment_start') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.appointment_end') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
        </div>
</template>
