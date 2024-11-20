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

const props = defineProps({
    lead: Object,
})

const menu = ref();

const toggle = (event) => {
    menu.value.toggle(event);
};

const items = ref([
    {
        label: 'lead_details',
        icon: h(IconIdBadge2),
        command: () => {
            window.location.href = `/crm/lead/detail/${props.lead.id}`;
        },
    },
    {
        label: 'delete_lead',
        icon: h(IconTrashX),
        command: () => {
            requireConfirmation('delete_lead')
        },
    },
]);

const confirm = useConfirm();

const requireConfirmation = (action_type) => {
    const messages = {
        delete_lead: {
            group: 'headless',
            color: 'error',
            icon: h(IconTrashX),
            header: trans('public.delete_lead'),
            message: trans('public.delete_lead_desc'),
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.delete'),
            action: () => {
                router.visit(route('member.deleteMember'), {
                    method: 'delete',
                    data: {
                        id: props.member.id,
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
</template>
