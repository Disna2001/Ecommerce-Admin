<script setup>
import { ref } from 'vue';

const props = defineProps({
    version: { type: Object, required: true },
});

const emit = defineEmits(['rollback']);

const isBusy = ref(false);

async function onRollback() {
    isBusy.value = true;
    try {
        await emit('rollback', props.version.id);
    } finally {
        isBusy.value = false;
    }
}
</script>

<template>
    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="space-y-0.5">
            <div class="flex items-center gap-2">
                <span class="text-xs font-black text-slate-900 dark:text-white">v{{ version.id }}</span>
                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                    {{ version.status }}
                </span>
            </div>
            <p v-if="version.note" class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ version.note }}</p>
            <p class="text-[10px] text-slate-400">
                {{ version.published_at ? new Date(version.published_at).toLocaleString() : 'N/A' }}
            </p>
        </div>

        <button
            type="button"
            class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600"
            :disabled="isBusy"
            @click="onRollback"
        >
            <i v-if="isBusy" class="fas fa-spinner fa-spin mr-1"></i>
            Rollback
        </button>
    </div>
</template>
