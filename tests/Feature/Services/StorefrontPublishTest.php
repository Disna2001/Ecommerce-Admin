<?php

namespace Tests\Feature\Services;

use App\Models\StorefrontLayoutVersion;
use App\Models\StorefrontPage;
use App\Models\StorefrontSection;
use App\Services\Storefront\StorefrontLayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontPublishTest extends TestCase
{
    use RefreshDatabase;

    protected StorefrontLayoutService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StorefrontLayoutService::class);
    }

    public function test_can_publish_page_and_create_version(): void
    {
        $sec = $this->service->addSection('home', 'hero');

        $version = $this->service->publishPage('home', 'Initial publish');

        $this->assertInstanceOf(StorefrontLayoutVersion::class, $version);
        $this->assertEquals('published', $version->status);
        $this->assertEquals('Initial publish', $version->note);
        $this->assertCount(1, $version->snapshot);
    }

    public function test_has_unpublished_changes_detection(): void
    {
        $sec = $this->service->addSection('home', 'hero');
        $this->service->publishPage('home');

        $this->assertFalse($this->service->hasUnpublishedChanges('home'));

        $this->service->updateSection($sec->id, ['heading' => 'New Heading']);

        $this->assertTrue($this->service->hasUnpublishedChanges('home'));
    }

    public function test_can_discard_draft_changes(): void
    {
        $sec = $this->service->addSection('home', 'hero');
        $this->service->updateSection($sec->id, ['heading' => 'Published Heading']);
        $this->service->publishPage('home');

        $this->service->updateSection($sec->id, ['heading' => 'Draft Unsaved Heading']);
        $this->assertTrue($this->service->hasUnpublishedChanges('home'));

        $this->service->discardDraft('home');

        $this->assertFalse($this->service->hasUnpublishedChanges('home'));
        $pageModel = StorefrontPage::where('key', 'home')->first();
        $restoredSection = StorefrontSection::where('page_id', $pageModel->id)->first();
        $this->assertEquals('Published Heading', $restoredSection->config['heading']);
    }

    public function test_can_rollback_to_previous_version(): void
    {
        $sec = $this->service->addSection('home', 'hero');
        $this->service->updateSection($sec->id, ['heading' => 'Version 1']);
        $v1 = $this->service->publishPage('home', 'V1');

        $this->service->updateSection($sec->id, ['heading' => 'Version 2']);
        $v2 = $this->service->publishPage('home', 'V2');

        $rollbackVersion = $this->service->rollbackToVersion('home', $v1->id);

        $this->assertDatabaseHas('storefront_layout_versions', [
            'id' => $rollbackVersion->id,
            'status' => 'published',
        ]);
        $this->assertFalse($this->service->hasUnpublishedChanges('home'));
    }

    public function test_bootstrap_publish_command(): void
    {
        $sec = $this->service->addSection('home', 'hero');

        $this->artisan('storefront:bootstrap-publish')
            ->assertExitCode(0);

        $this->assertDatabaseHas('storefront_layout_versions', [
            'status' => 'published',
            'note' => 'Initial bootstrap publish',
        ]);
    }
}
