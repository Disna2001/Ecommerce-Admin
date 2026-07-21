<script setup>
import { errors, dismissError } from '../errorReporter';
</script>

<template>
    <div v-if="errors.length" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-md w-full px-4">
        <div
            v-for="err in errors"
            :key="err.id"
            class="flex items-start justify-between gap-3 rounded-2xl bg-rose-600 p-4 text-white shadow-2xl transition"
        >
            <div class="space-y-0.5 min-w-0 flex-1">
                <p class="text-xs font-black">{{ err.message }}</p>
                <p v-if="err.detail" class="text-[11px] text-rose-100 opacity-90 break-words font-mono">
                    {{ err.detail }}
                </p>
            </div>
            <button
                type="button"
                class="rounded-lg p-1 text-rose-200 hover:bg-rose-700 hover:text-white transition"
                @click="dismissError(err.id)"
            >
                ✕
            </button>
        </div>
    </div>
</template>
