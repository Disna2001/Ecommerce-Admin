<script setup>
import { ref, onMounted } from 'vue';
import OverviewTab from './tabs/OverviewTab.vue';
import PagesTab from './tabs/PagesTab.vue';
import ThemeTab from './tabs/ThemeTab.vue';
import BannersTab from './tabs/BannersTab.vue';
import DiscountsTab from './tabs/DiscountsTab.vue';
import ReviewsTab from './tabs/ReviewsTab.vue';

const tabs = [
    { key: 'overview', label: 'Overview', icon: 'fa-gauge-high' },
    { key: 'pages', label: 'Pages', icon: 'fa-layer-group' },
    { key: 'theme', label: 'Theme', icon: 'fa-palette' },
    { key: 'banners', label: 'Banners', icon: 'fa-image' },
    { key: 'discounts', label: 'Discounts', icon: 'fa-tags' },
    { key: 'reviews', label: 'Reviews', icon: 'fa-star' },
];

const active = ref('pages');

function selectTab(key) {
    active.value = key;
    const url = new URL(window.location);
    url.searchParams.set('tab', key);
    window.history.replaceState({}, '', url);
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const tabParam = params.get('tab');
    if (tabParam && tabs.some((t) => t.key === tabParam)) {
        active.value = tabParam;
    }
});
</script>

<template>
    <div class="flex h-[calc(100vh-5.4rem)] lg:h-[calc(100vh-6.8rem)] flex-col bg-slate-50 dark:bg-slate-900 overflow-hidden">
        <nav class="flex shrink-0 items-center gap-1 border-b border-slate-200 bg-white px-4 dark:border-slate-700 dark:bg-slate-800">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                type="button"
                class="flex items-center gap-2 px-4 py-3 text-sm font-semibold transition border-b-2"
                :class="active === tab.key
                    ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                    : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                @click="selectTab(tab.key)"
            >
                <i class="fas" :class="tab.icon"></i>
                <span>{{ tab.label }}</span>
            </button>
        </nav>

        <div class="min-h-0 flex-1 overflow-y-auto">
            <OverviewTab v-if="active === 'overview'" @navigate="selectTab" />
            <PagesTab v-else-if="active === 'pages'" />
            <ThemeTab v-else-if="active === 'theme'" />
            <BannersTab v-else-if="active === 'banners'" />
            <DiscountsTab v-else-if="active === 'discounts'" />
            <ReviewsTab v-else-if="active === 'reviews'" />
        </div>
    </div>
</template>
