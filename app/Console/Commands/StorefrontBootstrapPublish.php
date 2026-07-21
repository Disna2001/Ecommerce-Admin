<?php

namespace App\Console\Commands;

use App\Models\StorefrontPage;
use App\Services\Storefront\StorefrontLayoutService;
use Illuminate\Console\Command;

class StorefrontBootstrapPublish extends Command
{
    protected $signature = 'storefront:bootstrap-publish';

    protected $description = 'Publish initial layout versions for any page with sections but no published version.';

    public function handle(StorefrontLayoutService $layoutService): int
    {
        $pages = StorefrontPage::all();
        $publishedCount = 0;

        foreach ($pages as $page) {
            $hasPublished = $page->versions()->where('status', 'published')->exists();
            $hasSections = $page->sections()->exists();

            if ($hasSections && !$hasPublished) {
                $layoutService->publishPage($page, 'Initial bootstrap publish');
                $this->info("Bootstrapped publication for page: {$page->key}");
                $publishedCount++;
            }
        }

        $this->info("Completed bootstrap publish pass. Pages published: {$publishedCount}");
        return Command::SUCCESS;
    }
}
