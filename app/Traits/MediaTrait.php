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

    public function assignMedia(Media $media): Media
    {
        $media->update([
            'entity_type' => $this->getMorphClass(),
            'entity_id' => $this->getKey(),
        ]);

        return $media;
    }

}
