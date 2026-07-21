<script setup>
import { useStudioStore } from '../store/studio';

const store = useStudioStore();

const pages = [
    { key: 'home', label: 'Home Page' },
    { key: 'product', label: 'Product Details' },
    { key: 'cart', label: 'Shopping Cart' },
    { key: 'checkout', label: 'Checkout Page' },
    { key: 'wishlist', label: 'Saved Wishlist' },
];

async function selectPage(key) {
    store.pageKey = key;
    await store.loadSections();
    await store.refreshPreview();
    await store.fetchVersions();
}
</script>

<template>
    <div class="relative">
        <select
            :value="store.pageKey"
            class="rounded-xl border border-slate-200 bg-white py-1.5 pl-3 pr-8 text-xs font-bold text-slate-800 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
            @change="selectPage($event.target.value)"
        >
            <option v-for="p in pages" :key="p.key" :value="p.key">
                {{ p.label }}
            </option>
        </select>
    </div>
</template>
