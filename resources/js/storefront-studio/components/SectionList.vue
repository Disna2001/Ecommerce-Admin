<script setup>
import { ref } from 'vue';
import { useStudioStore } from '../store/studio';
import SectionListItem from './SectionListItem.vue';
import AddSectionModal from './AddSectionModal.vue';

const store = useStudioStore();
const showAddModal = ref(false);
</script>

<template>
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between border-b border-slate-200 p-4 dark:border-slate-700">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                Sections
            </h3>
            <button
                type="button"
                class="inline-flex items-center gap-1 rounded-xl bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700"
                @click="showAddModal = true"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Add</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2">
            <SectionListItem
                v-for="(sec, idx) in store.sections"
                :key="sec.id"
                :section="sec"
                :index="idx"
                :total="store.sections.length"
            />

            <div v-if="!store.sections.length" class="py-12 text-center text-xs text-slate-400">
                No sections yet. Add one to begin.
            </div>
        </div>

        <AddSectionModal :open="showAddModal" @close="showAddModal = false" />
    </div>
</template>
