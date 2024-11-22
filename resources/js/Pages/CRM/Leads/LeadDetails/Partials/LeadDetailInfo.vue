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
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import { wTrans } from "laravel-vue-i18n";
import { usePage } from "@inertiajs/vue3";
import LeadDetailTab from "@/Pages/CRM/Leads/LeadDetails/Partials/Tabs/LeadDetailTab.vue";
import DataAndAppointmentTab from "@/Pages/CRM/Leads/LeadDetails/Partials/Tabs/DataAndAppointmentTab.vue";
import CommunicationAndAssignmentTab from "@/Pages/CRM/Leads/LeadDetails/Partials/Tabs/CommunicationAndAssignmentTab.vue";
import ContactInfoTab from "@/Pages/CRM/Leads/LeadDetails/Partials/Tabs/ContactInfoTab.vue";
import RemarksAndSystemTab from "@/Pages/CRM/Leads/LeadDetails/Partials/Tabs/RemarksAndSystemTab.vue";
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc);
dayjs.extend(timezone);

const user = usePage().props.auth.user;

const props = defineProps({
    leadDetail: Object,
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

// watch(() => props.leadDetail, (user) => {
//     form.user_id = props.leadDetail.id
//     form.name = props.leadDetail.name
//     form.email = props.leadDetail.email
//     form.phone = props.leadDetail.phone

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

const tabs = ref([
    {   
        title: wTrans('lead_details'),
        type: 'lead_details',
        component: h(LeadDetailTab),
    },
    {   
        title: wTrans('data_and_appointment'),
        type: 'data_and_appointment',
        component: h(DataAndAppointmentTab),
    },
    {   
        title: wTrans('communication_and_assignment'),
        type: 'communication_and_assignment',
        component: h(CommunicationAndAssignmentTab),
    },
    {   
        title: wTrans('contact_info'),
        type: 'contact_info',
        component: h(ContactInfoTab),
    },
    {   
        title: wTrans('remarks_and_system'),
        type: 'remarks_and_system',
        component: h(RemarksAndSystemTab),
    },
]);

const selectedType = ref('lead_details');
const activeIndex = ref(tabs.value.findIndex(tab => tab.type === selectedType.value));

// Watch for changes in selectedType and update the activeIndex accordingly
watch(selectedType, (newType) => {
    const index = tabs.value.findIndex(tab => tab.type === newType);
    if (index >= 0) {
        activeIndex.value = index;
        getResults();
    }
});

function updateType(event) {
    const selectedTab = tabs.value[event.index];
    selectedType.value = selectedTab.type;
}

</script>

<template>
    <div class="w-full flex flex-col items-center p-3 gap-3 self-stretch rounded-lg bg-white dark:bg-gray-800 shadow-card md:px-6 md:py-5">
        <div class="flex flex-col justify-center items-center gap-4 self-stretch">
            <div class="flex justify-between items-start self-stretch">
                <span class="w-full text-gray-950 dark:text-gray-100 font-bold text-xxl break-words">{{ $t('public.lead_details') }}</span>
                <Button
                    type="button"
                    iconOnly
                    size="base"
                    variant="gray-text"
                    pill
                    @click="openDialog()"
                    :disabled="!leadDetail"
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
                        {{ leadDetail.last_name && leadDetail.first_name ? `${leadDetail.last_name} ${leadDetail.first_name}` : '-' }}
                    </span>
                    <span class="w-full truncate self-stretch text-gray-500 dark:text-gray-100 text-sm font-bold">
                        {{ leadDetail?.email ? leadDetail.email : '-' }}
                    </span>
                    <div class="w-full truncate flex items-start gap-1 self-stretch">
                        <IconPhone size="20" stroke-width="1.25" class="text-gray-500" />
                        <span class="w-full truncate self-stretch text-gray-500 dark:text-gray-100 text-sm font-bold">
                            {{ leadDetail?.phone_number ? leadDetail.phone_number : '-' }}
                        </span>
                    </div>
                </div>
                <Tabs v-model:value="activeIndex" class="w-full" @tab-change="updateType" >
                    <TabList>
                        <Tab 
                            v-for="(tab, index) in tabs" 
                            :key="tab.title"
                            :value="index"
                        >
                            {{ tab.title }}
                        </Tab>
                    </TabList>
                </Tabs>
            </div>
        </div>
        <div class="h-[1px] self-stretch bg-gray-200" />
        <component 
            v-if="tabs[activeIndex].component"
            :is="tabs[activeIndex].component" 
            key="tabs[activeIndex].type" 
            :leadDetail="props.leadDetail" 
        />
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

