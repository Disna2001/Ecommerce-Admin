<script setup>
import { ref, watch } from 'vue';
import client from '../api/client';
import TextField from './fields/TextField.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    review: { type: Object, default: null },
});

const emit = defineEmits(['close', 'updated', 'deleted']);

const busy = ref(false);
const isEditing = ref(false);
const errorMessage = ref('');

const editForm = ref({
    rating: 5,
    title: '',
    body: '',
    is_approved: true,
    is_flagged: false,
});

watch(
    () => props.review,
    (rev) => {
        if (rev) {
            editForm.value = {
                rating: rev.rating || 5,
                title: rev.title || '',
                body: rev.body || '',
                is_approved: rev.is_approved ?? true,
                is_flagged: rev.is_flagged ?? false,
            };
            isEditing.value = false;
            errorMessage.value = '';
        }
    },
    { immediate: true }
);

async function handleApprove() {
    if (!props.review) return;
    busy.value = true;
    try {
        const { data } = await client.post(`/reviews/${props.review.id}/approve`);
        emit('updated', data.data || data);
    } catch (e) {
        errorMessage.value = e.response?.data?.message || 'Failed to approve review.';
    } finally {
        busy.value = false;
    }
}

async function handleReject() {
    if (!props.review) return;
    busy.value = true;
    try {
        const { data } = await client.post(`/reviews/${props.review.id}/reject`);
        emit('updated', data.data || data);
    } catch (e) {
        errorMessage.value = e.response?.data?.message || 'Failed to reject review.';
    } finally {
        busy.value = false;
    }
}

async function handleToggleFlag() {
    if (!props.review) return;
    busy.value = true;
    try {
        const { data } = await client.patch(`/reviews/${props.review.id}/toggle-flag`);
        emit('updated', data.data || data);
    } catch (e) {
        errorMessage.value = e.response?.data?.message || 'Failed to toggle flag.';
    } finally {
        busy.value = false;
    }
}

async function handleSaveEdits() {
    if (!props.review) return;
    busy.value = true;
    errorMessage.value = '';
    try {
        const { data } = await client.put(`/reviews/${props.review.id}`, editForm.value);
        emit('updated', data.data || data);
        isEditing.value = false;
    } catch (e) {
        errorMessage.value = e.response?.data?.message || 'Failed to save review edits.';
    } finally {
        busy.value = false;
    }
}

async function handleDelete() {
    if (!props.review) return;
    if (!confirm(`Are you sure you want to delete this review by ${props.review.user?.name || 'Customer'}?`)) return;
    busy.value = true;
    try {
        await client.delete(`/reviews/${props.review.id}`);
        emit('deleted', props.review.id);
        emit('close');
    } catch (e) {
        errorMessage.value = e.response?.data?.message || 'Failed to delete review.';
    } finally {
        busy.value = false;
    }
}
</script>

<template>
    <div
        v-if="open && review"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
    >
        <div class="w-full max-w-xl rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-800 my-8">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-white font-black text-xs dark:bg-indigo-600">
                        {{ (review.user?.name || 'V').substring(0, 1).toUpperCase() }}
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">
                            {{ review.user?.name || 'Verified Customer' }}
                        </h3>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            {{ review.user?.email || 'Verified Purchase' }} · #{{ review.id }}
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-700 dark:hover:text-white"
                    @click="emit('close')"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div v-if="errorMessage" class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-400">
                {{ errorMessage }}
            </div>

            <!-- Content Area -->
            <div class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                <!-- Status Badges -->
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        v-if="review.is_approved"
                        class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400"
                    >
                        <i class="fas fa-check-circle text-[8px]"></i> Published on Storefront
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                    >
                        <i class="fas fa-clock text-[8px]"></i> Pending Moderation
                    </span>

                    <span
                        v-if="review.is_flagged"
                        class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-600 dark:bg-rose-950 dark:text-rose-400"
                    >
                        <i class="fas fa-flag text-[8px]"></i> Flagged
                    </span>

                    <span v-if="review.stock" class="rounded-md bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                        Product: {{ review.stock.name }} ({{ review.stock.sku }})
                    </span>
                </div>

                <!-- Read Mode -->
                <div v-if="!isEditing" class="space-y-3 rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-700/60 dark:bg-slate-900/40">
                    <div class="flex items-center justify-between">
                        <div class="flex text-amber-400 text-xs gap-0.5">
                            <i v-for="n in 5" :key="n" class="fas fa-star" :class="n <= review.rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700'"></i>
                        </div>
                        <span class="text-[10px] font-mono text-slate-400">{{ review.created_at?.substring(0, 10) }}</span>
                    </div>

                    <h4 v-if="review.title" class="text-xs font-black text-slate-900 dark:text-white">
                        {{ review.title }}
                    </h4>

                    <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-300 italic">
                        "{{ review.body }}"
                    </p>
                </div>

                <!-- Edit Mode Form -->
                <form v-else class="space-y-3 rounded-2xl border border-indigo-100 bg-indigo-50/30 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/20" @submit.prevent="handleSaveEdits">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Rating (1-5 Stars)</label>
                        <div class="flex items-center gap-2">
                            <select v-model.number="editForm.rating" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <option :value="5">5 Stars (★★★★★)</option>
                                <option :value="4">4 Stars (★★★★☆)</option>
                                <option :value="3">3 Stars (★★★☆☆)</option>
                                <option :value="2">2 Stars (★★☆☆☆)</option>
                                <option :value="1">1 Star (★☆☆☆☆)</option>
                            </select>
                        </div>
                    </div>

                    <TextField v-model="editForm.title" label="Review Title" placeholder="Optional review title" />

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Review Text / Body *</label>
                        <textarea
                            v-model="editForm.body"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            @click="isEditing = false"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="busy"
                            class="rounded-xl bg-indigo-600 px-4 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50"
                        >
                            Save Edits
                        </button>
                    </div>
                </form>
            </div>

            <!-- Actions Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-2">
                    <!-- Approve button -->
                    <button
                        v-if="!review.is_approved"
                        type="button"
                        :disabled="busy"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-50 transition"
                        @click="handleApprove"
                    >
                        <i class="fas fa-check text-[10px]"></i>
                        <span>Approve & Publish</span>
                    </button>

                    <!-- Reject button -->
                    <button
                        v-else
                        type="button"
                        :disabled="busy"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-amber-700 disabled:opacity-50 transition"
                        @click="handleReject"
                    >
                        <i class="fas fa-ban text-[10px]"></i>
                        <span>Unpublish / Reject</span>
                    </button>

                    <!-- Flag toggle -->
                    <button
                        type="button"
                        :disabled="busy"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition"
                        @click="handleToggleFlag"
                    >
                        <i class="fas fa-flag text-[10px]" :class="review.is_flagged ? 'text-rose-500' : 'text-slate-400'"></i>
                        <span>{{ review.is_flagged ? 'Unflag' : 'Flag' }}</span>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        @click="isEditing = !isEditing"
                    >
                        <i class="fas fa-pen text-[10px] mr-1"></i> Edit Text
                    </button>

                    <button
                        type="button"
                        :disabled="busy"
                        class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-600 hover:text-white dark:bg-rose-950/40 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white transition"
                        @click="handleDelete"
                    >
                        <i class="fas fa-trash-alt text-[10px] mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
