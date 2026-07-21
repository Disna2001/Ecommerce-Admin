<script setup>
import { ref, watch } from 'vue';
import { useStudioStore } from '../store/studio';
import { useDebounceFn } from '@vueuse/core';
import TextField from './fields/TextField.vue';
import ColorField from './fields/ColorField.vue';
import ImageField from './fields/ImageField.vue';
import RepeaterField from './fields/RepeaterField.vue';

const store = useStudioStore();
const configState = ref({});

watch(
    () => store.selectedSection,
    (newSec) => {
        if (newSec) {
            configState.value = JSON.parse(JSON.stringify(newSec.config || {}));
        } else {
            configState.value = {};
        }
    },
    { immediate: true, deep: true }
);

const debouncedSave = useDebounceFn((newConfig) => {
    if (store.selectedSectionId) {
        store.updateSectionConfig(store.selectedSectionId, newConfig);
    }
}, 400);

function updateField(fieldName, value) {
    configState.value[fieldName] = value;
    debouncedSave(configState.value);
}
</script>

<template>
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-200 p-4 dark:border-slate-700">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                Section Configuration
            </h3>
            <p v-if="store.selectedSection" class="mt-0.5 text-[10px] text-slate-400">
                Editing #{{ store.selectedSection.id }} ({{ store.selectedSection.type }})
            </p>
        </div>

        <div v-if="store.selectedSection" class="flex-1 overflow-y-auto p-4 space-y-4">
            <div v-for="field in store.selectedSchema" :key="field.name">
                <TextField
                    v-if="field.type === 'text'"
                    :model-value="configState[field.name]"
                    :label="field.label"
                    @update:model-value="updateField(field.name, $event)"
                />
                <ColorField
                    v-else-if="field.type === 'color'"
                    :model-value="configState[field.name]"
                    :label="field.label"
                    @update:model-value="updateField(field.name, $event)"
                />
                <ImageField
                    v-else-if="field.type === 'image'"
                    :model-value="configState[field.name]"
                    :label="field.label"
                    @update:model-value="updateField(field.name, $event)"
                />
                <RepeaterField
                    v-else-if="field.type === 'repeater'"
                    :model-value="configState[field.name]"
                    :label="field.label"
                    :fields="field.fields"
                    @update:model-value="updateField(field.name, $event)"
                />
            </div>
        </div>

        <div v-else class="flex flex-1 items-center justify-center p-6 text-center text-xs text-slate-400">
            Select a section from the list to edit its properties.
        </div>
    </div>
</template>
