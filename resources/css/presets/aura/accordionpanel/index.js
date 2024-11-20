export default {
    root: {
        class: ['flex', 'flex-col',],
    },
    header: {
        class: [
            'cursor-pointer', 'disabled:cursor-auto', 'flex', 'items-center', 'justify-between', 
            'p-[1.125rem]', 'font-semibold', 'bg-surface-0', 'dark:bg-surface-900',
            'text-surface-500', 'dark:text-surface-400', 'transition-colors', 'duration-200'
        ],
    },
    headerAction: {
        class: ['text-surface-500', 'dark:text-surface-400']
    },
    headerIcon: {
        class: ['text-surface-500', 'dark:text-surface-400']
    },
    headerTitle: {
        class: ['font-semibold']
    },
    toggleableContent: {
        class: ['bg-surface-0', 'dark:bg-surface-900', 'text-surface-700', 'dark:text-surface-0', 'pt-0', 'px-[1.125rem]', 'pb-[1.125rem]']
    },
    content: {
        class: ['bg-surface-0', 'dark:bg-surface-900', 'text-surface-700', 'dark:text-surface-0', 'pt-0', 'px-[1.125rem]', 'pb-[1.125rem]']
    },
    transition: {
        class: ['duration-200', 'ease-in-out']
    }
};
