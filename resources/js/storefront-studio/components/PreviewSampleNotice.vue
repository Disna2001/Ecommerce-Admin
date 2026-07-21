<script setup>
import { useStudioStore } from '../store/studio';

const store = useStudioStore();
</script>

<template>
    <div
        v-if="store.pageKey !== 'home'"
        class="bg-indigo-50 px-4 py-2 text-center text-xs font-semibold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-b border-indigo-100 dark:border-indigo-900"
    >
        Viewing sample data for page: <span class="font-bold uppercase">{{ store.pageKey }}</span>. Changes affect section slots on this layout.
    </div>
</template>
