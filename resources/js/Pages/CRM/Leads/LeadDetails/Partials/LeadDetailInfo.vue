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
                                :leadDetail="props.leadDetail" 
                            />
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
        </div>
    </div>
</template>

