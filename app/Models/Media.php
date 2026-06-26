<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'collection', 'disk', 'path', 'original_filename', 'mime_type',
    'size_bytes', 'is_primary', 'sort_order',
])]
class Media extends Model
{
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'size_bytes' => 'integer',
        ];
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Delete both the database row and the underlying file on disk.
     * Always use this instead of ->delete() alone, or the physical
     * file will be orphaned in storage forever.
     */
    public function deleteWithFile(): bool
    {
        Storage::disk($this->disk)->delete($this->path);

        return $this->delete();
    }
}
