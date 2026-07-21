<script setup>
import { useStudioStore } from '../store/studio';

const store = useStudioStore();
</script>

<template>
    <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 p-1 dark:border-slate-700 dark:bg-slate-800">
        <button
            type="button"
            class="flex h-7 w-8 items-center justify-center rounded-lg text-xs font-bold transition"
            :class="store.viewport === 'desktop' ? 'bg-white text-indigo-600 shadow-sm dark:bg-slate-700 dark:text-indigo-400' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
            title="Desktop view"
            @click="store.viewport = 'desktop'"
        >
            <i class="fas fa-desktop"></i>
        </button>
        <button
            type="button"
            class="flex h-7 w-8 items-center justify-center rounded-lg text-xs font-bold transition"
            :class="store.viewport === 'tablet' ? 'bg-white text-indigo-600 shadow-sm dark:bg-slate-700 dark:text-indigo-400' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
            title="Tablet view"
            @click="store.viewport = 'tablet'"
        >
            <i class="fas fa-tablet-screen-button"></i>
        </button>
        <button
            type="button"
            class="flex h-7 w-8 items-center justify-center rounded-lg text-xs font-bold transition"
            :class="store.viewport === 'mobile' ? 'bg-white text-indigo-600 shadow-sm dark:bg-slate-700 dark:text-indigo-400' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
            title="Mobile view"
            @click="store.viewport = 'mobile'"
        >
            <i class="fas fa-mobile-screen"></i>
        </button>
    </div>
</template>
