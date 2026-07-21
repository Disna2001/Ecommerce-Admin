<script setup>
import { useStudioStore } from '../store/studio';

const store = useStudioStore();
</script>

<template>
    <div
        v-if="store.hasUnpublishedChanges"
        class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900/60"
    >
        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
        <span>Unpublished Changes</span>
    </div>
</template>
