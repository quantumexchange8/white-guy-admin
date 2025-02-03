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
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import SaleOrderInfoTab from '@/Pages/CRM/SaleOrders/SaleOrderDetails/Partials/Tabs/SaleOrderInfoTab.vue';
import ClientDetailInfoTab from '@/Pages/CRM/SaleOrders/SaleOrderDetails/Partials/Tabs/ClientDetailInfoTab.vue';
import AllocationAndSupportTab from '@/Pages/CRM/SaleOrders/SaleOrderDetails/Partials/Tabs/AllocationAndSupportTab.vue';

dayjs.extend(utc);
dayjs.extend(timezone);

const user = usePage().props.auth.user;

const props = defineProps({
    saleOrderDetail: Object,
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

// watch(() => props.saleOrderDetail, (user) => {
//     form.user_id = props.saleOrderDetail.id
//     form.name = props.saleOrderDetail.name
//     form.email = props.saleOrderDetail.email
//     form.phone = props.saleOrderDetail.phone

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
        title: wTrans('sale_order_details'),
        type: 'sale_order_details',
        component: h(SaleOrderInfoTab),
    },
    {   
        title: wTrans('client_details'),
        type: 'client_details',
        component: h(ClientDetailInfoTab),
    },
    {   
        title: wTrans('allocation_and_support'),
        type: 'allocation_and_support',
        component: h(AllocationAndSupportTab),
    },
    // {   
    //     title: wTrans('contact_info'),
    //     type: 'contact_info',
    //     component: h(ContactInfoTab),
    // },
    // {   
    //     title: wTrans('remarks_and_system'),
    //     type: 'remarks_and_system',
    //     component: h(RemarksAndSystemTab),
    // },
]);

const selectedType = ref('sale_order_details');
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
        <div class="flex flex-col items-start gap-1 self-stretch">
            <Tabs v-model:value="activeIndex" class="w-full" >
                <TabList>
                    <Tab 
                        v-for="(tab, index) in tabs" 
                        :key="tab.title"
                        :value="index"
                    >
                        {{ tab.title }}
                    </Tab>
                </TabList>

                <TabPanels class="py-2">
                    <TabPanel :key="activeIndex" :value="activeIndex">
                        <component 
                            v-if="tabs[activeIndex].component"
                            :is="tabs[activeIndex].component" 
                            key="tabs[activeIndex].type" 
                            :saleOrderDetail="props.saleOrderDetail"
                            :isLoading="isLoading"
                        />
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </div>
</template>

