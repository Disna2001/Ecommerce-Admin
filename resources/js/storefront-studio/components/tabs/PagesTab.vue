<script setup>
import { useStudioStore } from '../../store/studio';
import TopBar from '../TopBar.vue';
import SectionList from '../SectionList.vue';
import PreviewFrame from '../PreviewFrame.vue';
import ConfigPanel from '../ConfigPanel.vue';
import PreviewSampleNotice from '../PreviewSampleNotice.vue';

const store = useStudioStore();
</script>

<template>
    <div class="flex h-full flex-col min-h-0 flex-1">
        <TopBar />
        <PreviewSampleNotice />
        <div class="flex min-h-0 flex-1 overflow-hidden">
            <aside class="w-[280px] shrink-0 border-r border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900 overflow-y-auto">
                <SectionList />
            </aside>

            <main class="min-w-0 flex-1 overflow-y-auto">
                <PreviewFrame />
            </main>

            <aside class="w-[320px] shrink-0 border-l border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800 overflow-y-auto">
                <ConfigPanel />
            </aside>
        </div>
    </div>
</template>
