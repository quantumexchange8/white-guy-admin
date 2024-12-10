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
        class="relative w-full flex flex-col flex-1 max-w-full max-h-full gap-1 items-center "
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
                :title="$t('public.announcements')"
                :href="route('crm.announcement.index')"
                :active="route().current('crm.announcement.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.notifications')"
                :href="route('crm.notification.index')"
                :active="route().current('crm.notification.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.documents')"
                :href="route('crm.document.index')"
                :active="route().current('crm.document.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.internal_news')"
                :href="route('crm.internalNews.index')"
                :active="route().current('crm.internalNews.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.private_messages')"
                :href="route('crm.privateMessage.index')"
                :active="route().current('crm.privateMessage.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.account_manager_profiles')"
                :href="route('crm.accountManager.index')"
                :active="route().current('crm.accountManager.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.members')"
                :href="route('crm.member.index')"
                :active="route().current('crm.member.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.orders')"
                :href="route('crm.order.index')"
                :active="route().current('crm.order.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.sale_orders')"
                :href="route('crm.saleOrder.index')"
                :active="route().current('crm.saleOrder.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.leads')"
                :href="route('crm.lead.index')"
                :active="route().current('crm.lead.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.payment_methods')"
                :href="route('crm.paymentMethod.index')"
                :active="route().current('crm.paymentMethod.*')"
            />

            <SidebarCollapsibleItem
                :title="$t('public.payment_submissions')"
                :href="route('crm.paymentSubmission.index')"
                :active="route().current('crm.paymentSubmission.*')"
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
