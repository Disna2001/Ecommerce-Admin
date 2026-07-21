<script setup>
import { ref, computed, onMounted } from 'vue';
import client from '../../api/client';
import { reportError } from '../../errorReporter';
import DiscountFormModal from '../DiscountFormModal.vue';
import ConfirmDialog from '../ConfirmDialog.vue';

const activeTab = ref('coupons'); // 'coupons' | 'automated'

// --- Coupons / Manual Discounts State ---
const loadingDiscounts = ref(true);
const discounts = ref([]);
const stats = ref({ total: 0, active: 0, coupons: 0, auto_apply: 0, scheduled: 0 });
const categories = ref([]);
const products = ref([]);
const search = ref('');
const filterType = ref('all'); // 'all' | 'coupon' | 'auto_apply'

const showFormModal = ref(false);
const editingDiscount = ref(null);

const showDeleteConfirm = ref(false);
const discountToDelete = ref(null);
const deleting = ref(false);

// --- Automated Rules State ---
const loadingAutomated = ref(false);
const savingRule = ref(false);
const orchestrating = ref(false);
const automatedData = ref({
    rule: {
        is_active: true,
        min_margin_percent: 10,
        max_discount_percent: 30,
        daily_items_limit: 10,
        rotation_strategy: 'random',
        target_categories: [],
        target_brands: [],
    },
    daily_discounts: [],
    daily_stats: { active_today: 0, total_value: 0, savings_value: 0, avg_discount: 0 },
    categories: [],
    brands: [],
});

async function loadDiscounts() {
    loadingDiscounts.value = true;
    try {
        const { data } = await client.get('/discounts', {
            params: { search: search.value, type: filterType.value },
        });
        discounts.value = data.data || [];
        stats.value = data.stats || stats.value;
        categories.value = data.categories || [];
        products.value = data.products || [];
    } catch (e) {
        reportError('Failed to load discounts.', e?.message ?? String(e));
    } finally {
        loadingDiscounts.value = false;
    }
}

async function loadAutomatedRules() {
    loadingAutomated.value = true;
    try {
        const { data } = await client.get('/automated-discounts');
        automatedData.value = data;
    } catch (e) {
        reportError('Failed to load automated discount rules.', e?.message ?? String(e));
    } finally {
        loadingAutomated.value = false;
    }
}

function openCreateModal() {
    editingDiscount.value = null;
    showFormModal.value = true;
}

function openEditModal(discount) {
    editingDiscount.value = discount;
    showFormModal.value = true;
}

function handleSavedDiscount() {
    loadDiscounts();
}

async function toggleActive(discount) {
    try {
        const { data } = await client.patch(`/discounts/${discount.id}/toggle`);
        const updated = data.data || data;
        const idx = discounts.value.findIndex((d) => d.id === updated.id);
        if (idx !== -1) {
            discounts.value[idx] = updated;
        }
    } catch (e) {
        reportError('Failed to toggle discount status.', e?.message ?? String(e));
    }
}

function promptDelete(discount) {
    discountToDelete.value = discount;
    showDeleteConfirm.value = true;
}

async function confirmDelete() {
    if (!discountToDelete.value) return;
    deleting.value = true;
    try {
        await client.delete(`/discounts/${discountToDelete.value.id}`);
        discounts.value = discounts.value.filter((d) => d.id !== discountToDelete.value.id);
        showDeleteConfirm.value = false;
        discountToDelete.value = null;
    } catch (e) {
        reportError('Failed to delete discount.', e?.message ?? String(e));
    } finally {
        deleting.value = false;
    }
}

async function saveAutomatedRule() {
    savingRule.value = true;
    try {
        const { data } = await client.patch('/automated-discounts', automatedData.value.rule);
        automatedData.value.rule = data.rule || automatedData.value.rule;
    } catch (e) {
        reportError('Failed to save automated rule config.', e?.message ?? String(e));
    } finally {
        savingRule.value = false;
    }
}

async function runOrchestration() {
    orchestrating.value = true;
    try {
        await client.post('/automated-discounts/orchestrate');
        await loadAutomatedRules();
    } catch (e) {
        reportError('Failed to run daily discount orchestration.', e?.message ?? String(e));
    } finally {
        orchestrating.value = false;
    }
}

onMounted(() => {
    loadDiscounts();
    loadAutomatedRules();
});
</script>

<template>
    <div class="flex h-full flex-col">
        <!-- Sub-Tab Header Bar -->
        <div class="flex flex-col gap-4 border-b border-slate-200 p-4 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                    Discounts & Pricing Promotions
                </h3>
                <p class="mt-0.5 text-[10px] text-slate-400">
                    Manage promo codes, storewide discounts, and automated margin-safe daily price rotations.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <!-- Sub-tab switcher -->
                <div class="flex rounded-xl bg-slate-200/70 p-1 dark:bg-slate-800">
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                        :class="activeTab === 'coupons'
                            ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white'
                            : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                        @click="activeTab = 'coupons'"
                    >
                        <i class="fas fa-ticket text-[10px] mr-1"></i> Coupons & Promos
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                        :class="activeTab === 'automated'
                            ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white'
                            : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                        @click="activeTab = 'automated'"
                    >
                        <i class="fas fa-robot text-[10px] mr-1"></i> Automated Rules
                    </button>
                </div>

                <button
                    v-if="activeTab === 'coupons'"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition"
                    @click="openCreateModal"
                >
                    <i class="fas fa-plus text-[10px]"></i>
                    <span>Add Discount</span>
                </button>
            </div>
        </div>

        <!-- ==================== SUB-TAB 1: COUPONS & PROMOS ==================== -->
        <div v-if="activeTab === 'coupons'" class="flex min-h-0 flex-1 flex-col overflow-y-auto p-4 space-y-6">
            <!-- Stats overview bar -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total Discounts</span>
                    <p class="mt-1 text-lg font-black text-slate-900 dark:text-white">{{ stats.total }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Active Live</span>
                    <p class="mt-1 text-lg font-black text-emerald-600 dark:text-emerald-400">{{ stats.active }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Coupon Codes</span>
                    <p class="mt-1 text-lg font-black text-indigo-600 dark:text-indigo-400">{{ stats.coupons }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[9px] font-black uppercase tracking-widest text-teal-600 dark:text-teal-400">Auto-Applied</span>
                    <p class="mt-1 text-lg font-black text-teal-600 dark:text-teal-400">{{ stats.auto_apply }}</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search discount by name or coupon code..."
                        class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-xs text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        @input="loadDiscounts"
                    />
                </div>

                <div class="flex items-center gap-1.5 overflow-x-auto">
                    <button
                        type="button"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                        :class="filterType === 'all'
                            ? 'bg-slate-900 text-white dark:bg-indigo-600'
                            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                        @click="filterType = 'all'; loadDiscounts();"
                    >
                        All Types
                    </button>
                    <button
                        type="button"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                        :class="filterType === 'coupon'
                            ? 'bg-slate-900 text-white dark:bg-indigo-600'
                            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                        @click="filterType = 'coupon'; loadDiscounts();"
                    >
                        Coupons Only
                    </button>
                    <button
                        type="button"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition shrink-0"
                        :class="filterType === 'auto_apply'
                            ? 'bg-slate-900 text-white dark:bg-indigo-600'
                            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'"
                        @click="filterType = 'auto_apply'; loadDiscounts();"
                    >
                        Auto-Applied Only
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loadingDiscounts" class="flex flex-1 items-center justify-center p-6 text-xs text-slate-400">
                <i class="fas fa-spinner animate-spin mr-2"></i> Loading discounts...
            </div>

            <!-- Discounts Data Table -->
            <div v-else-if="discounts.length > 0" class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-slate-100 bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:border-slate-700 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-4 py-3">Discount / Code</th>
                            <th class="px-4 py-3">Type & Value</th>
                            <th class="px-4 py-3">Scope</th>
                            <th class="px-4 py-3">Min Order</th>
                            <th class="px-4 py-3">Redemptions</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        <tr v-for="d in discounts" :key="d.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition">
                            <!-- Name & Code -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-white truncate max-w-[200px]">
                                    {{ d.name }}
                                </div>
                                <div v-if="d.code" class="mt-0.5">
                                    <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 font-mono text-[10px] font-bold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">
                                        <i class="fas fa-ticket text-[8px]"></i> {{ d.code }}
                                    </span>
                                </div>
                                <div v-else class="mt-0.5 text-[10px] text-slate-400 italic">
                                    Auto-applied
                                </div>
                            </td>

                            <!-- Value -->
                            <td class="px-4 py-3">
                                <span class="font-black text-slate-900 dark:text-white">
                                    {{ d.type === 'percentage' ? `${d.value}% OFF` : `Rs ${Number(d.value).toLocaleString()} OFF` }}
                                </span>
                                <div v-if="d.max_discount_amount" class="text-[10px] text-slate-400">
                                    Max: Rs {{ Number(d.max_discount_amount).toLocaleString() }}
                                </div>
                            </td>

                            <!-- Scope -->
                            <td class="px-4 py-3">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    {{ d.scope }}
                                </span>
                            </td>

                            <!-- Min Order -->
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-mono">
                                {{ d.min_order_amount > 0 ? `Rs ${Number(d.min_order_amount).toLocaleString()}` : 'None' }}
                            </td>

                            <!-- Usage -->
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ d.used_count }} / {{ d.usage_limit ? d.usage_limit : '∞' }}
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3">
                                <span
                                    v-if="d.is_currently_active"
                                    class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400"
                                >
                                    <i class="fas fa-circle text-[6px]"></i> Active
                                </span>
                                <span
                                    v-else-if="d.is_expired"
                                    class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-[9px] font-bold text-rose-600 dark:bg-rose-950 dark:text-rose-400"
                                >
                                    <i class="fas fa-clock text-[8px]"></i> Expired
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-400 dark:bg-slate-700 dark:text-slate-400"
                                >
                                    Disabled
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Active Switch -->
                                    <button
                                        type="button"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="d.is_active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'"
                                        @click="toggleActive(d)"
                                    >
                                        <span
                                            class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="d.is_active ? 'translate-x-4' : 'translate-x-0.5'"
                                        ></span>
                                    </button>

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-900 hover:text-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-indigo-600 dark:hover:text-white transition"
                                        title="Edit Discount"
                                        @click="openEditModal(d)"
                                    >
                                        <i class="fas fa-pen text-[10px]"></i>
                                    </button>

                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white"
                                        title="Delete Discount"
                                        @click="promptDelete(d)"
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
                    <i class="fas fa-tags text-2xl"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">No Discounts Found</h4>
                <p class="mt-1 text-xs text-slate-400">
                    Create a coupon code or automatic store discount to start running campaigns.
                </p>
                <div class="mt-4">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition"
                        @click="openCreateModal"
                    >
                        <i class="fas fa-plus text-[10px]"></i>
                        <span>Create Discount</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ==================== SUB-TAB 2: AUTOMATED RULES ==================== -->
        <div v-else-if="activeTab === 'automated'" class="flex min-h-0 flex-1 flex-col overflow-y-auto p-4 space-y-6">
            <div v-if="loadingAutomated" class="flex flex-1 items-center justify-center p-6 text-xs text-slate-400">
                <i class="fas fa-spinner animate-spin mr-2"></i> Loading automated rules engine...
            </div>

            <template v-else>
                <!-- Stats & Run Orchestration Header Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 space-y-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                                Dynamic Pricing Engine
                            </span>
                            <h4 class="text-base font-black text-slate-900 dark:text-white">
                                Automated Daily Discount Orchestrator
                            </h4>
                            <p class="text-xs text-slate-400">
                                Automatically calculates margin-safe daily price reductions on catalog items based on rotation strategy.
                            </p>
                        </div>

                        <button
                            type="button"
                            :disabled="orchestrating"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50 transition shrink-0"
                            @click="runOrchestration"
                        >
                            <i v-if="orchestrating" class="fas fa-spinner animate-spin"></i>
                            <i v-else class="fas fa-rotate text-xs"></i>
                            <span>{{ orchestrating ? 'Orchestrating...' : 'Run Daily Orchestration Now' }}</span>
                        </button>
                    </div>

                    <!-- Daily Stats -->
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 pt-2 border-t border-slate-100 dark:border-slate-700">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Items Discounted Today</span>
                            <p class="mt-0.5 text-base font-black text-slate-900 dark:text-white">{{ automatedData.daily_stats.active_today }}</p>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total Original Value</span>
                            <p class="mt-0.5 text-base font-black text-slate-900 dark:text-white">Rs {{ Number(automatedData.daily_stats.total_value).toLocaleString() }}</p>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Customer Savings Value</span>
                            <p class="mt-0.5 text-base font-black text-emerald-600 dark:text-emerald-400">Rs {{ Number(automatedData.daily_stats.savings_value).toLocaleString() }}</p>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Average Discount %</span>
                            <p class="mt-0.5 text-base font-black text-indigo-600 dark:text-indigo-400">{{ Number(automatedData.daily_stats.avg_discount).toFixed(1) }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Rule Parameters Config Form -->
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-700">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                            Automation Safety Rules & Strategy
                        </h4>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Rule Active</span>
                            <button
                                type="button"
                                class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="automatedData.rule.is_active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'"
                                @click="automatedData.rule.is_active = !automatedData.rule.is_active"
                            >
                                <span
                                    class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="automatedData.rule.is_active ? 'translate-x-4' : 'translate-x-0.5'"
                                ></span>
                            </button>
                        </div>
                    </div>

                    <form class="space-y-4" @submit.prevent="saveAutomatedRule">
                        <div class="grid gap-4 sm:grid-cols-4">
                            <!-- Min Margin -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Min Profit Margin (%) *
                                </label>
                                <input
                                    v-model.number="automatedData.rule.min_margin_percent"
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    max="100"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                                <p class="text-[10px] text-slate-400">Guarantees profit above unit cost.</p>
                            </div>

                            <!-- Max Discount -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Max Discount Cap (%) *
                                </label>
                                <input
                                    v-model.number="automatedData.rule.max_discount_percent"
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    max="100"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                                <p class="text-[10px] text-slate-400">Maximum allowed percentage drop.</p>
                            </div>

                            <!-- Daily Limit -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Daily Items Limit *
                                </label>
                                <input
                                    v-model.number="automatedData.rule.daily_items_limit"
                                    type="number"
                                    min="1"
                                    max="100"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                                <p class="text-[10px] text-slate-400">Number of products selected per day.</p>
                            </div>

                            <!-- Strategy -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Rotation Strategy *
                                </label>
                                <select
                                    v-model="automatedData.rule.rotation_strategy"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                >
                                    <option value="random">Random Sampling</option>
                                    <option value="slow_moving">Slow-moving Products</option>
                                    <option value="overstocked">Highest Stock Quantity</option>
                                </select>
                                <p class="text-[10px] text-slate-400">Product selection criteria.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-3">
                            <button
                                type="submit"
                                :disabled="savingRule"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800 disabled:opacity-50 dark:bg-indigo-600 dark:hover:bg-indigo-700 transition"
                            >
                                <i v-if="savingRule" class="fas fa-spinner animate-spin"></i>
                                <span>{{ savingRule ? 'Saving...' : 'Save Rule Parameters' }}</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Today's Daily Discounts Registry -->
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 space-y-4">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                        Today's Automated Discount Registry ({{ automatedData.daily_discounts.length }} Items)
                    </h4>

                    <div v-if="automatedData.daily_discounts.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="border-b border-slate-100 bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:border-slate-700 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-4 py-2.5">Product Item</th>
                                    <th class="px-4 py-2.5">Original Price</th>
                                    <th class="px-4 py-2.5">Discount %</th>
                                    <th class="px-4 py-2.5">Final Storefront Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                                <tr v-for="daily in automatedData.daily_discounts" :key="daily.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30">
                                    <td class="px-4 py-2.5 font-bold text-slate-900 dark:text-white">
                                        {{ daily.stock?.name || `Product #${daily.stock_id}` }}
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-400 line-through font-mono">
                                        Rs {{ Number(daily.original_price).toLocaleString() }}
                                    </td>
                                    <td class="px-4 py-2.5 font-black text-indigo-600 dark:text-indigo-400">
                                        -{{ daily.discount_percent }}%
                                    </td>
                                    <td class="px-4 py-2.5 font-black text-emerald-600 dark:text-emerald-400 font-mono">
                                        Rs {{ Number(daily.discounted_price).toLocaleString() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="py-8 text-center text-xs text-slate-400">
                        No automated daily discounts generated yet for today. Click 'Run Daily Orchestration Now' above to calculate.
                    </div>
                </div>
            </template>
        </div>

        <!-- Form Modal -->
        <DiscountFormModal
            :open="showFormModal"
            :discount="editingDiscount"
            :categories="categories"
            :products="products"
            @close="showFormModal = false"
            @saved="handleSavedDiscount"
        />

        <!-- Delete Confirm Dialog -->
        <ConfirmDialog
            :open="showDeleteConfirm"
            title="Delete Discount Campaign"
            :message="`Are you sure you want to delete '${discountToDelete?.name}'? This action will permanently remove this discount.`"
            confirm-label="Delete Discount"
            danger
            :busy="deleting"
            @cancel="showDeleteConfirm = false"
            @confirm="confirmDelete"
        />
    </div>
</template>
