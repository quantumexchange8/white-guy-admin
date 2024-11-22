<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm } from "@inertiajs/vue3";
import { ref, watchEffect } from "vue";
import { IconChevronRight } from '@tabler/icons-vue';
import Button from '@/Components/Button.vue';
import MemberDetailInfo from '@/Pages/CRM/Member/Details/Partials/MemberDetailInfo.vue';
import MemberAccountInfo from '@/Pages/CRM/Member/Details/Partials/MemberAccountInfo.vue';
import MemberOrders from '@/Pages/CRM/Member/Details/Partials/MemberOrders.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    member: Object,
})

const memberDetail = ref();
const isLoading = ref(false);

const getMemberData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/crm/member/getMemberData?id=` + props.member.id);

        memberDetail.value = response.data.memberDetail;
    } catch (error) {
        console.error('Error get network:', error);
    } finally {
        isLoading.value = false; // Set loading state to false after data fetch (success or failure)
    }
};

getMemberData();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getUserData();
    }
});

</script>

<template>
    <AuthenticatedLayout :title="$t('public.member_details')">
        <div class="w-full flex flex-col items-center gap-5">
            <!-- Breadcrumb -->
            <div class="flex flex-wrap md:flex-nowrap items-center gap-2 self-stretch text-gray-700 dark:text-gray-100">
                <Button
                    external
                    type="button"
                    variant="primary-text"
                    size="sm"
                    :href="route('crm.member.index')"
                >
                    {{ $t('public.member_listing') }}
                </Button>
                <IconChevronRight
                    :size="16"
                    stroke-width="1.25"
                />
                <div class="flex px-4 py-2 items-center justify-center rounded text-sm font-medium">{{ `${props.member.username}` }} - {{ $t('public.view_details') }}</div>
            </div>

            <div class="w-full h-full grid grid-cols-1 md:grid-cols-2 gap-5">
                <MemberDetailInfo
                    :memberDetail="memberDetail"
                    :isLoading="isLoading"
                />
                <MemberAccountInfo
                    :memberDetail="memberDetail"
                    :isLoading="isLoading"
                />
                <div class="w-full grid col-span-2">
                    <MemberOrders
                        :user_id="props.member.id"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>