<?php

namespace App\Services\Storefront;

use App\Models\StorefrontLayoutVersion;
use App\Models\StorefrontPage;
use App\Models\StorefrontSection;
use App\Services\Storefront\Exceptions\NoPublishedVersionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StorefrontLayoutService
{
    public function __construct(
        protected SectionRegistry $registry
    ) {
    }

    public function getPage(StorefrontPage|string $page): StorefrontPage
    {
        if ($page instanceof StorefrontPage) {
            return $page;
        }

        return StorefrontPage::firstOrCreate(
            ['key' => $page],
            ['label' => ucfirst($page)]
        );
    }

    public function editorSectionsFor(StorefrontPage|string $page, ?string $slot = null): Collection
    {
        $pageModel = $this->getPage($page);

        $query = StorefrontSection::where('page_id', $pageModel->id)->ordered();

        if ($slot !== null) {
            $query->slot($slot);
        }

        return $query->get();
    }

    public function publicSectionsFor(string $pageKey, ?string $slot = null): Collection
    {
        $pageModel = $this->getPage($pageKey);
        $latestPublished = StorefrontLayoutVersion::where('page_id', $pageModel->id)
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->first();

        if (!$latestPublished || empty($latestPublished->snapshot)) {
            Log::warning("StorefrontLayoutService: No published version found for page '{$pageKey}'. Falling back to live draft sections.");
            $draftSections = $this->editorSectionsFor($pageKey, $slot);
            return $draftSections->filter(fn ($s) => $s->is_active);
        }

        $snapshot = collect($latestPublished->snapshot);

        return $snapshot->filter(function ($item) use ($slot) {
            $isActive = is_array($item) ? ($item['is_active'] ?? true) : ($item->is_active ?? true);
            $itemSlot = is_array($item) ? ($item['slot'] ?? 'before') : ($item->slot ?? 'before');

            if (!$isActive) {
                return false;
            }

            if ($slot !== null && $itemSlot !== $slot) {
                return false;
            }

            return true;
        })->values();
    }

    public function addSection(StorefrontPage|string $page, string $type, ?int $afterOrder = null, string $slot = 'before'): StorefrontSection
    {
        return DB::transaction(function () use ($page, $type, $afterOrder, $slot) {
            $pageModel = $this->getPage($page);
            $contract = $this->registry->get($type);

            $defaults = $contract ? $contract->defaults() : [];

            $existing = StorefrontSection::where('page_id', $pageModel->id)
                ->where('slot', $slot)
                ->ordered()
                ->get();

            $targetOrder = $existing->count();

            if ($afterOrder !== null) {
                $targetOrder = $afterOrder + 1;
                foreach ($existing as $sec) {
                    if ($sec->order >= $targetOrder) {
                        $sec->increment('order');
                    }
                }
            }

            return StorefrontSection::create([
                'page_id' => $pageModel->id,
                'type' => $type,
                'order' => $targetOrder,
                'is_active' => true,
                'config' => $defaults,
                'style' => [],
                'schema_version' => 1,
                'slot' => $slot,
            ]);
        });
    }

    public function updateSection(int $sectionId, array $config, ?array $style = null): StorefrontSection
    {
        return DB::transaction(function () use ($sectionId, $config, $style) {
            $section = StorefrontSection::findOrFail($sectionId);

            $payload = ['config' => array_merge($section->config ?? [], $config)];
            if ($style !== null) {
                $payload['style'] = array_merge($section->style ?? [], $style);
            }

            $section->update($payload);
            return $section->fresh();
        });
    }

    public function reorderSections(StorefrontPage|string $page, array $orderedSectionIds): void
    {
        DB::transaction(function () use ($page, $orderedSectionIds) {
            $pageModel = $this->getPage($page);

            foreach ($orderedSectionIds as $index => $id) {
                StorefrontSection::where('page_id', $pageModel->id)
                    ->where('id', $id)
                    ->update(['order' => $index]);
            }
        });
    }

    public function deleteSection(int $sectionId): void
    {
        DB::transaction(function () use ($sectionId) {
            $section = StorefrontSection::findOrFail($sectionId);
            $pageId = $section->page_id;
            $slot = $section->slot;

            $section->delete();

            $remaining = StorefrontSection::where('page_id', $pageId)
                ->where('slot', $slot)
                ->ordered()
                ->get();

            foreach ($remaining as $index => $sec) {
                $sec->update(['order' => $index]);
            }
        });
    }

    public function toggleSection(int $sectionId, bool $isActive): StorefrontSection
    {
        return DB::transaction(function () use ($sectionId, $isActive) {
            $section = StorefrontSection::findOrFail($sectionId);
            $section->update(['is_active' => $isActive]);
            return $section->fresh();
        });
    }

    public function publishPage(StorefrontPage|string $page, ?string $note = null, ?int $publishedBy = null): StorefrontLayoutVersion
    {
        return DB::transaction(function () use ($page, $note, $publishedBy) {
            $pageModel = $this->getPage($page);

            // Archive prior published versions
            StorefrontLayoutVersion::where('page_id', $pageModel->id)
                ->where('status', 'published')
                ->update(['status' => 'archived']);

            $sections = StorefrontSection::where('page_id', $pageModel->id)
                ->ordered()
                ->get();

            $snapshot = $sections->map(function ($s) {
                return [
                    'id' => $s->id,
                    'type' => $s->type,
                    'order' => $s->order,
                    'is_active' => $s->is_active,
                    'config' => $s->config,
                    'style' => $s->style,
                    'schema_version' => $s->schema_version,
                    'slot' => $s->slot,
                ];
            })->toArray();

            return StorefrontLayoutVersion::create([
                'page_id' => $pageModel->id,
                'status' => 'published',
                'snapshot' => $snapshot,
                'note' => $note ?: 'Published layout version',
                'published_at' => now(),
                'published_by' => $publishedBy ?: auth()->id(),
            ]);
        });
    }

    public function discardDraft(StorefrontPage|string $page): Collection
    {
        return DB::transaction(function () use ($page) {
            $pageModel = $this->getPage($page);

            $latestPublished = StorefrontLayoutVersion::where('page_id', $pageModel->id)
                ->where('status', 'published')
                ->orderByDesc('created_at')
                ->first();

            if (!$latestPublished) {
                throw new NoPublishedVersionException("No published version exists to discard draft for page {$pageModel->key}.");
            }

            // Delete current draft sections
            StorefrontSection::where('page_id', $pageModel->id)->delete();

            // Re-create sections from snapshot
            $newSections = collect();
            foreach ($latestPublished->snapshot as $item) {
                $created = StorefrontSection::create([
                    'page_id' => $pageModel->id,
                    'type' => $item['type'],
                    'order' => $item['order'],
                    'is_active' => $item['is_active'],
                    'config' => $item['config'],
                    'style' => $item['style'],
                    'schema_version' => $item['schema_version'] ?? 1,
                    'slot' => $item['slot'] ?? 'before',
                ]);
                $newSections->push($created);
            }

            return $newSections;
        });
    }

    public function rollbackToVersion(StorefrontPage|string $page, int $versionId, ?int $publishedBy = null): StorefrontLayoutVersion
    {
        return DB::transaction(function () use ($page, $versionId, $publishedBy) {
            $pageModel = $this->getPage($page);
            $targetVersion = StorefrontLayoutVersion::where('page_id', $pageModel->id)
                ->where('id', $versionId)
                ->firstOrFail();

            // Restore draft sections from target version snapshot
            StorefrontSection::where('page_id', $pageModel->id)->delete();

            foreach ($targetVersion->snapshot as $item) {
                StorefrontSection::create([
                    'page_id' => $pageModel->id,
                    'type' => $item['type'],
                    'order' => $item['order'],
                    'is_active' => $item['is_active'],
                    'config' => $item['config'],
                    'style' => $item['style'],
                    'schema_version' => $item['schema_version'] ?? 1,
                    'slot' => $item['slot'] ?? 'before',
                ]);
            }

            // Immediately re-publish
            return $this->publishPage($pageModel, "Rollback to version #{$versionId}", $publishedBy);
        });
    }

    public function hasUnpublishedChanges(StorefrontPage|string $page): bool
    {
        $pageModel = $this->getPage($page);

        $latestPublished = StorefrontLayoutVersion::where('page_id', $pageModel->id)
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->first();

        if (!$latestPublished) {
            return StorefrontSection::where('page_id', $pageModel->id)->exists();
        }

        $draft = StorefrontSection::where('page_id', $pageModel->id)
            ->ordered()
            ->get()
            ->map(fn ($s) => [
                'type' => $s->type,
                'order' => (int) $s->order,
                'is_active' => (bool) $s->is_active,
                'config' => $s->config,
                'style' => $s->style,
                'slot' => $s->slot,
            ])->values()->toArray();

        $snapshot = collect($latestPublished->snapshot)->map(fn ($item) => [
            'type' => $item['type'],
            'order' => (int) $item['order'],
            'is_active' => (bool) $item['is_active'],
            'config' => $item['config'],
            'style' => $item['style'],
            'slot' => $item['slot'] ?? 'before',
        ])->values()->toArray();

        return md5(json_encode($draft)) !== md5(json_encode($snapshot));
    }
}
