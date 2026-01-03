<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Base Resource class for consistent API resource structure
 */
abstract class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->getResourceData($request);
    }

    /**
     * Get the resource data array.
     * Override this method in child classes to define specific resource data.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    abstract protected function getResourceData(Request $request): array;

    /**
     * Include timestamps if they exist on the model.
     *
     * @return array<string, mixed>
     */
    protected function includeTimestamps(): array
    {
        $timestamps = [];

        if ($this->resource && method_exists($this->resource, 'getCreatedAtColumn')) {
            if ($this->resource->created_at) {
                $timestamps['created_at'] = $this->resource->created_at->toIso8601String();
            }
            if ($this->resource->updated_at) {
                $timestamps['updated_at'] = $this->resource->updated_at->toIso8601String();
            }
        }

        return $timestamps;
    }
}

