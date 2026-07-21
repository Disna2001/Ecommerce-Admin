<script setup>
import { ref } from 'vue';
import { useStudioStore } from '../store/studio';
import ViewportToggle from './ViewportToggle.vue';
import UnsavedChangesBadge from './UnsavedChangesBadge.vue';
import PublishDialog from './PublishDialog.vue';
import VersionHistory from './VersionHistory.vue';
import ConfirmDialog from './ConfirmDialog.vue';
import PageSwitcher from './PageSwitcher.vue';

const store = useStudioStore();

const showPublish = ref(false);
const showHistory = ref(false);
const showDiscardConfirm = ref(false);

async function confirmDiscard() {
    showDiscardConfirm.value = false;
    await store.discardDraft();
}
</script>

<template>
    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-2.5 dark:border-slate-700 bg-white dark:bg-slate-800">
        <div class="flex items-center gap-3">
            <h1 class="text-sm font-black text-slate-800 dark:text-slate-100">Storefront Studio</h1>
            <PageSwitcher />
            <UnsavedChangesBadge />
        </div>

        <div class="flex items-center gap-3">
            <ViewportToggle />

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700"
                    :disabled="store.isDiscarding"
                    @click="showDiscardConfirm = true"
                >
                    <i v-if="store.isDiscarding" class="fas fa-spinner fa-spin mr-1"></i>
                    Discard
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-indigo-700"
                    :disabled="store.isPublishing"
                    @click="showPublish = true"
                >
                    <i v-if="store.isPublishing" class="fas fa-spinner fa-spin mr-1"></i>
                    Publish
                </button>
                <button
                    type="button"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700"
                    title="Version history"
                    @click="showHistory = true"
                >
                    <i class="fas fa-clock-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <PublishDialog :open="showPublish" @close="showPublish = false" />
    <VersionHistory :open="showHistory" @close="showHistory = false" />

    <ConfirmDialog
        :open="showDiscardConfirm"
        title="Discard all changes since last publish?"
        message="This cannot be undone. Your unpublished draft edits will be reverted to the last published version."
        confirm-label="Discard"
        danger
        :busy="store.isDiscarding"
        @confirm="confirmDiscard"
        @cancel="showDiscardConfirm = false"
    />
</template>
