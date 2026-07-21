<script setup>
import { ref, onMounted, watch } from 'vue';
import client from '../api/client';
import { reportError } from '../errorReporter';
import MediaUploadDropzone from './MediaUploadDropzone.vue';
import MediaLibraryGrid from './MediaLibraryGrid.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    currentValue: { type: String, default: '' },
});

const emit = defineEmits(['close', 'select']);

const assets = ref([]);
const loading = ref(false);
const isUploading = ref(false);

async function loadAssets() {
    loading.value = true;
    try {
        const { data } = await client.get('/media');
        assets.value = data;
    } catch (e) {
        reportError('Failed to load media assets.', e?.message ?? String(e));
    } finally {
        loading.value = false;
    }
}

async function handleUpload(file) {
    isUploading.value = true;
    try {
        const formData = new FormData();
        formData.append('file', file);
        const { data } = await client.post('/media', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        assets.value.unshift(data);
        emit('select', data.path);
        emit('close');
    } catch (e) {
        reportError('Failed to upload image.', e?.message ?? String(e));
    } finally {
        isUploading.value = false;
    }
}

async function handleDelete(id) {
    if (!confirm('Delete this media asset? Note: it may still be referenced in existing section configurations.')) {
        return;
    }
    try {
        await client.delete(`/media/${id}`);
        assets.value = assets.value.filter((a) => a.id !== id);
    } catch (e) {
        reportError('Failed to delete media asset.', e?.message ?? String(e));
    }
}

watch(
    () => props.open,
    (val) => {
        if (val) loadAssets();
    }
);
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800 flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Media Library Picker</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="$emit('close')">✕</button>
            </div>

            <div class="py-4 border-b border-slate-200 dark:border-slate-700">
                <MediaUploadDropzone @upload="handleUpload" />
            </div>

            <div class="flex-1 overflow-y-auto py-4">
                <MediaLibraryGrid
                    :assets="assets"
                    :selected-path="currentValue"
                    @select="(asset) => { $emit('select', asset.path); $emit('close'); }"
                    @delete="handleDelete"
                />
                <div v-if="!assets.length && !loading" class="py-8 text-center text-xs text-slate-400">
                    No images uploaded yet. Drag an image above to upload.
                </div>
            </div>
        </div>
    </div>
</template>
