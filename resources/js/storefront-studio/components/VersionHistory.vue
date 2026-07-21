<script setup>
import { useStudioStore } from '../store/studio';
import VersionHistoryItem from './VersionHistoryItem.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);
const store = useStudioStore();

async function handleRollback(versionId) {
    await store.rollback(versionId);
    emit('close');
}
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Version History & Snapshots</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="$emit('close')">✕</button>
            </div>

            <div class="mt-4 max-h-96 overflow-y-auto space-y-2.5 pr-1">
                <VersionHistoryItem
                    v-for="v in store.versions"
                    :key="v.id"
                    :version="v"
                    @rollback="handleRollback"
                />
                <div v-if="!store.versions.length" class="py-8 text-center text-xs text-slate-400">
                    No published versions yet.
                </div>
            </div>
        </div>
    </div>
</template>
