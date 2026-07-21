<script setup>
import { ref, onMounted, watch } from 'vue';
import client from '../../api/client';
import { useDebounceFn } from '@vueuse/core';
import { reportError } from '../../errorReporter';
import TextField from '../fields/TextField.vue';
import ColorField from '../fields/ColorField.vue';
import ImageField from '../fields/ImageField.vue';

const loading = ref(true);
const saving = ref(false);
const lastSaved = ref(null);

const form = ref({
    site_name: '',
    site_tagline: '',
    logo_path: '',
    favicon_path: '',
    primary_color: '#4f46e5',
    secondary_color: '#7c3aed',
    accent_color: '#06b6d4',
    text_color: '#111827',
    bg_color: '#f8fafc',
    nav_bg_color: '#ffffff',
    heading_font: 'Plus Jakarta Sans',
    body_font: 'Figtree',
});

const FONT_OPTIONS = [
    { value: 'Plus Jakarta Sans', label: 'Plus Jakarta Sans' },
    { value: 'Outfit', label: 'Outfit' },
    { value: 'Figtree', label: 'Figtree' },
];

async function loadSettings() {
    loading.value = true;
    try {
        const { data } = await client.get('/theme');
        form.value = { ...form.value, ...data };
    } catch (e) {
        reportError('Failed to load theme settings.', e?.message ?? String(e));
    } finally {
        loading.value = false;
    }
}

const save = useDebounceFn(async (payload) => {
    saving.value = true;
    try {
        await client.patch('/theme', payload);
        lastSaved.value = new Date();
    } catch (e) {
        reportError('Failed to save theme settings.', e?.message ?? String(e));
    } finally {
        saving.value = false;
    }
}, 400);

function updateField(key, value) {
    form.value[key] = value;
    save({ ...form.value });
}

onMounted(() => {
    loadSettings();
});
</script>

<template>
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-200 p-4 dark:border-slate-700">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                Theme & Branding
            </h3>
            <p class="mt-0.5 text-[10px] text-slate-400">
                Manage fonts, colors, and brand assets. Changes save automatically.
            </p>
        </div>

        <div v-if="loading" class="flex flex-1 items-center justify-center p-6 text-xs text-slate-400">
            Loading theme settings...
        </div>

        <div v-else class="flex min-h-0 flex-1 overflow-y-auto">
            <div class="flex-1 space-y-6 p-4">
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Branding</h4>
                        <TextField
                            label="Site Name"
                            :model-value="form.site_name"
                            @update:model-value="updateField('site_name', $event)"
                        />
                        <TextField
                            label="Site Tagline"
                            :model-value="form.site_tagline"
                            @update:model-value="updateField('site_tagline', $event)"
                        />
                        <ImageField
                            label="Logo"
                            :model-value="form.logo_path"
                            @update:model-value="updateField('logo_path', $event)"
                        />
                        <ImageField
                            label="Favicon"
                            :model-value="form.favicon_path"
                            @update:model-value="updateField('favicon_path', $event)"
                        />
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Fonts</h4>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Heading Font</label>
                            <select
                                :value="form.heading_font"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                @change="updateField('heading_font', $event.target.value)"
                            >
                                <option v-for="font in FONT_OPTIONS" :key="font.value" :value="font.value">
                                    {{ font.label }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Body Font</label>
                            <select
                                :value="form.body_font"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                @change="updateField('body_font', $event.target.value)"
                            >
                                <option v-for="font in FONT_OPTIONS" :key="font.value" :value="font.value">
                                    {{ font.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Palette</h4>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <ColorField
                            label="Primary"
                            :model-value="form.primary_color"
                            @update:model-value="updateField('primary_color', $event)"
                        />
                        <ColorField
                            label="Secondary"
                            :model-value="form.secondary_color"
                            @update:model-value="updateField('secondary_color', $event)"
                        />
                        <ColorField
                            label="Accent"
                            :model-value="form.accent_color"
                            @update:model-value="updateField('accent_color', $event)"
                        />
                        <ColorField
                            label="Text"
                            :model-value="form.text_color"
                            @update:model-value="updateField('text_color', $event)"
                        />
                        <ColorField
                            label="Background"
                            :model-value="form.bg_color"
                            @update:model-value="updateField('bg_color', $event)"
                        />
                        <ColorField
                            label="Nav Background"
                            :model-value="form.nav_bg_color"
                            @update:model-value="updateField('nav_bg_color', $event)"
                        />
                    </div>
                </div>
            </div>

            <aside class="w-[320px] shrink-0 border-l border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800 overflow-y-auto hidden lg:block">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-4">Live Preview</h4>
                <div
                    class="rounded-2xl border border-slate-200 p-6 shadow-sm dark:border-slate-700"
                    :style="{
                        background: form.bg_color,
                        color: form.text_color,
                        fontFamily: form.body_font + ', sans-serif',
                    }"
                >
                    <div
                        class="mb-4 h-1 w-full rounded-full"
                        :style="{ background: 'linear-gradient(90deg, ' + form.primary_color + ', ' + form.secondary_color + ')' }"
                    ></div>
                    <h3
                        class="text-lg font-black mb-2"
                        :style="{ fontFamily: form.heading_font + ', sans-serif', color: form.primary_color }"
                    >
                        {{ form.site_name || 'Store Name' }}
                    </h3>
                    <p class="text-xs opacity-70 mb-4">{{ form.site_tagline || 'Your tagline here' }}</p>
                    <div class="flex gap-2">
                        <span
                            class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white"
                            :style="{ background: form.primary_color }"
                        >
                            Primary
                        </span>
                        <span
                            class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white"
                            :style="{ background: form.secondary_color }"
                        >
                            Secondary
                        </span>
                        <span
                            class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white"
                            :style="{ background: form.accent_color }"
                        >
                            Accent
                        </span>
                    </div>
                    <div
                        class="mt-4 rounded-xl p-3 text-xs font-medium"
                        :style="{ background: form.nav_bg_color, color: form.text_color, border: '1px solid rgba(0,0,0,0.05)' }"
                    >
                        Nav bar preview
                    </div>
                </div>
                <div v-if="form.logo_path" class="mt-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Logo</p>
                    <img :src="form.logo_path" class="h-10 object-contain" alt="Logo preview" />
                </div>
                <div v-if="form.favicon_path" class="mt-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Favicon</p>
                    <img :src="form.favicon_path" class="h-8 w-8 rounded object-contain" alt="Favicon preview" />
                </div>
            </aside>
        </div>
    </div>
</template>
