<script setup>
import { ref } from 'vue';

const emit = defineEmits(['upload']);
const isDragging = ref(false);
const fileInput = ref(null);

function triggerFileInput() {
    fileInput.value?.click();
}

function handleFileSelect(e) {
    const files = e.target.files;
    if (files.length) {
        emit('upload', files[0]);
    }
}

function handleDrop(e) {
    isDragging.value = false;
    const files = e.dataTransfer.files;
    if (files.length) {
        emit('upload', files[0]);
    }
}
</script>

<template>
    <div
        class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-6 text-center transition cursor-pointer"
        :class="isDragging ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-slate-300 bg-slate-50/50 hover:border-slate-400 dark:border-slate-700 dark:bg-slate-800/50'"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
        @click="triggerFileInput"
    >
        <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleFileSelect" />
        <i class="fas fa-cloud-arrow-up text-2xl text-indigo-500 mb-2"></i>
        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
            Click or drag & drop image file to upload
        </p>
        <p class="text-[10px] text-slate-400 mt-1">PNG, JPG, WEBP, SVG up to 5MB</p>
    </div>
</template>
