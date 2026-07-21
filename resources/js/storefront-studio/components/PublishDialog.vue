<script setup>
import { ref } from 'vue';
import { useStudioStore } from '../store/studio';

const props = defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const store = useStudioStore();
const note = ref('');

async function handlePublish() {
    await store.publish(note.value);
    note.value = '';
    emit('close');
}
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
            <h3 class="text-base font-black text-slate-900 dark:text-white">Publish Storefront Changes</h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                This will save your current section layout and make it live on the public storefront.
            </p>

            <div class="mt-4 space-y-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Release Note (Optional)
                </label>
                <input
                    type="text"
                    v-model="note"
                    placeholder="e.g. Added hero banner & updated promo rail"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700"
                    :disabled="store.isPublishing"
                    @click="$emit('close')"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-700"
                    :disabled="store.isPublishing"
                    @click="handlePublish"
                >
                    <i v-if="store.isPublishing" class="fas fa-spinner fa-spin mr-1"></i>
                    Publish Now
                </button>
            </div>
        </div>
    </div>
</template>
