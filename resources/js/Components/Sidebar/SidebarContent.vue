<script setup>
import PerfectScrollbar from '@/Components/PerfectScrollbar.vue'
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import {onMounted, ref, watchEffect} from "vue";
import {usePage} from "@inertiajs/vue3";
import {
    IconLayoutDashboard,
    IconComponents,
    IconUserCircle,
    IconUsersGroup,
    IconReceiptDollar,
    IconId,
    IconCoinMonero,
    IconBusinessplan,
    IconClockDollar,
    IconAward
} from '@tabler/icons-vue';

const pendingWithdrawals = ref(0);
const pendingPammAllocate = ref(0);
const pendingBonusWithdrawal = ref(0);

// const getPendingCounts = async () => {
//     try {
//         const response = await axios.get('/getPendingCounts');
//         pendingWithdrawals.value = response.data.pendingWithdrawals
//         pendingPammAllocate.value = response.data.pendingPammAllocate
//         pendingBonusWithdrawal.value = response.data.pendingBonusWithdrawal
//     } catch (error) {
//         console.error('Error pending counts:', error);
//     }
// };

// onMounted(() => {
//     getPendingCounts();
// })

// watchEffect(() => {
//     if (usePage().props.toast !== null) {
//         getPendingCounts();
//     }
// });
</script>

<template>
    <PerfectScrollbar
        tagname="nav"
        aria-label="main"
        class="relative flex flex-col flex-1 max-h-full gap-1 px-5 py-3 items-center"
    >
        <!-- Dashboard -->
        <SidebarLink
            :title="$t('public.dashboard')"
            :href="route('dashboard')"
            :active="route().current('dashboard')"
        >
            <template #icon>
                <IconLayoutDashboard :size="20" stroke-width="1.25" />
            </template>
        </SidebarLink>

        <!-- CRM -->
        <SidebarCollapsible
            :title="$t('public.crm')"
            :active="route().current('crm.*')"
        >
            <template #icon>
                <IconClockDollar :size="20" stroke-width="1.25" />
            </template>

            <SidebarCollapsibleItem
                :title="$t('public.leads')"
                :href="route('crm.lead.index')"
                :active="route().current('crm.lead.index')"
            />


            <!-- <SidebarCollapsibleItem
                :title="$t('public.withdrawal')"
                :href="route('pending.withdrawal')"
                :active="route().current('pending.withdrawal')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.incentive')"
                :href="route('pending.incentive')"
                :active="route().current('pending.incentive')"
            />
 -->
        </SidebarCollapsible>


    </PerfectScrollbar>
</template>
