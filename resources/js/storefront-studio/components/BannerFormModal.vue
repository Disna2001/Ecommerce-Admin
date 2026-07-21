<script setup>
import { ref, watch } from 'vue';
import client from '../api/client';
import ImageField from './fields/ImageField.vue';
import TextField from './fields/TextField.vue';
import ColorField from './fields/ColorField.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    banner: { type: Object, default: null },
});

const emit = defineEmits(['close', 'saved']);

const saving = ref(false);
const errorMessage = ref('');
const fieldErrors = ref({});

const form = ref({
    title: '',
    subtitle: '',
    caption: '',
    button_text: '',
    button_link: '',
    image_path: '',
    position: 'hero',
    bg_color: '#4f46e5',
    text_color: '#ffffff',
    is_active: true,
    sort_order: 0,
    starts_at: '',
    ends_at: '',
});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            errorMessage.value = '';
            fieldErrors.value = {};
            if (props.banner) {
                form.value = {
                    title: props.banner.title || '',
                    subtitle: props.banner.subtitle || '',
                    caption: props.banner.caption || '',
                    button_text: props.banner.button_text || '',
                    button_link: props.banner.button_link || '',
                    image_path: props.banner.image_path || '',
                    position: props.banner.position || 'hero',
                    bg_color: props.banner.bg_color || '#4f46e5',
                    text_color: props.banner.text_color || '#ffffff',
                    is_active: props.banner.is_active ?? true,
                    sort_order: props.banner.sort_order ?? 0,
                    starts_at: props.banner.starts_at || '',
                    ends_at: props.banner.ends_at || '',
                };
            } else {
                form.value = {
                    title: '',
                    subtitle: '',
                    caption: '',
                    button_text: '',
                    button_link: '',
                    image_path: '',
                    position: 'hero',
                    bg_color: '#4f46e5',
                    text_color: '#ffffff',
                    is_active: true,
                    sort_order: 0,
                    starts_at: '',
                    ends_at: '',
                };
            }
        }
    }
);

function applyPreset(preset) {
    if (preset === 'hero_launch') {
        form.value.title = 'Launch a bold hero campaign';
        form.value.subtitle = 'Hero spotlight';
        form.value.caption = 'Use this for seasonal launches, top-selling collections, or premium storefront announcements.';
        form.value.button_text = 'Shop collection';
        form.value.button_link = '/products';
        form.value.position = 'hero';
        form.value.bg_color = '#312e81';
        form.value.text_color = '#ffffff';
    } else if (preset === 'promo_strip') {
        form.value.title = 'Weekend deal now live';
        form.value.subtitle = 'Promo strip';
        form.value.caption = 'Keep a shorter message here for bundle offers, free shipping, or limited-time coupon pushes.';
        form.value.button_text = 'View deal';
        form.value.button_link = '/products';
        form.value.position = 'promo';
        form.value.bg_color = '#0f766e';
        form.value.text_color = '#ffffff';
    } else if (preset === 'top_notice') {
        form.value.title = 'Free delivery on selected orders';
        form.value.subtitle = 'Top notice';
        form.value.caption = 'A compact line for service updates, delivery promises, or trust-building announcements.';
        form.value.button_text = 'Learn more';
        form.value.button_link = '/help-center';
        form.value.position = 'top_bar';
        form.value.bg_color = '#7c2d12';
        form.value.text_color = '#ffffff';
    }
}

async function handleSubmit() {
    saving.value = true;
    errorMessage.value = '';
    fieldErrors.value = {};

    try {
        let res;
        if (props.banner?.id) {
            res = await client.put(`/banners/${props.banner.id}`, form.value);
        } else {
            res = await client.post('/banners', form.value);
        }
        emit('saved', res.data.data || res.data);
        emit('close');
    } catch (err) {
        if (err.response?.status === 422 && err.response?.data?.errors) {
            fieldErrors.value = err.response.data.errors;
            errorMessage.value = err.response.data.message || 'Please fix the validation errors below.';
        } else {
            errorMessage.value = err.response?.data?.message || err.message || 'Failed to save banner.';
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
                        {{ banner ? 'Edit Banner Campaign' : 'Create Banner Campaign' }}
                    </h3>
                    <p class="text-xs text-slate-400">
                        Configure campaign headline, position, styling, and scheduling parameters.
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

            <!-- Presets strip -->
            <div v-if="!banner" class="mt-4 flex flex-wrap items-center gap-2 rounded-2xl bg-slate-50 p-3 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Quick Presets:</span>
                <button
                    type="button"
                    class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300"
                    @click="applyPreset('hero_launch')"
                >
                    Hero Launch
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-teal-50 px-2.5 py-1 text-xs font-bold text-teal-600 hover:bg-teal-100 dark:bg-teal-950 dark:text-teal-300"
                    @click="applyPreset('promo_strip')"
                >
                    Promo Strip
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-100 dark:bg-amber-950 dark:text-amber-300"
                    @click="applyPreset('top_notice')"
                >
                    Top Notice
                </button>
            </div>

            <div v-if="errorMessage" class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-600 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-400">
                {{ errorMessage }}
            </div>

            <form class="mt-4 space-y-4 max-h-[65vh] overflow-y-auto pr-1" @submit.prevent="handleSubmit">
                <!-- Title & Position -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <TextField
                            v-model="form.title"
                            label="Headline / Title *"
                            placeholder="e.g. Summer Mega Sale"
                        />
                        <p v-if="fieldErrors.title" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.title[0] }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Placement Position *
                        </label>
                        <select
                            v-model="form.position"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="hero">Hero (Main Carousel)</option>
                            <option value="promo">Promo Strip</option>
                            <option value="sidebar">Sidebar Banner</option>
                            <option value="top_bar">Top Bar Notice</option>
                        </select>
                        <p v-if="fieldErrors.position" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.position[0] }}</p>
                    </div>
                </div>

                <!-- Subtitle & Caption -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <TextField
                            v-model="form.subtitle"
                            label="Subtitle / Eyebrow"
                            placeholder="e.g. Limited Time Offer"
                        />
                        <p v-if="fieldErrors.subtitle" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.subtitle[0] }}</p>
                    </div>
                    <div>
                        <TextField
                            v-model="form.caption"
                            label="Caption / Description"
                            placeholder="e.g. Up to 50% off select products this week"
                        />
                        <p v-if="fieldErrors.caption" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.caption[0] }}</p>
                    </div>
                </div>

                <!-- Button Text & Link -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <TextField
                            v-model="form.button_text"
                            label="Button Label"
                            placeholder="e.g. Shop Now"
                        />
                        <p v-if="fieldErrors.button_text" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.button_text[0] }}</p>
                    </div>
                    <div>
                        <TextField
                            v-model="form.button_link"
                            label="Button Link URL"
                            placeholder="e.g. /products or https://..."
                        />
                        <p v-if="fieldErrors.button_link" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.button_link[0] }}</p>
                    </div>
                </div>

                <!-- Media Library Image Selection -->
                <div>
                    <ImageField
                        v-model="form.image_path"
                        label="Banner Image Asset"
                    />
                    <p v-if="fieldErrors.image_path" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.image_path[0] }}</p>
                </div>

                <!-- Colors & Order -->
                <div class="grid gap-4 sm:grid-cols-3">
                    <ColorField
                        v-model="form.bg_color"
                        label="Background Color *"
                    />
                    <ColorField
                        v-model="form.text_color"
                        label="Text Color *"
                    />
                    <div>
                        <TextField
                            v-model.number="form.sort_order"
                            label="Display Order"
                            type="number"
                        />
                        <p v-if="fieldErrors.sort_order" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.sort_order[0] }}</p>
                    </div>
                </div>

                <!-- Scheduling (Starts & Ends At) -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Starts At (Optional)
                        </label>
                        <input
                            v-model="form.starts_at"
                            type="datetime-local"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="fieldErrors.starts_at" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.starts_at[0] }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Ends At (Optional)
                        </label>
                        <input
                            v-model="form.ends_at"
                            type="datetime-local"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        />
                        <p v-if="fieldErrors.ends_at" class="mt-1 text-[10px] font-bold text-rose-500">{{ fieldErrors.ends_at[0] }}</p>
                    </div>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center justify-between rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                    <div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Active Status</span>
                        <p class="text-[10px] text-slate-400">Enable or disable this banner on the live storefront.</p>
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
                        <span>{{ saving ? 'Saving...' : (banner ? 'Save Changes' : 'Create Banner') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
