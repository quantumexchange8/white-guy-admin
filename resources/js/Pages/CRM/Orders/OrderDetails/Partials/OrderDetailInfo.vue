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
    orderDetail: Object,
    isLoading: Boolean,
})

const visible = ref(false)
// const countries = ref(props.countries)
const selectedCountry = ref();
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

// watch(() => props.orderDetail, (user) => {
//     form.user_id = props.orderDetail.id
//     form.name = props.orderDetail.name
//     form.email = props.orderDetail.email
//     form.phone = props.orderDetail.phone

//     // Set selectedCountry based on dial_code
//     // selectedCountry.value = countries.value.find(country => country.phone_code === user.dial_code);

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
                <span class="w-full text-gray-950 dark:text-gray-100 font-bold text-xxl break-words">{{ $t('public.order_details') }}</span>
                <Button
                    type="button"
                    iconOnly
                    size="base"
                    variant="gray-text"
                    pill
                    @click="openDialog()"
                    :disabled="!orderDetail"
                >
                    <IconPencilMinus size="20" />
                </Button>
            </div>
            <div v-if="isLoading" class="animate-pulse flex flex-col items-start gap-1.5 self-stretch">
                <div class="h-4 bg-gray-200 rounded-full w-48 my-2 md:my-3"></div>
            </div>
            <div v-else class="flex flex-col items-start gap-1 self-stretch">
                <div class="grid items-start gap-1 self-stretch">
                    <span class="w-full truncate self-stretch text-gray-950 dark:text-gray-100 text-xl font-bold">
                        {{ orderDetail?.user?.full_name ? orderDetail?.user?.full_name : '-' }}
                    </span>
                    <span class="w-full truncate self-stretch text-gray-500 dark:text-gray-100 text-sm font-bold">
                        {{ orderDetail?.user?.email ? orderDetail?.user?.email : '-' }}
                    </span>
                    <div class="w-full truncate flex items-start gap-1 self-stretch">
                        <IconPhone size="20" stroke-width="1.25" class="text-gray-500" />
                        <span class="w-full truncate self-stretch text-gray-500 dark:text-gray-100 text-sm font-bold">
                            {{ orderDetail?.user?.phone_number ? orderDetail?.user?.phone_number : '' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-[1px] self-stretch bg-gray-200" />
        <div v-if="isLoading" class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5 animate-pulse">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.created_at') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                    <div class="h-2 bg-gray-200 rounded-full w-48 my-2"></div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.created_at') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 my-2"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.trade_id') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.action_type') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.stock_type') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.stock') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.limb_stage') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 my-2"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.quantity') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-20 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.unit_price') }}</div>
                <div class="h-3 bg-gray-200 rounded-full w-36 mt-1 mb-1.5"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.current_unit_price') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.amount') }}</div>
                <div class="h-2 bg-gray-200 rounded-full w-36 mt-2 mb-1"></div>
            </div>
        </div>

        <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.created_at') }}</div>
                <!-- <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.created_at ? formatToUserTimezone(props.orderDetail.created_at, user.timezone, true) : '-' }}</div> -->
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.created_at ? props.orderDetail.created_at : '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.trade_id') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.trade_id || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.action_type') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.action_type || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.stock_type') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.stock_type || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.stock') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.stock || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.limb_stage') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.limb_stage || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.quantity') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.quantity || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.unit_price') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.unit_price || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.current_unit_price') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">{{ props.orderDetail?.current_unit_price || '-' }}</div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-gray-500 dark:text-gray-100 truncate">{{ $t('public.amount') }}</div>
                <div class="truncate text-gray-700 dark:text-gray-300 font-medium">
                    {{ props.orderDetail?.action_type === 'SELL' ? formatAmount(props.orderDetail.profit) : (props.orderDetail?.action_type === 'BUY' ? formatAmount(props.orderDetail.unit_price * props.orderDetail.quantity) : '-') }}
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

