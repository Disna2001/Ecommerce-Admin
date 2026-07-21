import { defineStore } from 'pinia';
import client from '../api/client';
import { useDebounceFn } from '@vueuse/core';

export const useStudioStore = defineStore('studio', {
    state: () => ({
        pageKey: 'home',
        sections: [],
        registry: {},
        selectedSectionId: null,
        previewToken: '',
        previewUrl: '',
        loading: false,
        viewport: 'desktop', // desktop | tablet | mobile
        hasUnpublishedChanges: false,
        versions: [],
        isPublishing: false,
        isDiscarding: false,
        previewCacheBuster: 0,
    }),

    getters: {
        selectedSection(state) {
            if (!state.selectedSectionId || !Array.isArray(state.sections)) return null;
            return state.sections.find((s) => s.id === state.selectedSectionId) || null;
        },
        selectedSchema(state) {
            const section = this.selectedSection;
            if (!section) return [];
            return state.registry[section.type]?.schema || [];
        },
    },

    actions: {
        async loadRegistry() {
            const { data } = await client.get('/registry');
            this.registry = data;
        },

        async loadSections() {
            this.loading = true;
            try {
                const { data } = await client.get(`/layout/${this.pageKey}`);
                this.sections = Array.isArray(data) ? data : (data.data || []);
            } finally {
                this.loading = false;
            }
        },

        async refreshPreviewToken() {
            const { data } = await client.get(`/preview-token/${this.pageKey}`);
            this.previewToken = data.token;
            const base = window.location.origin + '/?studio_preview=' + encodeURIComponent(data.token);
            this.previewUrl = base;
        },

        async refreshPreview() {
            await this.refreshPreviewToken();
        },

        async addSection(type, afterOrder = null) {
            const { data } = await client.post('/sections', {
                page_key: this.pageKey,
                type,
                after_order: afterOrder,
            });
            await this.loadSections();
            await this.refreshPreview();
            await this.checkUnpublishedChanges();
            return data.data || data;
        },

        async updateSectionConfig(id, config) {
            const { data } = await client.patch(`/sections/${id}`, { config });
            const updated = data.data || data;
            if (Array.isArray(this.sections)) {
                const index = this.sections.findIndex((s) => s.id === id);
                if (index !== -1) this.sections[index] = updated;
            }
            await this.refreshPreview();
            await this.checkUnpublishedChanges();
        },

        async reorderSections(orderedIds) {
            await client.post('/reorder', {
                page_key: this.pageKey,
                ordered_ids: orderedIds,
            });
            await this.loadSections();
            await this.refreshPreview();
            await this.checkUnpublishedChanges();
        },

        async deleteSection(id) {
            await client.delete(`/sections/${id}`);
            if (this.selectedSectionId === id) this.selectedSectionId = null;
            await this.loadSections();
            await this.refreshPreview();
            await this.checkUnpublishedChanges();
        },

        async toggleSection(id, isActive) {
            const { data } = await client.patch(`/sections/${id}/toggle`, { is_active: isActive });
            const updated = data.data || data;
            if (Array.isArray(this.sections)) {
                const index = this.sections.findIndex((s) => s.id === id);
                if (index !== -1) this.sections[index] = updated;
            }
            await this.refreshPreview();
            await this.checkUnpublishedChanges();
        },

        selectSection(id) {
            this.selectedSectionId = id;
        },

        checkUnpublishedChanges: useDebounceFn(async function () {
            try {
                const { data } = await client.get(`/pages/${this.pageKey}/has-unpublished-changes`);
                this.hasUnpublishedChanges = !!data.has_unpublished_changes;
            } catch (e) {
                // ignore network errors during rapid edits
            }
        }, 800),

        async fetchVersions() {
            try {
                const { data } = await client.get(`/pages/${this.pageKey}/versions`);
                this.versions = Array.isArray(data) ? data : (data.data || []);
            } catch (e) {
                this.versions = [];
            }
        },

        bumpCacheBuster() {
            this.previewCacheBuster++;
        },

        buildPreviewUrl() {
            const sep = this.previewUrl.includes('?') ? '&' : '?';
            return `${this.previewUrl}${sep}_cb=${this.previewCacheBuster}`;
        },

        async publish(note = null) {
            this.isPublishing = true;
            try {
                const payload = note ? { note } : {};
                await client.post(`/pages/${this.pageKey}/publish`, payload);
                await this.fetchVersions();
                this.hasUnpublishedChanges = false;
                this.bumpCacheBuster();
            } finally {
                this.isPublishing = false;
            }
        },

        async discardDraft() {
            this.isDiscarding = true;
            try {
                const { data } = await client.post(`/pages/${this.pageKey}/discard-draft`);
                this.sections = Array.isArray(data) ? data : (data.data || []);
                await this.fetchVersions();
                this.hasUnpublishedChanges = false;
                this.bumpCacheBuster();
                await this.refreshPreview();
            } finally {
                this.isDiscarding = false;
            }
        },

        async rollback(versionId) {
            const { data } = await client.post(`/pages/${this.pageKey}/rollback/${versionId}`, {});
            await this.fetchVersions();
            this.hasUnpublishedChanges = false;
            this.bumpCacheBuster();
            await this.refreshPreview();
            return data.data || data;
        },
    },
});
