<script setup>
import { ref } from 'vue';
import MediaLibraryModal from '../MediaLibraryModal.vue';

defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, required: true },
});

const emit = defineEmits(['update:modelValue']);
const showMediaModal = ref(false);
</script>

<template>
    <div class="space-y-1">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            {{ label }}
        </label>
        <div class="flex items-center gap-2">
            <input
                type="text"
                :value="modelValue"
                placeholder="Image URL or media path"
                class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                @input="$emit('update:modelValue', $event.target.value)"
            />
            <button
                type="button"
                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                @click="showMediaModal = true"
            >
                Browse
            </button>
        </div>
        <div v-if="modelValue" class="mt-2 relative h-20 w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-900">
            <img :src="modelValue" class="h-full w-full object-cover" alt="Preview" />
        </div>

        <MediaLibraryModal
            :open="showMediaModal"
            :current-value="modelValue"
            @close="showMediaModal = false"
            @select="$emit('update:modelValue', $event)"
        />
    </div>
</template>
