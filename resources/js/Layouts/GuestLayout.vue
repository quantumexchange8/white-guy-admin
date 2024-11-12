<script setup>
import { isDark, toggleDarkMode, sidebarState } from '@/Composables'
import { IconWorld, IconMoonFilled, IconSunFilled } from '@tabler/icons-vue';
import { ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { loadLanguageAsync } from "laravel-vue-i18n";
import TieredMenu from "primevue/tieredmenu";
import dayjs from "dayjs";
import Button from '@/Components/Button.vue'

defineProps({
    title: String,
})

const menu = ref(null);
const toggle = (event) => {
    menu.value.toggle(event);
};

const currentLocale = ref(usePage().props.locale);
const locales = [
    {'label': 'English', 'value': 'en'},
    {'label': '简体中文', 'value': 'cn'},
    {'label': '繁體中文', 'value': 'tw'},
];

const changeLanguage = async (langVal) => {
    try {
        currentLocale.value = langVal;
        await loadLanguageAsync(langVal);
        await axios.get(`/locale/${langVal}`);
    } catch (error) {
        console.error('Error changing locale:', error);
    }
};
</script>

<template>
    <Head :title="title"></Head>

    <div class="flex flex-col min-h-screen bg-gray-25 dark:bg-gray-900">
        <div class="flex py-3 px-5 md:px-10 justify-end items-center">
            <Button
                iconOnly
                variant="gray-text"
                type="button"
                pill=""
                @click="() => { toggleDarkMode() }"
                v-slot="{ iconSizeClasses }"
                srText="Toggle dark mode"
            >
                <IconMoonFilled v-show="!isDark" size="20" color="#4B6A9B" stroke-width="1.25" />
                <IconSunFilled v-show="isDark" size="20" color="#FFD700" stroke-width="1.25" />
            </Button>

            <div
                class="w-[60px] h-[60px] p-[17.5px] flex items-center justify-center rounded-full hover:cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-white"
                @click="toggle"
            >
                <IconWorld size="25" stroke-width="1.25" />
            </div>
        </div>
        <div class="flex flex-1 flex-col justify-center items-center pb-12 md:px-8 xs:gap-y-[60px]">
            <div class="w-full flex md:flex-1 justify-center">
                <div class="w-full max-w-xs md:max-w-none md:w-[360px] flex flex-col justify-center items-center mx-5">
                    <slot />
                </div>
            </div>
            <div class="text-center text-gray-500 text-xs mt-auto">© {{ dayjs().year() }} White Guy All rights reserved.</div>
        </div>
    </div>

    <TieredMenu ref="menu" id="overlay_tmenu" :model="locales" popup>
        <template #item="{ item, props }">
            <div
                class="flex items-center gap-3 self-stretch text-gray-700"
                :class="{'bg-primary-100 text-primary-600': item.value === currentLocale}"
                v-bind="props.action"
                @click="changeLanguage(item.value)"
            >
                {{ item.label }}
            </div>
        </template>
    </TieredMenu>
</template>
