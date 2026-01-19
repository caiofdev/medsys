<?php

namespace Tests\Unit\Services;

use App\Infrastructure\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadServiceTest extends TestCase
{
    private FileUploadService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->service = new FileUploadService();
    }

    public function test_can_upload_file(): void
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $path = $this->service->upload($file, 'photos');

        $this->assertStringStartsWith('photos/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_can_delete_existing_file(): void
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $path = $this->service->upload($file, 'photos');

        $deleted = $this->service->delete($path);

        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_returns_false_when_deleting_non_existing_file(): void
    {
        $deleted = $this->service->delete('photos/non-existing.jpg');

        $this->assertFalse($deleted);
    }

    public function test_can_update_file(): void
    {
        $oldFile = UploadedFile::fake()->image('old.jpg');
        $oldPath = $this->service->upload($oldFile, 'photos');

        $newFile = UploadedFile::fake()->image('new.jpg');

        $newPath = $this->service->update($oldPath, $newFile, 'photos');

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
        $this->assertNotEquals($oldPath, $newPath);
    }

    public function test_can_update_file_when_no_old_file_exists(): void
    {
        $newFile = UploadedFile::fake()->image('new.jpg');

        $newPath = $this->service->update(null, $newFile, 'photos');

        Storage::disk('public')->assertExists($newPath);
    }
}