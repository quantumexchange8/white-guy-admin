<script setup>
import Button from "@/Components/Button.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import { IconPencilMinus, IconCircleCheck, IconCircleX } from "@tabler/icons-vue";
import DefaultProfilePhoto from "@/Components/DefaultProfilePhoto.vue";
import InputSwitch from "primevue/inputswitch";
import { ref, watch, h } from "vue";
import Dialog from "primevue/dialog";
import InputLabel from "@/Components/InputLabel.vue";
import Select from "primevue/select";
import InputError from "@/Components/InputError.vue";
import InputText from "primevue/inputtext";
import { useForm } from "@inertiajs/vue3";
import { generalFormat, transactionFormat, formatToUserTimezone } from "@/Composables/index.js";
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
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc);
dayjs.extend(timezone);

const user = usePage().props.auth.user;

const props = defineProps({
    lead_id: Number,
    // countries: Array,
})

const leadNotes = ref();
const visible = ref(false)
// const countries = ref(props.countries)
const selectedCountry = ref();
const { formatRgbaColor } = generalFormat();
const { formatAmount } = transactionFormat();

const getLeadNotes = async () => {
    try {
        const response = await axios.get(`/crm/lead/getLeadNotes?id=` + props.lead_id);

        leadNotes.value = response.data;
    } catch (error) {
        console.error('Error get network:', error);
    }
};

getLeadNotes();

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

// watch(() => props.leadNotes, (user) => {
//     form.user_id = props.leadNotes.id
//     form.name = props.leadNotes.name
//     form.email = props.leadNotes.email
//     form.phone = props.leadNotes.phone

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
        <div class="flex flex-col justify-center items-center gap-4 self-stretch">
            <div class="flex justify-between items-start self-stretch">
                <span class="text-gray-950 dark:text-white font-bold text-xxl">{{ $t('public.lead_notes') }}</span>
                <Button
                    type="button"
                    iconOnly
                    size="base"
                    variant="gray-text"
                    pill
                    @click="openDialog()"
                    :disabled="!leadNotes"
                >
                    <IconPencilMinus size="20" />
                </Button>
            </div>
        </div>
        <div class="h-[1px] self-stretch bg-gray-200" />
        <!-- Accordion to display lead notes -->
        <Accordion multiple class="w-full max-h-[800px] 2xl:max-h-[700px] overflow-auto">
            <AccordionPanel 
                v-for="(note, index) in leadNotes" 
                :key="note.id" 
                :value="index"
            >
                <div class="flex flex-col items-center pt-2 gap-1">
                    <h3 class="self-stretch text-gray-950 dark:text-gray-100 font-semibold">{{ note.note }}</h3>
                    <!-- <span class="self-stretch text-sm text-gray-500 dark:text-gray-400">{{ formatToUserTimezone(note.created_at, user.timezone, true) }}</span> -->
                    <span class="self-stretch text-sm text-gray-500 dark:text-gray-400">{{ note.created_at }}</span>
                </div>
                <AccordionHeader class="px-2">{{ `${$t('public.lead_note_details')}&nbsp;(${note.id})` }}</AccordionHeader>
                <AccordionContent>
                    <div class="w-full flex flex-col items-center px-2">
                        <div class="w-full flex items-center gap-2">
                            <span class="truncate text-gray-950 dark:text-gray-100 font-semibold">{{ $t('public.user_editable') }}:</span>
                            <component 
                                :is="note.user_editable ? h(IconCircleCheck) : h(IconCircleX)" 
                                size="24" 
                                stroke-width="1.25" 
                                :class="note.user_editable ? 'text-success-700' : 'text-error-500'" 
                            />
                        </div>
                        <div class="w-full flex items-center gap-2">
                            <span class="truncate text-gray-950 dark:text-gray-100 font-semibold">{{ $t('public.created_by') }}:</span>
                            <span class="truncate text-primary-700 font-bold">{{ note.lead_note_creator.username || '' }} ({{ note.lead_note_creator.site.name || '' }})</span>
                        </div>
                    </div>
                </AccordionContent>
            </AccordionPanel>
        </Accordion>
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

