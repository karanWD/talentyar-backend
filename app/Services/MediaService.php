<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MediaService
{
    public function addMediaFromUploadedFile(
        UploadedFile $file,
        string       $entity_slug,
        string       $type,
        string       $collection = 'default',
        string       $disk = 'public',
        array        $metaData = []
    )
    {
        if (!$file->isValid()) {
            throw new RuntimeException('Invalid uploaded file.');
        }

        // Compute hash (stream-safe)
        $hash = hash_file(Media::HASH_ALGORITHM, $file->getRealPath());

        $mime = $file->getMimeType() ?: 'application/octet-stream';
        $size = $file->getSize();
        $ext = $file->getClientOriginalExtension()
            ?: $this->guessExtFromMime($mime)
                ?: 'bin';

        // New physical file
        $hashedPath = $this->hashedPath($hash, $ext, $entity_slug);

        if (!Storage::disk($disk)->exists($hashedPath)) {
            Storage::disk($disk)->makeDirectory(dirname($hashedPath));

            Storage::disk($disk)->put(
                $hashedPath,
                fopen($file->getRealPath(), 'rb')
            );
        }

        return $this->createMediaRow(
            $hashedPath,
            $entity_slug,
            $type,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            $mime,
            $hash,
            $size,
            $collection,
            $disk,
            $metaData
        );
    }

    protected function hashedPath(
        string $hash,
        string $ext,
        string $entitySlug
    ): string
    {
        $entitySlug = trim(strtolower($entitySlug));

        return sprintf(
            'media/%s/%s/%s/%s.%s',
            $entitySlug,
            substr($hash, 0, 2),
            substr($hash, 2, 2),
            $hash,
            $ext
        );
    }

    protected function guessExtFromMime(?string $mime): ?string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'application/pdf' => 'pdf',
            default => str_contains($mime ?? '', '/')
                ? explode('/', $mime, 2)[1]
                : null,
        };
    }

    protected function createMediaRow(
        string $path,
        string $entity_slug,
        string $type,
        string $name,
        string $mime,
        string $hash,
        int    $size,
        string $collection,
        string $disk,
        array  $metaData
    ): Media
    {
        return Media::query()->create([
            'path' => $path,
            'entity_slug' => $entity_slug,
            'name' => $name,
            'mime_type' => $mime,
            'hash' => $hash,
            'size' => (string)$size,
            'collection' => $collection,
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'type' => $type,
            'disk' => $disk,
            'meta_data' => $metaData ? json_encode($metaData) : null,
        ]);
    }
}

