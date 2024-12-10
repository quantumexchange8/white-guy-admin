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
import ActionHistory from "@/Pages/CRM/AccountManagerProfiles/Partials/ActionHistory.vue";

const props = defineProps({
    account_manager: Object,
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
        label: 'account_manager_action_history',
        icon: h(IconIdBadge2),
        command: () => {
            visible.value = true;
            dialogType.value = 'account_manager_action_history';
            title.value = 'account_manager_action_history';
        },
    },
    {
        label: 'delete_account_manager',
        icon: h(IconTrashX),
        command: () => {
            requireConfirmation('delete_account_manager')
        },
    },
]);

const confirm = useConfirm();

const requireConfirmation = (action_type) => {
    const messages = {
        delete_account_manager: {
            group: 'headless',
            color: 'error',
            icon: h(IconTrashX),
            header: trans('public.delete_account_manager'),
            message: trans('public.delete_account_manager_desc'),
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.delete'),
            action: () => {
                router.visit(route('paymentMethod.deletePaymentMethod'), {
                    method: 'delete',
                    data: {
                        id: props.account_manager.id,
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
        <template v-if="dialogType === 'account_manager_action_history'">
            <ActionHistory
                :account_manager="props.account_manager"
            />
        </template>
    </Dialog>

</template>
