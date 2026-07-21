<script setup>
import { ref, watch, onMounted } from 'vue';
import client from '../../api/client';
import { reportError } from '../../errorReporter';
import ReviewDetailModal from '../ReviewDetailModal.vue';
import ConfirmDialog from '../ConfirmDialog.vue';

const loading = ref(true);
const reviews = ref([]);
const stats = ref({ total: 0, approved: 0, pending: 0, flagged: 0, avg: 0, five: 0, one: 0 });
const meta = ref({ current_page: 1, last_page: 1, total: 0 });

const search = ref('');
const statusFilter = ref('all'); // 'all' | 'approved' | 'pending' | 'flagged'
const ratingFilter = ref('');

const selectedIds = ref([]);
const selectAll = ref(false);
const bulkBusy = ref(false);

const viewingReview = ref(null);
const showDetailModal = ref(false);

const showDeleteConfirm = ref(false);
const reviewToDelete = ref(null);
const deleting = ref(false);

async function loadReviews(page = 1) {
    loading.value = true;
    try {
        const { data } = await client.get('/reviews', {
            params: {
                page,
                search: search.value,
                status: statusFilter.value,
                rating: ratingFilter.value,
            },
        });
        reviews.value = data.data || [];
        stats.value = data.stats || stats.value;
        meta.value = data.meta || meta.value;
        selectedIds.value = [];
        selectAll.value = false;
    } catch (e) {
        reportError('Failed to load reviews.', e?.message ?? String(e));
    } finally {
        loading.value = false;
    }
}

function handleSelectAll(e) {
    if (e.target.checked) {
        selectedIds.value = reviews.value.map((r) => r.id);
    } else {
        selectedIds.value = [];
    }
}

async function quickApprove(review) {
    try {
        const { data } = await client.post(`/reviews/${review.id}/approve`);
        const updated = data.data || data;
        const idx = reviews.value.findIndex((r) => r.id === updated.id);
        if (idx !== -1) reviews.value[idx] = updated;
        loadReviews(meta.value.current_page);
    } catch (e) {
        reportError('Failed to approve review.', e?.message ?? String(e));
    }
}

async function quickReject(review) {
    try {
        const { data } = await client.post(`/reviews/${review.id}/reject`);
        const updated = data.data || data;
        const idx = reviews.value.findIndex((r) => r.id === updated.id);
        if (idx !== -1) reviews.value[idx] = updated;
        loadReviews(meta.value.current_page);
    } catch (e) {
        reportError('Failed to reject review.', e?.message ?? String(e));
    }
}

async function quickToggleFlag(review) {
    try {
        const { data } = await client.patch(`/reviews/${review.id}/toggle-flag`);
        const updated = data.data || data;
        const idx = reviews.value.findIndex((r) => r.id === updated.id);
        if (idx !== -1) reviews.value[idx] = updated;
    } catch (e) {
        reportError('Failed to toggle review flag.', e?.message ?? String(e));
    }
}

function openDetail(review) {
    viewingReview.value = review;
    showDetailModal.value = true;
}

function handleReviewUpdated(updated) {
    const idx = reviews.value.findIndex((r) => r.id === updated.id);
    if (idx !== -1) reviews.value[idx] = updated;
    viewingReview.value = updated;
    loadReviews(meta.value.current_page);
}

function handleReviewDeleted(deletedId) {
    reviews.value = reviews.value.filter((r) => r.id !== deletedId);
    showDetailModal.value = false;
    loadReviews(meta.value.current_page);
}

function promptDelete(review) {
    reviewToDelete.value = review;
    showDeleteConfirm.value = true;
}

async function confirmDelete() {
    if (!reviewToDelete.value) return;
    deleting.value = true;
    try {
        await client.delete(`/reviews/${reviewToDelete.value.id}`);
        reviews.value = reviews.value.filter((r) => r.id !== reviewToDelete.value.id);
        showDeleteConfirm.value = false;
        reviewToDelete.value = null;
        loadReviews(meta.value.current_page);
    } catch (e) {
        reportError('Failed to delete review.', e?.message ?? String(e));
    } finally {
        deleting.value = false;
    }
}

async function executeBulk(action) {
    if (!selectedIds.value.length) return;
    if (action === 'delete' && !confirm(`Are you sure you want to delete ${selectedIds.value.length} selected reviews?`)) {
        return;
    }
    bulkBusy.value = true;
    try {
        await client.post('/reviews/bulk', {
            action,
            ids: selectedIds.value,
        });
        await loadReviews(meta.value.current_page);
    } catch (e) {
        reportError('Bulk action failed.', e?.message ?? String(e));
    } finally {
        bulkBusy.value = false;
    }
}

watch([statusFilter, ratingFilter], () => {
    loadReviews(1);
});

onMounted(() => {
    loadReviews(1);
});
</script>

<template>
    <div class="flex h-full flex-col">
        <!-- Header -->
        <div class="flex flex-col gap-4 border-b border-slate-200 p-4 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                    Customer Reviews & Testimonials Moderation
                </h3>
                <p class="mt-0.5 text-[10px] text-slate-400">
                    Approve verified customer reviews to showcase live testimonials on your storefront homepage.
                </p>
            </div>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-y-auto p-4 space-y-6">
            <!-- Stats Bar -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total Reviews</span>
                    <p class="mt-1 text-lg font-black text-slate-900 dark:text-white">{{ stats.total }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Approved Live</span>
                    <p class="mt-1 text-lg font-black text-emerald-600 dark:text-emerald-400">{{ stats.approved }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">Pending Review</span>
                    <p class="mt-1 text-lg font-black text-amber-600 dark:text-amber-400">{{ stats.pending }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-rose-600 dark:text-rose-400">Flagged</span>
                    <p class="mt-1 text-lg font-black text-rose-600 dark:text-rose-400">{{ stats.flagged }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Avg Rating</span>
                    <div class="mt-1 flex items-center gap-1">
                        <span class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ stats.avg }}</span>
                        <i class="fas fa-star text-xs text-amber-400"></i>
                    </div>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by customer name, product, or keyword..."
                        class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-xs text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        @input="loadReviews(1)"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Filter pills -->
                    <button
                        type="button"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                        :class="statusFilter === 'all'
                            ? 'bg-slate-900 text-white dark:bg-indigo-600'
                            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                        @click="statusFilter = 'all'"
                    >
                        All ({{ stats.total }})
                    </button>
                    <button
                        type="button"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                        :class="statusFilter === 'approved'
                            ? 'bg-slate-900 text-white dark:bg-indigo-600'
                            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                        @click="statusFilter = 'approved'"
                    >
                        Approved ({{ stats.approved }})
                    </button>
                    <button
                        type="button"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                        :class="statusFilter === 'pending'
                            ? 'bg-slate-900 text-white dark:bg-indigo-600'
                            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                        @click="statusFilter = 'pending'"
                    >
                        Pending ({{ stats.pending }})
                    </button>
                    <button
                        type="button"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                        :class="statusFilter === 'flagged'
                            ? 'bg-slate-900 text-white dark:bg-indigo-600'
                            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                        @click="statusFilter = 'flagged'"
                    >
                        Flagged ({{ stats.flagged }})
                    </button>

                    <!-- Rating Select -->
                    <select
                        v-model="ratingFilter"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                    >
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars ★★★★★</option>
                        <option value="4">4 Stars ★★★★☆</option>
                        <option value="3">3 Stars ★★★☆☆</option>
                        <option value="2">2 Stars ★★☆☆☆</option>
                        <option value="1">1 Star ★☆☆☆☆</option>
                    </select>
                </div>
            </div>

            <!-- Bulk Action Bar -->
            <div v-if="selectedIds.length > 0" class="flex items-center justify-between rounded-2xl bg-indigo-50 p-3 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50">
                <span class="text-xs font-bold text-indigo-900 dark:text-indigo-200">
                    {{ selectedIds.length }} review(s) selected
                </span>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="bulkBusy"
                        class="rounded-xl bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 disabled:opacity-50"
                        @click="executeBulk('approve')"
                    >
                        Bulk Approve
                    </button>
                    <button
                        type="button"
                        :disabled="bulkBusy"
                        class="rounded-xl bg-amber-600 px-3 py-1 text-xs font-bold text-white shadow-xs hover:bg-amber-700 disabled:opacity-50"
                        @click="executeBulk('reject')"
                    >
                        Bulk Reject
                    </button>
                    <button
                        type="button"
                        :disabled="bulkBusy"
                        class="rounded-xl bg-rose-600 px-3 py-1 text-xs font-bold text-white shadow-xs hover:bg-rose-700 disabled:opacity-50"
                        @click="executeBulk('delete')"
                    >
                        Bulk Delete
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex flex-1 items-center justify-center p-6 text-xs text-slate-400">
                <i class="fas fa-spinner animate-spin mr-2"></i> Loading customer reviews...
            </div>

            <!-- Data Table -->
            <div v-else-if="reviews.length > 0" class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-slate-100 bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:border-slate-700 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-4 py-3 w-8">
                                <input
                                    type="checkbox"
                                    :checked="selectAll"
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    @change="handleSelectAll"
                                />
                            </th>
                            <th class="px-4 py-3">Customer & Rating</th>
                            <th class="px-4 py-3">Review Excerpt</th>
                            <th class="px-4 py-3">Product Item</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        <tr v-for="r in reviews" :key="r.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition">
                            <td class="px-4 py-3">
                                <input
                                    v-model="selectedIds"
                                    type="checkbox"
                                    :value="r.id"
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />
                            </td>

                            <!-- Customer & Rating -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ r.user?.name || 'Verified Customer' }}
                                </div>
                                <div class="flex text-amber-400 text-[10px] gap-0.5 mt-0.5">
                                    <i v-for="n in 5" :key="n" class="fas fa-star" :class="n <= r.rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700'"></i>
                                </div>
                            </td>

                            <!-- Body Excerpt -->
                            <td class="px-4 py-3 max-w-[260px]">
                                <div v-if="r.title" class="font-semibold text-slate-800 dark:text-slate-200 truncate">
                                    {{ r.title }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 italic">
                                    "{{ r.body }}"
                                </div>
                            </td>

                            <!-- Product -->
                            <td class="px-4 py-3">
                                <span v-if="r.stock" class="rounded-md bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300 truncate max-w-[140px] inline-block">
                                    {{ r.stock.name }}
                                </span>
                                <span v-else class="text-[10px] text-slate-400 italic">
                                    Storefront General
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3">
                                <span
                                    v-if="r.is_approved"
                                    class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400"
                                >
                                    <i class="fas fa-check-circle text-[6px]"></i> Approved
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[9px] font-bold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                >
                                    <i class="fas fa-clock text-[8px]"></i> Pending
                                </span>
                                <span v-if="r.is_flagged" class="ml-1 inline-flex items-center rounded-md bg-rose-50 px-1.5 py-0.5 text-[9px] font-bold text-rose-600 dark:bg-rose-950 dark:text-rose-400">
                                    Flagged
                                </span>
                            </td>

                            <!-- Date -->
                            <td class="px-4 py-3 text-[10px] text-slate-400 font-mono">
                                {{ r.created_at?.substring(0, 10) }}
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Quick approve / reject -->
                                    <button
                                        v-if="!r.is_approved"
                                        type="button"
                                        class="rounded-lg bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-600 hover:bg-emerald-600 hover:text-white dark:bg-emerald-950 dark:text-emerald-400 transition"
                                        title="Approve Review"
                                        @click="quickApprove(r)"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        class="rounded-lg bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-700 hover:bg-amber-600 hover:text-white dark:bg-amber-950 dark:text-amber-300 transition"
                                        title="Reject Review"
                                        @click="quickReject(r)"
                                    >
                                        Reject
                                    </button>

                                    <!-- Flag toggle -->
                                    <button
                                        type="button"
                                        class="h-7 w-7 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                                        :title="r.is_flagged ? 'Unflag Review' : 'Flag Review'"
                                        @click="quickToggleFlag(r)"
                                    >
                                        <i class="fas fa-flag text-[10px]" :class="r.is_flagged ? 'text-rose-500' : 'text-slate-300'"></i>
                                    </button>

                                    <!-- View/Edit -->
                                    <button
                                        type="button"
                                        class="h-7 w-7 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white dark:bg-slate-700 dark:text-slate-200 transition"
                                        title="View Review Details"
                                        @click="openDetail(r)"
                                    >
                                        <i class="fas fa-eye text-[10px]"></i>
                                    </button>

                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="h-7 w-7 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white dark:bg-rose-950/40 dark:text-rose-400 transition"
                                        title="Delete Review"
                                        @click="promptDelete(r)"
                                    >
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-else class="py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600 mb-4">
                    <i class="fas fa-star text-2xl"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">No Reviews Found</h4>
                <p class="mt-1 text-xs text-slate-400">
                    No customer reviews match your active filter criteria.
                </p>
            </div>

            <!-- Pagination -->
            <div v-if="meta.last_page > 1" class="flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-700">
                <span class="text-xs text-slate-400">
                    Showing page {{ meta.current_page }} of {{ meta.last_page }} ({{ meta.total }} total reviews)
                </span>
                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-40 dark:border-slate-700 dark:text-slate-300"
                        :disabled="meta.current_page === 1"
                        @click="loadReviews(meta.current_page - 1)"
                    >
                        Prev
                    </button>
                    <button
                        type="button"
                        class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-40 dark:border-slate-700 dark:text-slate-300"
                        :disabled="meta.current_page === meta.last_page"
                        @click="loadReviews(meta.current_page + 1)"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <ReviewDetailModal
            :open="showDetailModal"
            :review="viewingReview"
            @close="showDetailModal = false"
            @updated="handleReviewUpdated"
            @deleted="handleReviewDeleted"
        />

        <!-- Delete Confirm Dialog -->
        <ConfirmDialog
            :open="showDeleteConfirm"
            title="Delete Review"
            :message="`Are you sure you want to delete this review by ${reviewToDelete?.user?.name || 'Customer'}?`"
            confirm-label="Delete Review"
            danger
            :busy="deleting"
            @cancel="showDeleteConfirm = false"
            @confirm="confirmDelete"
        />
    </div>
</template>
