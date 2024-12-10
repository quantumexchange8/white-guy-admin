<script setup>
import Button from "@/Components/Button.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import { IconPencilMinus, IconPhone } from "@tabler/icons-vue";
import DefaultProfilePhoto from "@/Components/DefaultProfilePhoto.vue";
import InputSwitch from "primevue/inputswitch";
import { ref, watch, h } from "vue";
import Dialog from "primevue/dialog";
import InputLabel from "@/Components/InputLabel.vue";
import Select from "primevue/select";
import InputError from "@/Components/InputError.vue";
import InputText from "primevue/inputtext";
import { useForm } from "@inertiajs/vue3";
import { generalFormat, transactionFormat } from "@/Composables/index.js";
import { useConfirm } from "primevue/useconfirm";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import { wTrans } from "laravel-vue-i18n";
import { usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc);
dayjs.extend(timezone);

const user = usePage().props.auth.user;

const props = defineProps({
    notificationDetail: Object,
    isLoading: Boolean,
})

const visible = ref(false)
// const countries = ref(props.countries)
const { formatRgbaColor } = generalFormat();
const { formatAmount } = transactionFormat();

const openDialog = () => {
    visible.value = true
}

const form = useForm({
    user_id: '',
    name: '',
    email: '',
    dial_code: '',
    phone: '',
    phone_number: '',
});

// watch(() => props.notificationDetail, (user) => {
//     form.user_id = props.notificationDetail.id
//     form.name = props.notificationDetail.name
//     form.email = props.notificationDetail.email
//     form.phone = props.notificationDetail.phone

// });

const submitForm = () => {
    form.dial_code = selectedCountry.value;

    if (selectedCountry.value) {
        form.phone_number = selectedCountry.value.phone_code + form.phone;
    }

    form.post(route('member.updateMemberInfo'), {
        onSuccess: () => {
            visible.value = false;
        },
    });
};

</script>

<template>
    <div class="w-full flex flex-col items-center p-3 gap-3 self-stretch rounded-lg bg-white dark:bg-gray-800 shadow-card md:px-6 md:py-5">
        <div class="flex flex-col justify-center items-center gap-2 self-stretch">
            <div class="flex justify-between items-start self-stretch">
                <span class="w-full text-gray-950 dark:text-gray-100 font-bold text-xxl break-words">{{ $t('public.notification_details') }}</span>
                <Button
                    type="button"
                    iconOnly
                    size="base"
                    variant="gray-text"
                    pill
                    @click="openDialog()"
                    :disabled="!notificationDetail"
                >
                    <IconPencilMinus size="20" />
                </Button>
            </div>
            <div v-if="isLoading" class="animate-pulse flex flex-col items-start gap-2 self-stretch">
                <div class="w-full flex flex-col items-start gap-1">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.title') }}</span>
                    <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                </div>
                <div class="w-full flex flex-col items-start gap-1">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.description') }}</span>
                    <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                </div>
                <div class="w-full flex flex-col items-start gap-1">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.attachment') }}</span>
                    <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                </div>
                <div class="w-full truncate grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.date') }}</span>
                <div     class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                    </div>
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.notification_type') }}</span>
                <div     class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                    </div>
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.viewed?') }}</span>
                <div     class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                    </div>
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.receiver_name') }}</span>
                <div     class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                    </div>
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.receiver_email') }}</span>
                <div     class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                    </div>
                </div>
            </div>
            <div v-else class="flex flex-col items-start gap-2 self-stretch">
                <div class="w-full flex flex-col items-start gap-1">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.title') }}</span>
                    <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ notificationDetail?.title ? notificationDetail?.title : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.description') }}</span>
                    <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ notificationDetail?.description ? notificationDetail?.description : '-' }}</span>
                </div>
                <div class="w-full flex flex-col items-start gap-1">
                    <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.attachment') }}</span>
                    <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ notificationDetail?.attachment ? notificationDetail?.attachment : '-' }}</span>
                </div>
                <div class="w-full truncate grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.date') }}</span>
                        <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ notificationDetail?.created_at ? notificationDetail?.created_at : '-' }}</span>
                    </div>
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.notification_type') }}</span>
                        <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ notificationDetail?.notification_type ? notificationDetail?.notification_type : '-' }}</span>
                    </div>
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.viewed?') }}</span>
                        <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ notificationDetail?.is_read ? $t('public.true') : $t('public.false') }}</span>
                    </div>
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.receiver_name') }}</span>
                        <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ notificationDetail?.user?.full_name ? notificationDetail?.user?.full_name : '-' }}</span>
                    </div>
                    <div class="w-full flex flex-col items-start gap-1">
                        <span class="w-full max-w-[200px] truncate text-gray-500 dark:text-gray-300 text-sm">{{ $t('public.receiver_email') }}</span>
                        <span class="w-full text-gray-950 dark:text-gray-100 text-sm font-medium break-words">{{ notificationDetail?.user?.email ? notificationDetail?.user?.email : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- edit contact info -->
    <Dialog
        v-model:visible="visible"
        modal
        :header="$t('public.edit_member_info')"
        class="dialog-xs md:dialog-sm"
    >
        <form>
            <div class="flex flex-col gap-5 py-4 md:py-6">
                <div class="flex flex-col gap-1">
                    <InputLabel for="name" :value="$t('public.name')" :invalid="!!form.errors.name" />
                    <InputText
                        id="name"
                        type="text"
                        class="block w-full"
                        v-model="form.name"
                        :placeholder="$t('public.enter_name')"
                        :invalid="!!form.errors.name"
                        autocomplete="name"
                    />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="flex flex-col gap-1">
                    <InputLabel for="email" :value="$t('public.email_address')" :invalid="!!form.errors.email" />
                    <InputText
                        id="email"
                        type="email"
                        class="block w-full"
                        v-model="form.email"
                        :placeholder="$t('public.enter_email')"
                        :invalid="!!form.errors.email"
                        autocomplete="email"
                    />
                    <InputError :message="form.errors.email" />
                </div>
                <div class="flex flex-col gap-1 items-start self-stretch">
                    <InputLabel for="phone" :value="$t('public.phone_number')" :invalid="!!form.errors.phone" />
                    <div class="flex gap-2 items-center self-stretch relative">
                        <!-- <Select
                            v-model="selectedCountry"
                            :options="countries"
                            filter
                            :filterFields="['name', 'phone_code']"
                            optionLabel="name"
                            :placeholder="$t('public.phone_code')"
                            class="w-[100px]"
                            scroll-height="236px"
                            :invalid="!!form.errors.dial_code"
                        >
                            <template #value="slotProps">
                                <div v-if="slotProps.value" class="flex items-center">
                                    <div>{{ slotProps.value.phone_code }}</div>
                                </div>
                                <span v-else>
                                    {{ slotProps.placeholder }}
                                </span>
                            </template>
                            <template #option="slotProps">
                                <div class="flex items-center w-[262px] md:max-w-[236px]">
                                    <div>{{ slotProps.option.name }} <span class="text-gray-500">{{ slotProps.option.phone_code }}</span></div>
                                </div>
                            </template>
                        </Select> -->

                        <InputText
                            id="phone"
                            type="text"
                            class="block w-full"
                            v-model="form.phone"
                            :placeholder="$t('public.phone_number')"
                            :invalid="!!form.errors.phone"
                        />
                    </div>
                    <InputError :message="form.errors.phone" />
                </div>
            </div>
            <div class="flex justify-end items-center pt-6 gap-4 self-stretch">
                <Button
                    type="button"
                    variant="gray-tonal"
                    class="w-full"
                    :disabled="form.processing"
                    @click.prevent="visible = false"
                >
                    {{ $t('public.cancel') }}
                </Button>
                <Button
                    type="button"
                    variant="primary-flat"
                    class="w-full"
                    :disabled="form.processing"
                    @click="submitForm"
                >
                    {{ $t('public.save') }}
                </Button>
            </div>
        </form>
    </Dialog>
</template>

