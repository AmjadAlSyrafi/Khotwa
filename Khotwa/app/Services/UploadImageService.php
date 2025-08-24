<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadImageService
{
    public function save(UploadedFile $file, string $dir, ?string $oldPath = null): string
    {
        $storedPath = $file->storePublicly($dir, 'public'); // e.g. images/volunteers/xxxx.jpg

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return $storedPath;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
