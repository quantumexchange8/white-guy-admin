<script setup>
import { defineProps } from 'vue';
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    leadDetail: Object,
});

const user = usePage().props.auth.user;

</script>

<!-- ContactInfoTab.vue -->
<template>
        <div v-if="props.leadDetail" class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_phone_number') }}1</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.phone_number_alt_1 || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_phone_number') }}2</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.phone_number_alt_2 || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_phone_number') }}3</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.phone_number_alt_3 || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_email') }}1</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.email_alt_1 || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_email') }}2</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.email_alt_2 || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_email') }}3</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.leadDetail?.email_alt_3 || '-' }}</div>
            </div>
        </div>
        <div v-else class="w-full grid grid-cols-3 gap-5 animate-pulse">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_phone_number') }}1</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                    <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_phone_number') }}2</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 my-2"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_phone_number') }}3</div>
                <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_email') }}1</div>
                <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_email') }}2</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.alt_email') }}3</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
        </div>
</template>
