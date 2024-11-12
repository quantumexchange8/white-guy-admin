import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.{vue,js,ts,jsx,tsx}',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: 'rgb(var(--primary))',
                'primary-inverse': 'rgb(var(--primary-inverse))',
                'primary-hover': 'rgb(var(--primary-hover))',
                'primary-active-color': 'rgb(var(--primary-active-color))',

                'primary-highlight': 'rgb(var(--primary)/var(--primary-highlight-opacity))',
                'primary-highlight-inverse': 'rgb(var(--primary-highlight-inverse))',
                'primary-highlight-hover': 'rgb(var(--primary)/var(--primary-highlight-hover-opacity))',

                'primary-25': 'rgb(var(--primary-25))',
                'primary-50': 'rgb(var(--primary-50))',
                'primary-100': 'rgb(var(--primary-100))',
                'primary-200': 'rgb(var(--primary-200))',
                'primary-300': 'rgb(var(--primary-300))',
                'primary-400': 'rgb(var(--primary-400))',
                'primary-500': 'rgb(var(--primary-500))',
                'primary-600': 'rgb(var(--primary-600))',
                'primary-700': 'rgb(var(--primary-700))',
                'primary-800': 'rgb(var(--primary-800))',
                'primary-900': 'rgb(var(--primary-900))',
                'primary-950': 'rgb(var(--primary-950))',

                'surface-0': 'rgb(var(--surface-0))',
                'surface-50': 'rgb(var(--surface-50))',
                'surface-100': 'rgb(var(--surface-100))',
                'surface-200': 'rgb(var(--surface-200))',
                'surface-300': 'rgb(var(--surface-300))',
                'surface-400': 'rgb(var(--surface-400))',
                'surface-500': 'rgb(var(--surface-500))',
                'surface-600': 'rgb(var(--surface-600))',
                'surface-700': 'rgb(var(--surface-700))',
                'surface-800': 'rgb(var(--surface-800))',
                'surface-900': 'rgb(var(--surface-900))',
                'surface-950': 'rgb(var(--surface-950))',

                gray: {
                    25: '#FCFCFD',
                    50: '#F9FAFB',
                    100: '#F2F4F7',
                    200: '#EAECF0',
                    300: '#D0D5DD',
                    400: '#98A2B3',
                    500: '#667085',
                    600: '#475467',
                    700: '#344054',
                    800: '#182230',
                    900: '#101828',
                    950: '#0C111D',
                },
                success: {
                    25: '#F6FEF9',
                    50: '#ECFDF3',
                    100: '#DCFAE6',
                    200: '#ABEFC6',
                    300: '#75E0A7',
                    400: '#47CD89',
                    500: '#17B26A',
                    600: '#079455',
                    700: '#067647',
                    800: '#085D3A',
                    900: '#074D31',
                    950: '#053321',
                },
                info: {
                    25: '#F5FBFF',
                    50: '#F0F9FF',
                    100: '#E0F2FE',
                    200: '#B9E6FE',
                    300: '#7CD4FD',
                    400: '#36BFFA',
                    500: '#0BA5EC',
                    600: '#0086C9',
                    700: '#026AA2',
                    800: '#065986',
                    900: '#0B4A6F',
                    950: '#062C41',
                },
                warning: {
                    25: '#FFFCF5',
                    50: '#FFFAEB',
                    100: '#FEF0C7',
                    200: '#FEDF89',
                    300: '#FEC84B',
                    400: '#FDB022',
                    500: '#F79009',
                    600: '#DC6803',
                    700: '#B54708',
                    800: '#93370D',
                    900: '#7A2E0E',
                    950: '#4E1D09',
                },
                error: {
                    25: '#FFFBFA',
                    50: '#FEF3F2',
                    100: '#FEE4E2',
                    200: '#FECDCA',
                    300: '#FDA29B',
                    400: '#F97066',
                    500: '#F04438',
                    600: '#D92D20',
                    700: '#B42318',
                    800: '#912018',
                    900: '#7A271A',
                    950: '#55160C',
                },
                logo: '#9D4EDD',
                indigo: '#424EF0',
                turquoise: '#41EAD4',
                green: '#06D001',
                jonquil: '#ECC30B',
                orange: '#F26419',
                pink: '#FF2D58',
                purple: '#7A5AF8',
                cyan: '#00BCD4',
                teal: '#009688',
                cerisa_red: '#E91E63',
                violet: '#9C27B0',
            },
            boxShadow: {
              'input': '0 1px 2px 0px rgba(12, 17, 29, 0.05)',
              'dialog': '0 12px 24px -4px rgba(12, 17, 29, 0.10)',
              'toast': '0 4px 20px 0 rgba(12, 17, 29, 0.08)',
              'box': '0 8px 16px -4px rgba(12, 17, 29, 0.08)',
              'table': '0 2px 8px 0 rgba(12, 17, 29, 0.05)',
              'dropdown': '0px 8px 16px -4px rgba(12, 17, 29, 0.08)',
            },
            fontSize: {
                xxs: ['10px', '16px'],
                xs: ['12px', '18px'],
                sm: ['14px', '20px'],
                base: ['16px', '24px'],
                lg: ['20px', '28px'],
                xl: ['24px', '32px'],
                xxl: ['30px', '42px'],
            },
            screens: {
                'xxs': '320px',
                'xs': '450px',
                'sm': '640px',
                'md': '768px',
                'lg': '1024px',
                'xl': '1280px',
                '2xl': '1536px',
                '3xl': '1792px',
            },
            typography: ({ theme }) => ({
              DEFAULT: {
                css: {
                  '--tw-prose-paragraphs': theme('colors.gray[700]'),
                  '--tw-prose-headings': theme('colors.gray[950]'),
                  '--tw-prose-counters': theme('colors.gray[300]'),
                  '--tw-prose-bold': theme('colors.gray[950]'),
                  '--tw-prose-bullets': theme('colors.gray[500]'),
                  h1: {
                    fontSize: '30px',
                    lineHeight: '42px'
                  },
                  h2: {
                    fontSize: '24px',
                    lineHeight: '32px'
                  },
                  h3: {
                    fontSize: '20px',
                    lineHeight: '28px'
                  },
                  p: {
                    fontSize: '16px',
                    lineHeight: '24px'
                  },
                  a: {
                    color: theme('colors.primary'),
                    textDecoration: 'underline',
                    '&:hover': {
                      color: theme('colors.primary-hover'),
                    }
                  },
                }
              }
            }),
            chip: 'inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white shadow-input hover:bg-gray-50 focus:border-primary-300 focus:bg-primary-50 opacity-50 cursor-not-allowed',
        },
    },

    plugins: [forms, typography],
};
