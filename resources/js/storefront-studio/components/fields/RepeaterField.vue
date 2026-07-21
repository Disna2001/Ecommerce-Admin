<script setup>
import TextField from './TextField.vue';
import ImageField from './ImageField.vue';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    label: { type: String, required: true },
    fields: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

function addItem() {
    const newItem = {};
    props.fields.forEach((f) => {
        newItem[f.name] = f.default || '';
    });
    emit('update:modelValue', [...props.modelValue, newItem]);
}

function removeItem(index) {
    const list = [...props.modelValue];
    list.splice(index, 1);
    emit('update:modelValue', list);
}

function updateItemField(index, fieldName, val) {
    const list = JSON.parse(JSON.stringify(props.modelValue));
    list[index][fieldName] = val;
    emit('update:modelValue', list);
}
</script>

<template>
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                {{ label }}
            </label>
            <button
                type="button"
                class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300"
                @click="addItem"
            >
                + Add Item
            </button>
        </div>

        <div class="space-y-3">
            <div
                v-for="(item, idx) in modelValue"
                :key="idx"
                class="relative space-y-2 rounded-xl border border-slate-200 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-800/50"
            >
                <button
                    type="button"
                    class="absolute right-2 top-2 text-rose-500 hover:text-rose-700 text-xs font-bold"
                    @click="removeItem(idx)"
                >
                    ✕
                </button>

                <div v-for="field in fields" :key="field.name">
                    <TextField
                        v-if="field.type === 'text'"
                        :model-value="item[field.name]"
                        :label="field.label"
                        @update:model-value="updateItemField(idx, field.name, $event)"
                    />
                    <ImageField
                        v-else-if="field.type === 'image'"
                        :model-value="item[field.name]"
                        :label="field.label"
                        @update:model-value="updateItemField(idx, field.name, $event)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
