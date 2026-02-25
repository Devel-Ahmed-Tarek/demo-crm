<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadHelper
{
    public static function uploadFile(string $path, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name = time() . rand(100, 999) . '.' . $extension;
        $directory = public_path('uploads/' . trim($path, '/'));

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0775, true);
        }

        $file->move($directory, $name);

        return 'uploads/' . trim($path, '/') . '/' . $name;
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $normalized = ltrim($path, '/');

        if (Str::startsWith($normalized, 'uploads/')) {
            return asset($normalized);
        }

        if (Str::startsWith($normalized, 'storage/')) {
            return asset($normalized);
        }

        return asset('storage/' . $normalized);
    }

    public static function deleteFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $normalized = ltrim($path, '/');

        if (Str::startsWith($normalized, 'uploads/')) {
            $fullPath = public_path($normalized);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            return;
        }

        Storage::disk('public')->delete($normalized);
    }

    public static function absolutePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalized = ltrim($path, '/');

        if (Str::startsWith($normalized, 'uploads/')) {
            $fullPath = public_path($normalized);
        } elseif (Str::startsWith($normalized, 'storage/')) {
            $fullPath = public_path($normalized);
        } else {
            $fullPath = Storage::disk('public')->path($normalized);
        }

        return File::exists($fullPath) ? $fullPath : null;
    }
}

