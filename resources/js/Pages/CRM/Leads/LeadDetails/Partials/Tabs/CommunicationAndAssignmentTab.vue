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

<!-- CommunicationAndAssignmentTab.vue -->
<template>
        <div v-if="props.leadDetail" class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.last_called') }}</div>
                <!-- <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.contacted_at ? formatToUserTimezone(props.leadDetail.contacted_at, user.timezone, true) : '-' }}</div> -->
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.contacted_at ? props.leadDetail.contacted_at : '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.contact_outcome') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.contact_outcome?.title || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.give_up_at') }}</div>
                <!-- <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.give_up_at ? formatToUserTimezone(props.leadDetail.give_up_at, user.timezone, true) : '-' }}</div> -->
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.give_up_at ? props.leadDetail.give_up_at : '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.assignee') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail.assignee?.username || '-' }}{{ props.leadDetail.assignee?.site?.name ? ` (${props.leadDetail.assignee.site.name})` : '' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.assignee_read_at') }}</div>
                <!-- <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.assignee_read_at ? formatToUserTimezone(props.leadDetail.assignee_read_at, user.timezone, true) : '-' }}</div> -->
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.assignee_read_at ? props.leadDetail.assignee_read_at : '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.stage') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.stage?.title || '-' }}</div>
            </div>
        </div>
        <div v-else class="w-full grid grid-cols-3 gap-5 animate-pulse">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.last_called') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                    <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.contact_outcome') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 my-2"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.give_up_at') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.assignee') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.assignee_read_at') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.stage') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
        </div>
</template>
