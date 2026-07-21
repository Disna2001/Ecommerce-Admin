<script setup>
defineProps({
    assets: { type: Array, default: () => [] },
    selectedPath: { type: String, default: '' },
});

defineEmits(['select', 'delete']);
</script>

<template>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
        <div
            v-for="asset in assets"
            :key="asset.id"
            class="group relative aspect-square overflow-hidden rounded-xl border cursor-pointer transition"
            :class="selectedPath === asset.path ? 'border-indigo-500 ring-2 ring-indigo-500' : 'border-slate-200 dark:border-slate-700'"
            @click="$emit('select', asset)"
        >
            <img :src="asset.path" :alt="asset.name" class="h-full w-full object-cover" />

            <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity p-2 flex flex-col justify-between text-white">
                <p class="text-[10px] font-bold truncate">{{ asset.name }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-mono text-slate-300">{{ asset.width && asset.height ? `${asset.width}x${asset.height}` : '' }}</span>
                    <button
                        type="button"
                        class="rounded bg-rose-600 p-1 text-[10px] hover:bg-rose-700"
                        title="Delete asset"
                        @click.stop="$emit('delete', asset.id)"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
