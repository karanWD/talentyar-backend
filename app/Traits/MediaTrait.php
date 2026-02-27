<?php

namespace App\Traits;

use App\Models\Media;
use RuntimeException;

trait MediaTrait
{
    /* ---------------- relations ---------------- */

    public function media()
    {
        return $this->morphMany(Media::class, 'entity');
    }

    public function getFirstMediaAttribute()
    {
        return $this->media()->first();
    }

    public function getMediaAttribute()
    {
        return $this->media()->get();
    }

    public function assignMedia(Media $media, ?string $collection = null): Media
    {
        $data = [
            'entity_type' => $this->getMorphClass(),
            'entity_id' => $this->getKey(),
        ];
        if ($collection !== null) {
            $data['collection'] = $collection;
        }
        $media->update($data);

        return $media;
    }

}
