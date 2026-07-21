<script setup>
import { ref, computed, onMounted } from 'vue';
import client from '../../api/client';
import { reportError } from '../../errorReporter';
import BannerFormModal from '../BannerFormModal.vue';
import ConfirmDialog from '../ConfirmDialog.vue';

const loading = ref(true);
const banners = ref([]);
const activeFilter = ref('all');

const showFormModal = ref(false);
const editingBanner = ref(null);

const showDeleteConfirm = ref(false);
const bannerToDelete = ref(null);
const deleting = ref(false);

const POSITIONS = {
    hero: 'Hero (Main)',
    promo: 'Promo Strip',
    sidebar: 'Sidebar',
    top_bar: 'Top Bar',
};

async function loadBanners() {
    loading.value = true;
    try {
        const { data } = await client.get('/banners');
        banners.value = data.data || data;
    } catch (e) {
        reportError('Failed to load banners.', e?.message ?? String(e));
    } finally {
        loading.value = false;
    }
}

const filteredBanners = computed(() => {
    if (activeFilter.value === 'all') {
        return banners.value;
    }
    return banners.value.filter((b) => b.position === activeFilter.value);
});

const stats = computed(() => {
    const total = banners.value.length;
    const live = banners.value.filter((b) => b.is_live).length;
    const scheduled = banners.value.filter((b) => b.is_active && !b.is_live).length;
    const hero = banners.value.filter((b) => b.position === 'hero').length;
    return { total, live, scheduled, hero };
});

function openCreateModal() {
    editingBanner.value = null;
    showFormModal.value = true;
}

function openEditModal(banner) {
    editingBanner.value = banner;
    showFormModal.value = true;
}

function handleSaved(savedBanner) {
    const idx = banners.value.findIndex((b) => b.id === savedBanner.id);
    if (idx !== -1) {
        banners.value[idx] = savedBanner;
    } else {
        banners.value.unshift(savedBanner);
    }
    loadBanners(); // refresh list to preserve exact ordering
}

async function toggleActive(banner) {
    try {
        const { data } = await client.patch(`/banners/${banner.id}/toggle`);
        const updated = data.data || data;
        const idx = banners.value.findIndex((b) => b.id === updated.id);
        if (idx !== -1) {
            banners.value[idx] = updated;
        }
    } catch (e) {
        reportError('Failed to toggle banner active status.', e?.message ?? String(e));
    }
}

function promptDelete(banner) {
    bannerToDelete.value = banner;
    showDeleteConfirm.value = true;
}

async function confirmDelete() {
    if (!bannerToDelete.value) return;
    deleting.value = true;
    try {
        await client.delete(`/banners/${bannerToDelete.value.id}`);
        banners.value = banners.value.filter((b) => b.id !== bannerToDelete.value.id);
        showDeleteConfirm.value = false;
        bannerToDelete.value = null;
    } catch (e) {
        reportError('Failed to delete banner.', e?.message ?? String(e));
    } finally {
        deleting.value = false;
    }
}

async function movePosition(banner, direction) {
    // Reorder banners within the same position queue
    const posBanners = banners.value
        .filter((b) => b.position === banner.position)
        .sort((a, b) => a.sort_order - b.sort_order);

    const idx = posBanners.findIndex((b) => b.id === banner.id);
    if (idx === -1) return;

    const targetIdx = direction === 'up' ? idx - 1 : idx + 1;
    if (targetIdx < 0 || targetIdx >= posBanners.length) return;

    // Swap items in position queue
    const tempId = posBanners[idx].id;
    posBanners[idx] = posBanners[targetIdx];
    posBanners[targetIdx] = banners.value.find((b) => b.id === tempId);

    // Collect order payload for all banners
    const updatedIds = [];
    const grouped = {};
    for (const b of banners.value) {
        if (!grouped[b.position]) grouped[b.position] = [];
        if (b.position === banner.position) continue;
        grouped[b.position].push(b);
    }
    grouped[banner.position] = posBanners;

    for (const key of Object.keys(POSITIONS)) {
        if (grouped[key]) {
            grouped[key].forEach((item) => updatedIds.push(item.id));
        }
    }

    try {
        await client.post('/banners/reorder', { order: updatedIds });
        await loadBanners();
    } catch (e) {
        reportError('Failed to reorder banners.', e?.message ?? String(e));
    }
}

onMounted(() => {
    loadBanners();
});
</script>

<template>
    <div class="flex h-full flex-col">
        <!-- Header -->
        <div class="flex flex-col gap-4 border-b border-slate-200 p-4 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                    Banner & Campaign Management
                </h3>
                <p class="mt-0.5 text-[10px] text-slate-400">
                    Manage storefront hero slideshows, promo strips, sidebar banners, and top notices.
                </p>
            </div>
            <button
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition"
                @click="openCreateModal"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Add Banner</span>
            </button>
        </div>

        <div v-if="loading" class="flex flex-1 items-center justify-center p-6 text-xs text-slate-400">
            <i class="fas fa-spinner animate-spin mr-2"></i> Loading campaign banners...
        </div>

        <div v-else class="flex min-h-0 flex-1 flex-col overflow-y-auto p-4 space-y-6">
            <!-- Stats overview bar -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total Banners</span>
                    <p class="mt-1 text-lg font-black text-slate-900 dark:text-white">{{ stats.total }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Active Live</span>
                    <p class="mt-1 text-lg font-black text-emerald-600 dark:text-emerald-400">{{ stats.live }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-sky-600 dark:text-sky-400">Scheduled</span>
                    <p class="mt-1 text-lg font-black text-sky-600 dark:text-sky-400">{{ stats.scheduled }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Hero Main</span>
                    <p class="mt-1 text-lg font-black text-indigo-600 dark:text-indigo-400">{{ stats.hero }}</p>
                </div>
            </div>

            <!-- Position Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
                <button
                    type="button"
                    class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                    :class="activeFilter === 'all'
                        ? 'bg-slate-900 text-white dark:bg-indigo-600'
                        : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                    @click="activeFilter = 'all'"
                >
                    All Positions ({{ banners.length }})
                </button>
                <button
                    v-for="(posLabel, posKey) in POSITIONS"
                    :key="posKey"
                    type="button"
                    class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                    :class="activeFilter === posKey
                        ? 'bg-slate-900 text-white dark:bg-indigo-600'
                        : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                    @click="activeFilter = posKey"
                >
                    {{ posLabel }} ({{ banners.filter(b => b.position === posKey).length }})
                </button>
            </div>

            <!-- Banner List Grid -->
            <div v-if="filteredBanners.length > 0" class="space-y-3">
                <div
                    v-for="(banner, idx) in filteredBanners"
                    :key="banner.id"
                    class="group relative rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-slate-600"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <!-- Image thumbnail / Color sample -->
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="relative h-16 w-28 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-inner dark:border-slate-700 dark:bg-slate-900">
                                <img
                                    v-if="banner.image_url"
                                    :src="banner.image_url"
                                    class="h-full w-full object-cover"
                                    alt="Banner Image"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-white/80"
                                    :style="{ backgroundColor: banner.bg_color || '#4f46e5' }"
                                >
                                    <i class="fas fa-image text-lg" :style="{ color: banner.text_color || '#ffffff' }"></i>
                                </div>
                                <div class="absolute bottom-1.5 left-1.5 flex gap-1">
                                    <div
                                        class="h-2 w-2 rounded-full"
                                        :class="banner.is_live ? 'bg-emerald-500 animate-pulse' : (banner.is_active ? 'bg-sky-400' : 'bg-slate-400')"
                                    ></div>
                                </div>
                            </div>

                            <!-- Banner Details -->
                            <div class="min-w-0 flex-1 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[9px] font-black uppercase tracking-wider text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                        {{ POSITIONS[banner.position] || banner.position }}
                                    </span>

                                    <!-- Status indicator badge -->
                                    <span
                                        v-if="banner.is_live"
                                        class="rounded-md bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400"
                                    >
                                        <i class="fas fa-circle text-[6px] mr-1"></i> Live
                                    </span>
                                    <span
                                        v-else-if="banner.is_active"
                                        class="rounded-md bg-sky-50 px-2 py-0.5 text-[9px] font-bold text-sky-600 dark:bg-sky-950 dark:text-sky-400"
                                    >
                                        <i class="fas fa-clock text-[8px] mr-1"></i> Scheduled
                                    </span>
                                    <span
                                        v-else
                                        class="rounded-md bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-400 dark:bg-slate-700 dark:text-slate-400"
                                    >
                                        Disabled
                                    </span>
                                </div>

                                <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                    {{ banner.title }}
                                </h4>

                                <p class="text-xs text-slate-400 truncate">
                                    {{ banner.caption || banner.subtitle || 'No description provided' }}
                                </p>

                                <div class="flex flex-wrap items-center gap-3 pt-1 text-[10px] text-slate-500 dark:text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        <div class="h-2.5 w-2.5 rounded-full border border-slate-300 shadow-xs" :style="{ backgroundColor: banner.bg_color }"></div>
                                        <span>Palette</span>
                                    </div>
                                    <div v-if="banner.button_text" class="flex items-center gap-1">
                                        <i class="fas fa-link text-[8px]"></i>
                                        <span>{{ banner.button_text }}</span>
                                    </div>
                                    <div v-if="banner.starts_at || banner.ends_at" class="flex items-center gap-1">
                                        <i class="fas fa-calendar-alt text-[8px]"></i>
                                        <span>{{ banner.starts_at ? banner.starts_at.substring(0, 10) : 'Anytime' }} &rarr; {{ banner.ends_at ? banner.ends_at.substring(0, 10) : 'Forever' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions bar -->
                        <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-2 dark:border-slate-700 sm:border-0 sm:pt-0 shrink-0">
                            <!-- Reorder buttons -->
                            <div class="flex items-center gap-0.5 rounded-lg border border-slate-200 bg-slate-50 p-0.5 dark:border-slate-700 dark:bg-slate-900">
                                <button
                                    type="button"
                                    class="h-7 w-7 rounded-md text-slate-400 hover:bg-white hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-white disabled:opacity-30 transition"
                                    :disabled="idx === 0"
                                    title="Move Up"
                                    @click="movePosition(banner, 'up')"
                                >
                                    <i class="fas fa-chevron-up text-[10px]"></i>
                                </button>
                                <button
                                    type="button"
                                    class="h-7 w-7 rounded-md text-slate-400 hover:bg-white hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-white disabled:opacity-30 transition"
                                    :disabled="idx === filteredBanners.length - 1"
                                    title="Move Down"
                                    @click="movePosition(banner, 'down')"
                                >
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </button>
                            </div>

                            <!-- Active Switch -->
                            <button
                                type="button"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="banner.is_active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'"
                                :title="banner.is_active ? 'Deactivate Banner' : 'Activate Banner'"
                                @click="toggleActive(banner)"
                            >
                                <span
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="banner.is_active ? 'translate-x-5' : 'translate-x-0.5'"
                                ></span>
                            </button>

                            <!-- Edit Button -->
                            <button
                                type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-indigo-600 dark:hover:text-white transition"
                                title="Edit Campaign"
                                @click="openEditModal(banner)"
                            >
                                <i class="fas fa-pen text-xs"></i>
                            </button>

                            <!-- Delete Button -->
                            <button
                                type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white"
                                title="Delete Campaign"
                                @click="promptDelete(banner)"
                            >
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-300 dark:bg-slate-800 dark:text-slate-600 mb-4">
                    <i class="fas fa-images text-2xl"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">No Banners Found</h4>
                <p class="mt-1 text-xs text-slate-400">
                    {{ activeFilter === 'all' ? 'Create your first marketing banner to display on the storefront.' : 'No banners currently configured for this position.' }}
                </p>
                <div class="mt-4">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition"
                        @click="openCreateModal"
                    >
                        <i class="fas fa-plus text-[10px]"></i>
                        <span>Create Banner</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        <BannerFormModal
            :open="showFormModal"
            :banner="editingBanner"
            @close="showFormModal = false"
            @saved="handleSaved"
        />

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            :open="showDeleteConfirm"
            title="Decommission Banner Campaign"
            :message="`Are you sure you want to delete '${bannerToDelete?.title}'? This action cannot be undone.`"
            confirm-label="Delete Banner"
            danger
            :busy="deleting"
            @cancel="showDeleteConfirm = false"
            @confirm="confirmDelete"
        />
    </div>
</template>
