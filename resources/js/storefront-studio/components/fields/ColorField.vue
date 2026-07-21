<script setup>
defineProps({
    modelValue: { type: String, default: '#000000' },
    label: { type: String, required: true },
});
defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-1">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            {{ label }}
        </label>
        <div class="flex items-center gap-2">
            <input
                type="color"
                :value="modelValue"
                class="h-10 w-12 cursor-pointer rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-slate-700 dark:bg-slate-800"
                @input="$emit('update:modelValue', $event.target.value)"
            />
            <input
                type="text"
                :value="modelValue"
                class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-mono text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </div>
    </div>
</template>
