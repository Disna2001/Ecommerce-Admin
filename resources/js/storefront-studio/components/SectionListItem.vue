<script setup>
import { useStudioStore } from '../store/studio';

const props = defineProps({
    section: { type: Object, required: true },
    index: { type: Number, required: true },
    total: { type: Number, required: true },
});

const store = useStudioStore();

function moveUp() {
    if (props.index === 0) return;
    const ids = store.sections.map((s) => s.id);
    const temp = ids[props.index];
    ids[props.index] = ids[props.index - 1];
    ids[props.index - 1] = temp;
    store.reorderSections(ids);
}

function moveDown() {
    if (props.index === props.total - 1) return;
    const ids = store.sections.map((s) => s.id);
    const temp = ids[props.index];
    ids[props.index] = ids[props.index + 1];
    ids[props.index + 1] = temp;
    store.reorderSections(ids);
}
</script>

<template>
    <div
        class="group flex items-center justify-between rounded-xl border p-3 transition cursor-pointer"
        :class="store.selectedSectionId === section.id
            ? 'border-indigo-500 bg-indigo-50/50 dark:border-indigo-500 dark:bg-indigo-950/30'
            : 'border-slate-200 bg-white hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-slate-600'"
        @click="store.selectSection(section.id)"
    >
        <div class="flex items-center gap-3 min-w-0">
            <div class="flex flex-col gap-0.5 text-slate-300 group-hover:text-slate-400">
                <i class="fas fa-grip-vertical text-xs"></i>
            </div>
            <div class="truncate space-y-0.5">
                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">
                    {{ store.registry[section.type]?.label || section.type }}
                </p>
                <p class="text-[10px] text-slate-400 font-mono">
                    #{{ section.id }} · {{ section.slot }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100" @click.stop>
            <button
                type="button"
                class="h-6 w-6 rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-700 dark:hover:text-slate-200 disabled:opacity-30"
                :disabled="index === 0"
                title="Move Up"
                @click="moveUp"
            >
                <i class="fas fa-chevron-up text-[10px]"></i>
            </button>
            <button
                type="button"
                class="h-6 w-6 rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-700 dark:hover:text-slate-200 disabled:opacity-30"
                :disabled="index === total - 1"
                title="Move Down"
                @click="moveDown"
            >
                <i class="fas fa-chevron-down text-[10px]"></i>
            </button>
            <button
                type="button"
                class="h-6 w-6 rounded text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400"
                :title="section.is_active ? 'Hide Section' : 'Show Section'"
                @click="store.toggleSection(section.id, !section.is_active)"
            >
                <i class="fas" :class="section.is_active ? 'fa-eye text-indigo-500' : 'fa-eye-slash text-slate-300'"></i>
            </button>
            <button
                type="button"
                class="h-6 w-6 rounded text-slate-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950 dark:hover:text-rose-400"
                title="Delete Section"
                @click="store.deleteSection(section.id)"
            >
                <i class="fas fa-trash text-[10px]"></i>
            </button>
        </div>
    </div>
</template>
