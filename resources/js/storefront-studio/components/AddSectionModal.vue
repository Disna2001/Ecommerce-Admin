<script setup>
import { useStudioStore } from '../store/studio';

const props = defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);
const store = useStudioStore();

async function handleAdd(type) {
    await store.addSection(type);
    emit('close');
}
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Add New Section</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="$emit('close')">✕</button>
            </div>

            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Choose a section type from the registry to append to {{ store.pageKey }}.
            </p>

            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <button
                    v-for="(item, key) in store.registry"
                    :key="key"
                    type="button"
                    class="flex flex-col gap-1 rounded-xl border border-slate-200 bg-slate-50/50 p-4 text-left hover:border-indigo-500 hover:bg-indigo-50/30 dark:border-slate-700 dark:bg-slate-800/50 dark:hover:border-indigo-500"
                    @click="handleAdd(key)"
                >
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ item.label }}</span>
                    <span class="text-[10px] font-mono text-slate-400">type: {{ key }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
