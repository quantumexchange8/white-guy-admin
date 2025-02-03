<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm } from "@inertiajs/vue3";
import { h, ref, watch, watchEffect } from "vue";
import { IconChevronRight } from '@tabler/icons-vue';
import Button from '@/Components/Button.vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import { trans, wTrans } from "laravel-vue-i18n";
import MemberDetailInfo from '@/Pages/CRM/Member/Details/Partials/MemberDetailInfo.vue';
import MemberAccountInfo from '@/Pages/CRM/Member/Details/Partials/MemberAccountInfo.vue';
import MemberOrders from '@/Pages/CRM/Member/Details/Partials/MemberOrders.vue';
import MemberActionHistory from '@/Pages/CRM/Member/Details/Partials/MemberActionHistory.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    member: Object,
})

const tabs = ref([
    {   
        title: wTrans('public.orders'),
        type: 'member_order',
        component: h(MemberOrders, {member: props.member}),
    },
    {   
        title: wTrans('public.history'),
        type: 'history',
        component: h(MemberActionHistory, {member: props.member}),
    },
]);

const selectedType = ref('member_order');
const activeIndex = ref(tabs.value.findIndex(tab => tab.type === selectedType.value));

// Sync selectedType to activeIndex
watch(selectedType, (newType) => {
    // console.log('Selected type changed:', newType);
    const index = tabs.value.findIndex(tab => tab.type === newType);
    // console.log('Found index:', index);
    if (index >= 0) {
        activeIndex.value = index;
    }
});

// Sync activeIndex to selectedType
watch(activeIndex, (newIndex) => {
    if (newIndex >= 0 && newIndex < tabs.value.length) {
        selectedType.value = tabs.value[newIndex].type;
        // console.log('Active index changed:', newIndex, 'Updated selectedType:', selectedType.value);
    }
});

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
        getMemberData();
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
                <div class="w-full grid col-span-1 md:col-span-2">
                    <Tabs v-model:value="activeIndex" class="w-full gap-5">
                        <TabList>
                            <Tab 
                                v-for="(tab, index) in tabs" 
                                :key="tab.title"
                                :value="index"
                            >
                                {{ `${tab.title}` }}
                        </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel :key="activeIndex" :value="activeIndex">
                                <component :is="tabs[activeIndex].component" :key="tabs[activeIndex].type" :member="props.member" v-if="tabs[activeIndex].component"/>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>