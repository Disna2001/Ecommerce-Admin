<script setup>
defineProps({
    modelValue: { type: [String, Number], default: '' },
    label: { type: String, required: true },
});
defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-1">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            {{ label }}
        </label>
        <input
            type="text"
            :value="modelValue"
            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </div>
</template>
