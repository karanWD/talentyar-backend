<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'path',
        'hash',
        'name',
        'extension',
        'mime_type',
        'size',
        'alt',
        'type',
        'meta_data',
        'entity_id',
        'entity_type',
        'entity_slug',
        'collection',
        'disk',
    ];
    const HASH_ALGORITHM = 'sha256';


    const TYPE_NONE = 'none';
    const TYPE_COVER = 'cover';
    const TYPE_FILE = 'file';
    const TYPE_DOC = 'doc';
    const TYPE_PROFILE = 'profile';
    const TYPE_GALLERY = 'gallery';
    const TYPE_LOGO = 'logo';
    const TYPE_BANNER = 'banner';
    const TYPE_THUMBNAIL = 'thumbnail';
    const TYPE = [
        self::TYPE_NONE,
        self::TYPE_DOC,
        self::TYPE_PROFILE,
        self::TYPE_COVER,
        self::TYPE_FILE,
        self::TYPE_GALLERY,
        self::TYPE_LOGO,
        self::TYPE_BANNER,
        self::TYPE_THUMBNAIL,
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }


    public function getMetaDataAttribute($value)
    {
        return json_decode($value);
    }

    public function setMetaDataAttribute($value)
    {
        $this->attributes['meta_data'] = json_encode($value);
    }

    public function getSizeFormattedAttribute(): string
    {
        return $this->format_bytes($this->size);
    }

    private function format_bytes($size)
    {
        $size = (int)$size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }


    public function getUrlAttribute()
    {
        $disk = $this->disk ?? 'public';

        // For public disk, construct the URL directly
        if ($disk === 'public') {
            return config('app.url') . '/storage/' . ltrim($this->path, '/');
        }

        // For other disks, you may need to implement a route/controller to serve files
        // For now, return the storage URL
        return config('app.url') . '/storage/' . ltrim($this->path, '/');
    }
}
