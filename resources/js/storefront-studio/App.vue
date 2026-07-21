<script setup>
import { onMounted } from 'vue';
import { useStudioStore } from './store/studio';
import { reportError } from './errorReporter';
import StudioTabs from './components/StudioTabs.vue';
import ErrorBanner from './components/ErrorBanner.vue';

const store = useStudioStore();

onMounted(async () => {
    try {
        await store.loadRegistry();
        await store.loadSections();
        await store.fetchVersions();
        await store.checkUnpublishedChanges();
    } catch (e) {
        reportError('Failed to load the studio.', e?.message ?? String(e));
    }
});
</script>

<template>
    <StudioTabs />
    <ErrorBanner />
</template>
