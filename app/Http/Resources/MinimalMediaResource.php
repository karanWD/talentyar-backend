<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class MinimalMediaResource extends BaseResource
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
            'name' => $this->name,
            'url' => $this->url,
            'hash' => $this->hash,
            'type' => $this->type,
            'entity_slug' => $this->entity_slug,
        ];
    }
}

