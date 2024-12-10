<script setup>
import {
    IconDotsVertical,
    IconIdBadge2,
    IconLogin,
    IconArrowsRightLeft,
    IconUserUp,
    IconUserCancel,
    IconUserCheck,
    IconTool,
    IconLockCog,
    IconTrashX,
    IconChevronRight,
} from "@tabler/icons-vue";
import Button from "@/Components/Button.vue";
import { computed, h, ref, watch } from "vue";
import TieredMenu from "primevue/tieredmenu";
import ToggleSwitch from 'primevue/toggleswitch';
import { router } from "@inertiajs/vue3";
import { useConfirm } from "primevue/useconfirm";
import { trans, wTrans } from "laravel-vue-i18n";
import Dialog from "primevue/dialog";
import ActionHistory from "@/Pages/CRM/PaymentSubmissions/Partials/ActionHistory.vue";

const props = defineProps({
    payment_submission: Object,
})

const menu = ref();
const visible = ref(false)
const dialogType = ref('')
const title = ref('')

const toggle = (event) => {
    menu.value.toggle(event);
};

const items = ref([
    {
        label: 'payment_submission_action_history',
        icon: h(IconIdBadge2),
        command: () => {
            visible.value = true;
            dialogType.value = 'payment_submission_action_history';
            title.value = 'payment_submission_action_history';
        },
    },
    {
        label: 'delete_payment_submission',
        icon: h(IconTrashX),
        command: () => {
            requireConfirmation('delete_payment_submission')
        },
    },
]);

const confirm = useConfirm();

const requireConfirmation = (action_type) => {
    const messages = {
        delete_payment_submission: {
            group: 'headless',
            color: 'error',
            icon: h(IconTrashX),
            header: trans('public.delete_payment_submission'),
            message: trans('public.delete_payment_submission_desc'),
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.delete'),
            action: () => {
                router.visit(route('paymentSubmission.deletePaymentSubmission'), {
                    method: 'delete',
                    data: {
                        id: props.payment_submission.id,
                    },
                })
            }
        },
    };

    const { group, color, icon, header, message, cancelButton, acceptButton, action } = messages[action_type];

    confirm.require({
        group,
        color,
        icon,
        header,
        message,
        cancelButton,
        acceptButton,
        accept: action
    });
};
</script>

<template>
    <div class="flex gap-3 items-center">
        <Button
            variant="gray-text"
            size="sm"
            type="button"
            iconOnly
            pill
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_tmenu"
        >
            <IconDotsVertical size="16" stroke-width="1.25" class="text-gray-700 dark:text-gray-300" />
        </Button>
        <TieredMenu ref="menu" id="overlay_tmenu" :model="items" popup>
            <template #item="{ item, props, hasSubmenu }">
                <div
                    class="flex items-center gap-3 self-stretch"
                    v-bind="props.action"
                >
                    <component :is="item.icon" size="20" stroke-width="1.25" class="grow-0 shrink-0" />
                    <span class="text-gray-700 text-sm">{{ $t(`public.${item.label}`) }}</span>
                    <!-- Conditionally render submenu indicator if the item has a submenu -->
                    <span v-if="hasSubmenu" class="ml-auto text-gray-500">
                        <IconChevronRight size="20" stroke-width="1.25" />
                    </span>
                </div>
            </template>
        </TieredMenu>
    </div>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t('public.' + title)"
        class="dialog-xs md:dialog-md"
    >
        <template v-if="dialogType === 'payment_submission_action_history'">
            <ActionHistory 
                :payment_submission="props.payment_submission"
            />
        </template>
    </Dialog>

</template>
