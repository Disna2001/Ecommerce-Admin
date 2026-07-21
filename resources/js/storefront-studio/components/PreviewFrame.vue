<script setup>
import { onMounted, ref, computed, watch } from 'vue';
import { useStudioStore } from '../store/studio';
import morphdom from 'morphdom';
import client from '../api/client';

const store = useStudioStore();
const iframeRef = ref(null);
const patchWarned = ref(false);

const viewportWidthClass = computed(() => {
    if (store.viewport === 'mobile') return 'w-[375px] rounded-[2.5rem] border-[8px] border-slate-800 shadow-2xl h-[700px] my-auto';
    if (store.viewport === 'tablet') return 'w-[768px] rounded-[2rem] border-[8px] border-slate-800 shadow-2xl h-[800px] my-auto';
    return 'w-full h-full border-none';
});

async function patchSectionFragment(sectionId) {
    try {
        const iframe = iframeRef.value;
        if (!iframe || !iframe.contentDocument) {
            store.bumpCacheBuster();
            return;
        }

        const doc = iframe.contentDocument;
        const targetNode = doc.querySelector(`[data-section-id="${sectionId}"]`);
        if (!targetNode) {
            store.bumpCacheBuster();
            return;
        }

        const { data: rawHtml } = await client.get(`/sections/${sectionId}/fragment`, {
            responseType: 'text',
        });

        const tempContainer = doc.createElement('div');
        tempContainer.innerHTML = rawHtml.trim();
        const newSectionNode = tempContainer.querySelector(`[data-section-id="${sectionId}"]`) || tempContainer.firstElementChild;

        if (newSectionNode) {
            morphdom(targetNode, newSectionNode);
        } else {
            store.bumpCacheBuster();
        }
    } catch (e) {
        if (!patchWarned.value) {
            console.warn('[PreviewFrame] Fragment patch failed, falling back to full reload:', e);
            patchWarned.value = true;
        }
        store.bumpCacheBuster();
    }
}

onMounted(async () => {
    await store.refreshPreviewToken();
});
</script>

<template>
    <div class="flex h-full w-full flex-col items-center justify-center bg-slate-100 p-4 dark:bg-slate-950 overflow-hidden relative">
        <div v-if="store.loading" class="absolute inset-0 z-10 flex items-center justify-center bg-slate-900/10 backdrop-blur-[1px]">
            <div class="rounded-xl bg-white p-3 shadow-lg dark:bg-slate-800">
                <i class="fas fa-spinner fa-spin text-indigo-600"></i>
            </div>
        </div>

        <iframe
            v-if="store.previewUrl"
            ref="iframeRef"
            :src="store.buildPreviewUrl()"
            :class="viewportWidthClass"
            class="transition-all duration-300 bg-white"
        ></iframe>
    </div>
</template>
