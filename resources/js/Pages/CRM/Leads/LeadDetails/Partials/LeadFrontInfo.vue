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
import BasicInfoTab from "@/Pages/CRM/Leads/LeadDetails/Partials/Tabs/BasicInfoTab.vue";
import FinancialDetailTab from "@/Pages/CRM/Leads/LeadDetails/Partials/Tabs/FinancialDetailTab.vue";
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc);
dayjs.extend(timezone);

const user = usePage().props.auth.user;

const props = defineProps({
    leadFront: Object,
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

// watch(() => props.leadFront, (user) => {
//     form.user_id = props.leadFront.id
//     form.name = props.leadFront.name
//     form.email = props.leadFront.email
//     form.phone = props.leadFront.phone

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
        title: wTrans('public.basic_info'),
        type: 'basic_info',
        component: h(BasicInfoTab),
    },
    {   
        title: wTrans('public.financial_details'),
        type: 'financial_details',
        component: h(FinancialDetailTab),
    },
]);

const selectedType = ref('basic_info');
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

</script>

<template>
    <div class="w-full flex flex-col items-center p-3 gap-3 self-stretch rounded-lg bg-white dark:bg-gray-800 shadow-card md:px-6 md:py-5">
        <div class="flex flex-col justify-center items-center gap-2 self-stretch">
            <div class="flex flex-col items-start gap-1 self-stretch">
                <Tabs v-model:value="activeIndex" class="w-full gap-3">
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
                            <component 
                                v-if="tabs[activeIndex].component"
                                :is="tabs[activeIndex].component" 
                                key="tabs[activeIndex].type" 
                                :leadFront="props.leadFront"
                                :isLoading="isLoading"
                            />
                        </TabPanel>
                    </TabPanels>
                </Tabs>
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

