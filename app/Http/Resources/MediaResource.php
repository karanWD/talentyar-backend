<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class MediaResource extends BaseResource
{
    /**
     * Get the resource data array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'url' => $this->url,
            'hash' => $this->hash,
            'extension' => $this->extension,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'size_formatted' => $this->size_formatted,
            'alt' => $this->alt,
            'type' => $this->type,
            'collection' => $this->collection,
            'disk' => $this->disk,
            'meta_data' => $this->meta_data,
            'entity_slug' => $this->entity_slug,
            ...$this->includeTimestamps(),
        ];
    }
}

