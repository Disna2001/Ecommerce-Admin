<script setup>
defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, required: true },
    message: { type: String, default: '' },
    confirmLabel: { type: String, default: 'Confirm' },
    danger: { type: Boolean, default: false },
    busy: { type: Boolean, default: false },
});

defineEmits(['confirm', 'cancel']);
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
            <h3 class="text-base font-black text-slate-900 dark:text-white">{{ title }}</h3>
            <p v-if="message" class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ message }}</p>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700"
                    :disabled="busy"
                    @click="$emit('cancel')"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="rounded-xl px-4 py-2 text-xs font-bold text-white transition"
                    :class="danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-indigo-600 hover:bg-indigo-700'"
                    :disabled="busy"
                    @click="$emit('confirm')"
                >
                    <i v-if="busy" class="fas fa-spinner fa-spin mr-1"></i>
                    {{ confirmLabel }}
                </button>
            </div>
        </div>
    </div>
</template>
