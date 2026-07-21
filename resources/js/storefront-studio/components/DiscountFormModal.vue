<script setup>
import { ref, watch } from 'vue';
import client from '../api/client';
import TextField from './fields/TextField.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    discount: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
});

const emit = defineEmits(['close', 'saved']);

const saving = ref(false);
const generatingCode = ref(false);
const errorMessage = ref('');
const fieldErrors = ref({});

const form = ref({
    name: '',
    code: '',
    type: 'percentage',
    value: 10,
    min_order_amount: 0,
    max_discount_amount: '',
    scope: 'all',
    scope_id: '',
    has_timer: false,
    starts_at: '',
    ends_at: '',
    show_timer_on_site: true,
    timer_label: 'Offer ends in:',
    usage_limit: '',
    is_active: true,
    description: '',
});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            errorMessage.value = '';
            fieldErrors.value = {};
            if (props.discount) {
                form.value = {
                    name: props.discount.name || '',
                    code: props.discount.code || '',
                    type: props.discount.type || 'percentage',
                    value: props.discount.value ?? 10,
                    min_order_amount: props.discount.min_order_amount ?? 0,
                    max_discount_amount: props.discount.max_discount_amount ?? '',
                    scope: props.discount.scope || 'all',
                    scope_id: props.discount.scope_id || '',
                    has_timer: props.discount.has_timer ?? false,
                    starts_at: props.discount.starts_at || '',
                    ends_at: props.discount.ends_at || '',
                    show_timer_on_site: props.discount.show_timer_on_site ?? true,
                    timer_label: props.discount.timer_label || 'Offer ends in:',
                    usage_limit: props.discount.usage_limit ?? '',
                    is_active: props.discount.is_active ?? true,
                    description: props.discount.description || '',
                };
            } else {
                form.value = {
                    name: '',
                    code: '',
                    type: 'percentage',
                    value: 10,
                    min_order_amount: 0,
                    max_discount_amount: '',
                    scope: 'all',
                    scope_id: '',
                    has_timer: false,
                    starts_at: '',
                    ends_at: '',
                    show_timer_on_site: true,
                    timer_label: 'Offer ends in:',
                    usage_limit: '',
                    is_active: true,
                    description: '',
                };
            }
        }
    }
);

async function generateCode() {
    generatingCode.value = true;
    try {
        const { data } = await client.post('/discounts/generate-code');
        form.value.code = data.code;
    } catch (e) {
        // Fallback random generator
        form.value.code = Math.random().toString(36).substring(2, 10).toUpperCase();
    } finally {
        generatingCode.value = false;
    }
}

function applyPreset(preset) {
    if (preset === 'welcome') {
        form.value.name = 'Welcome Coupon';
        form.value.type = 'percentage';
        form.value.value = 10;
        form.value.min_order_amount = 0;
        form.value.usage_limit = 200;
        generateCode();
    } else if (preset === 'bundle') {
        form.value.name = 'Bundle Savings';
        form.value.type = 'fixed';
        form.value.value = 500;
        form.value.min_order_amount = 5000;
        form.value.max_discount_amount = '';
        form.value.code = '';
    } else if (preset === 'weekend') {
        form.value.name = 'Weekend Flash Sale';
        form.value.type = 'percentage';
        form.value.value = 15;
        form.value.has_timer = true;
        const now = new Date();
        const future = new Date(now.getTime() + 48 * 60 * 60 * 1000);
        form.value.starts_at = now.toISOString().slice(0, 16);
        form.value.ends_at = future.toISOString().slice(0, 16);
        form.value.show_timer_on_site = true;
        form.value.timer_label = 'Weekend deal ends in:';
        generateCode();
    }
}

async function handleSubmit() {
    saving.value = true;
    errorMessage.value = '';
    fieldErrors.value = {};

    try {
        let res;
        if (props.discount?.id) {
            res = await client.put(`/discounts/${props.discount.id}`, form.value);
        } else {
            res = await client.post('/discounts', form.value);
        }
        emit('saved', res.data.data || res.data);
        emit('close');
    } catch (err) {
        if (err.response?.status === 422 && err.response?.data?.errors) {
            fieldErrors.value = err.response.data.errors;
            errorMessage.value = err.response.data.message || 'Please fix the validation errors below.';
        } else {
            errorMessage.value = err.response?.data?.message || err.message || 'Failed to save discount.';
        }
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
    >
        <div class="w-full max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-800 my-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-700">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">
                        {{ discount ? 'Edit Discount / Coupon' : 'Create Discount / Coupon' }}
                    </h3>
                    <p class="text-xs text-slate-400">
                        Configure promotional rules, coupon codes, product scopes, and countdown timers.
                    </p>
                </div>
                <button
                    type="button"
                    class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-700 dark:hover:text-white"
                    @click="emit('close')"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Quick Presets Strip -->
            <div v-if="!discount" class="mt-4 flex flex-wrap items-center gap-2 rounded-2xl bg-slate-50 p-3 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Presets:</span>
                <button
                    type="button"
                    class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300"
                    @click="applyPreset('welcome')"
                >
                    Welcome Coupon (10%)
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-teal-50 px-2.5 py-1 text-xs font-bold text-teal-600 hover:bg-teal-100 dark:bg-teal-950 dark:text-teal-300"
                    @click="applyPreset('bundle')"
                >
                    Bundle Save (Fixed Rs 500)
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-100 dark:bg-amber-950 dark:text-amber-300"
                    @click="applyPreset('weekend')"
                >
                    Flash Sale (15% + Timer)
                </button>
            </div>

            <div v-if="errorMessage" class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-400">
                {{ errorMessage }}
            </div>

            <form class="mt-4 space-y-4 max-h-[65vh] overflow-y-auto pr-1" @submit.prevent="handleSubmit">
                <!-- Name & Code -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <TextField
                            v-model="form.name"
                            label="Campaign Name *"
                            placeholder="e.g. Summer Promo 2026"
                        />
                        <p v-if="fieldErrors.name" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.name[0] }}</p>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Coupon Code (Optional)
                            </label>
                            <button
                                type="button"
                                class="text-[10px] font-bold text-indigo-600 hover:underline dark:text-indigo-400"
                                @click="generateCode"
                            >
                                <i class="fas fa-wand-magic-sparkles mr-1"></i>Generate Code
                            </button>
                        </div>
                        <input
                            v-model="form.code"
                            type="text"
                            placeholder="Leave empty for auto-apply discount"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-mono uppercase text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="fieldErrors.code" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.code[0] }}</p>
                    </div>
                </div>

                <!-- Type & Value -->
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Discount Type *
                        </label>
                        <select
                            v-model="form.type"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (Rs)</option>
                        </select>
                        <p v-if="fieldErrors.type" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.type[0] }}</p>
                    </div>

                    <div>
                        <TextField
                            v-model.number="form.value"
                            :label="`Discount Value (${form.type === 'percentage' ? '%' : 'Rs'}) *`"
                            type="number"
                            step="0.01"
                        />
                        <p v-if="fieldErrors.value" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.value[0] }}</p>
                    </div>

                    <div>
                        <TextField
                            v-model.number="form.max_discount_amount"
                            label="Max Cap (Rs)"
                            placeholder="Optional cap"
                            type="number"
                            step="0.01"
                        />
                        <p v-if="fieldErrors.max_discount_amount" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.max_discount_amount[0] }}</p>
                    </div>
                </div>

                <!-- Scope & Scope ID -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Applicable Scope *
                        </label>
                        <select
                            v-model="form.scope"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="all">Entire Store (All Products)</option>
                            <option value="category">Specific Category</option>
                            <option value="product">Specific Product</option>
                        </select>
                        <p v-if="fieldErrors.scope" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.scope[0] }}</p>
                    </div>

                    <div v-if="form.scope === 'category'" class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Select Category *
                        </label>
                        <select
                            v-model="form.scope_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="">-- Choose Category --</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <p v-if="fieldErrors.scope_id" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.scope_id[0] }}</p>
                    </div>

                    <div v-if="form.scope === 'product'" class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Select Product *
                        </label>
                        <select
                            v-model="form.scope_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="">-- Choose Product --</option>
                            <option v-for="prod in products" :key="prod.id" :value="prod.id">{{ prod.name }} ({{ prod.sku }})</option>
                        </select>
                        <p v-if="fieldErrors.scope_id" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.scope_id[0] }}</p>
                    </div>
                </div>

                <!-- Limits & Min Order -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <TextField
                            v-model.number="form.min_order_amount"
                            label="Min Order Amount (Rs)"
                            type="number"
                            step="0.01"
                        />
                        <p v-if="fieldErrors.min_order_amount" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.min_order_amount[0] }}</p>
                    </div>
                    <div>
                        <TextField
                            v-model.number="form.usage_limit"
                            label="Usage Limit (Redemptions)"
                            placeholder="Unlimited if left blank"
                            type="number"
                        />
                        <p v-if="fieldErrors.usage_limit" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.usage_limit[0] }}</p>
                    </div>
                </div>

                <!-- Scheduling -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Starts At</label>
                        <input
                            v-model="form.starts_at"
                            type="datetime-local"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="fieldErrors.starts_at" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.starts_at[0] }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ends At (Expiry)</label>
                        <input
                            v-model="form.ends_at"
                            type="datetime-local"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="fieldErrors.ends_at" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.ends_at[0] }}</p>
                    </div>
                </div>

                <!-- Flash Sale Timer Configuration -->
                <div class="rounded-2xl border border-slate-200 p-4 space-y-3 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Flash Sale Countdown Timer</span>
                            <p class="text-[10px] text-slate-400">Show a live urgency timer widget on product pages and banners.</p>
                        </div>
                        <button
                            type="button"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="form.has_timer ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700'"
                            @click="form.has_timer = !form.has_timer"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="form.has_timer ? 'translate-x-5' : 'translate-x-0.5'"
                            ></span>
                        </button>
                    </div>

                    <div v-if="form.has_timer" class="grid gap-3 sm:grid-cols-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                        <TextField
                            v-model="form.timer_label"
                            label="Timer Label"
                            placeholder="Offer ends in:"
                        />
                        <div class="flex items-center gap-2 pt-5">
                            <input
                                v-model="form.show_timer_on_site"
                                type="checkbox"
                                id="show_timer"
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label for="show_timer" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Display timer on storefront
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <TextField
                        v-model="form.description"
                        label="Internal Description / Notes"
                        placeholder="Internal campaign notes or terms"
                    />
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center justify-between rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                    <div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Active Status</span>
                        <p class="text-[10px] text-slate-400">Enable or disable this discount across the storefront.</p>
                    </div>
                    <button
                        type="button"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none"
                        :class="form.is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'"
                        @click="form.is_active = !form.is_active"
                    >
                        <span
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="form.is_active ? 'translate-x-5' : 'translate-x-0.5'"
                        ></span>
                    </button>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                        @click="emit('close')"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="saving"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50"
                    >
                        <i v-if="saving" class="fas fa-spinner animate-spin text-xs"></i>
                        <span>{{ saving ? 'Saving...' : (discount ? 'Save Changes' : 'Create Discount') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
