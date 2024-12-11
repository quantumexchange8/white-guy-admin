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
import { wTrans } from "laravel-vue-i18n";
import { usePage } from "@inertiajs/vue3";
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import Empty from "@/Components/Empty.vue";

dayjs.extend(utc);
dayjs.extend(timezone);

const user = usePage().props.auth.user;

const props = defineProps({
    account_manager: Object,
})

const accountManagerProfileActionHistories = ref();
const visible = ref(false)
// const countries = ref(props.countries)
const selectedCountry = ref();
const isLoading = ref(false)
const { formatRgbaColor } = generalFormat();
const { formatAmount } = transactionFormat();

const getAccontManagerProfileLogEntries = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/crm/accountManager/getAccontManagerProfileLogEntries?id=` + props.account_manager.id);

        accountManagerProfileActionHistories.value = response.data;
    } catch (error) {
        console.error('Error get network:', error);
    } finally {
        isLoading.value = false; // Set loading state to false after data fetch (success or failure)
    }
};

getAccontManagerProfileLogEntries();

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

// watch(() => props.accountManagerProfileActionHistories, (user) => {
//     form.user_id = props.accountManagerProfileActionHistories.id
//     form.name = props.accountManagerProfileActionHistories.name
//     form.email = props.accountManagerProfileActionHistories.email
//     form.phone = props.accountManagerProfileActionHistories.phone

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

const formatLogChanges = (value) => {
    const pattern = /^(?:(?:19|20|21)\d{2})-(?:(?:0[1-9])|(?:1[0-2]))-(?:(?:0[1-9])|(?:[12][0-9])|(?:3[01]))\s(?:[01][0-9]|2[0-3]):(?:[0-5][0-9]):(?:[0-5][0-9])\.(?:\d{6})/;
    
    //Check if the value is a valid date format
    if (pattern.test(value)) {
        // Format the date value
        return dayjs(value + '+00').tz(userTimezone).format('YYYY-MM-DD HH:mm:ss');
    } else {
        return value;
    }
}

// Function to parse the changes string from a log entry
const extractChanges = (changes) => {
    let parsedChanges;
    try {
        parsedChanges = JSON.parse(changes);  // Parsing the changes JSON string into an object
    } catch (error) {
        console.error("Error parsing changes:", error);
        return {}; // Return empty if there's an error parsing
    }

    const changeEntries = {};

    // Loop through the parsed changes to extract key, old, and new values
    Object.keys(parsedChanges).forEach(key => {
        const [oldValue, newValue] = parsedChanges[key];  // Assuming the value is an array [oldValue, newValue]

        // Only include changes where the old and new values differ
        if (oldValue !== newValue) {
            changeEntries[key] = { old: oldValue, new: newValue };
        }
    });

    //   // Log the changeEntries for debugging purposes
    //   console.log(changeEntries);

    return changeEntries;  // Return an object with the keys, old, and new values
};

</script>

<template>
    <div class="flex flex-col gap-6 items-center self-stretch py-4 md:py-6 md:gap-8">
        <!-- Accordion to display order notes -->
        <Accordion multiple class="w-full max-h-[600px] overflow-auto">
            <div v-if="isLoading" class="animate-pulse flex flex-col items-start gap-1.5 self-stretch">
                <div class="h-20 bg-gray-200 rounded-xl w-full my-2 md:my-3"></div>
            </div>
            <AccordionPanel
                v-else
                v-for="(note, index) in accountManagerProfileActionHistories" 
                :key="note.id" 
                :value="index"
            >
                <div class="w-full flex gap-2">
                    <!-- dot on the top-left -->
                    <div class="flex flex-col items-center">
                        <div 
                            class="w-3 h-3 rounded-full grow-0 shrink-0"
                            :class="`bg-info-500`"
                        >
                        </div>
                        <div class="w-0.5 h-full bg-gray-200 dark:bg-gray-500 "></div>
                    </div>
                    
                    <div
                        class="w-full flex flex-col border-b-gray-200 dark:border-b-gray-500"
                        :class="{
                            'py-2 border-b': index !== 0 && index !== accountManagerProfileActionHistories.length - 1,
                            'pb-2 border-b': index === 0,
                            'pt-2': index === accountManagerProfileActionHistories.length - 1
                        }"
                    >
                        <div class="flex flex-col items-center pt-2 gap-1">
                            <h3 class="self-stretch text-gray-950 dark:text-gray-100 font-semibold">{{ `[${$t('public.system')}]: ` + (note.action === 0 ? 'Newly created' : 'Updated') }}</h3>
                            <span class="self-stretch text-sm text-gray-500 dark:text-gray-400">{{ note.created_at }}</span>
                        </div>
                        <AccordionHeader class="px-2 text-start">{{ `${$t('public.note_details')}&nbsp;(${note.id})` }}</AccordionHeader>
                        <AccordionContent>
                            <div class="w-full flex flex-col items-center p-2">
                                <!-- Loop through all changes dynamically -->
                                <div v-for="(change, key) in extractChanges(note.changes)" :key="key" class="w-full flex flex-col items-start justify-center gap-1">
                                    <span class="text-gray-950 dark:text-gray-100">
                                        ◉ [{{ $t('public.' + key) }}]
                                    </span>

                                    <span class="truncate text-gray-500 dark:text-gray-300 font-semibold ml-4">
                                        <!-- Use dynamic translation with correct keys and values -->
                                        ➤ {{ $t('public.change_message', { key: $t('public.' + key), old: `"${formatLogChanges(change.old)}"`, new: `"${formatLogChanges(change.new)}"`  }) }}
                                    </span>
                                </div>
                            </div>
                        </AccordionContent>
                    </div>
                </div>
            </AccordionPanel>
        </Accordion>
        <div v-if="!isLoading && accountManagerProfileActionHistories?.length <= 0">
            <Empty :title="$t('public.empty_action_history_title')" :message="$t('public.empty_action_history_message')" />
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

