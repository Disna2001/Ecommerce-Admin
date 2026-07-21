<?php

namespace Tests\Feature\Services;

use App\Models\MediaAsset;

use App\Models\User;
use App\Services\Storefront\MediaLibraryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected MediaLibraryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MediaLibraryService::class);
        Storage::fake('public');
    }

    public function test_can_upload_media_asset(): void
    {
        $file = UploadedFile::fake()->image('banner.jpg', 800, 600);

        $asset = $this->service->upload($file);

        $this->assertInstanceOf(MediaAsset::class, $asset);
        $this->assertEquals('banner.jpg', $asset->name);
        $this->assertDatabaseHas('media_assets', ['id' => $asset->id]);
    }

    public function test_can_list_and_delete_media_asset(): void
    {
        $file = UploadedFile::fake()->image('banner.png', 400, 300);
        $asset = $this->service->upload($file);

        $assets = $this->service->listAssets();
        $this->assertCount(1, $assets);

        $this->service->delete($asset->id);
        $this->assertDatabaseMissing('media_assets', ['id' => $asset->id]);
    }
}
