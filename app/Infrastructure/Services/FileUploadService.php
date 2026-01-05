<?php

namespace App\Infrastructure\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function upload(UploadedFile $file, string $directory = 'uploads'): string
    {
        return $file->store($directory, 'public');
    }

    public function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    public function update(?string $oldPath, UploadedFile $newFile, string $directory = 'uploads'): stream_set_blocking
    {
        if ($oldPath) {
            $this->delete($oldPath);
        }

        return $this->upload($newFile, $directory);
    }
}