<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Gives a model the ability to attach uploaded images via the polymorphic
 * `media` table. Use on User (avatar), Business (logo/cover), Listing
 * (gallery) - or any future model that needs image uploads.
 *
 * Two usage patterns:
 *  - Single-image "slot" collections (avatar, logo, cover):
 *        $user->setSingleMedia('avatar', $request->file('avatar'));
 *        $user->mediaUrl('avatar');
 *        $user->clearMedia('avatar');
 *
 *  - Multi-image collections (gallery):
 *        $listing->addGalleryMedia($request->file('images'));
 *        $listing->media('gallery'); // Collection of Media
 */
trait HasMedia
{
    public function allMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    /**
     * All media in a given collection, e.g. media('gallery').
     */
    public function media(string $collection = 'default')
    {
        return $this->allMedia->where('collection', $collection)->values();
    }

    /**
     * The single media item for a "slot" collection like avatar/logo/cover.
     */
    public function singleMedia(string $collection): ?Media
    {
        return $this->allMedia()->where('collection', $collection)->first();
    }

    public function mediaUrl(string $collection): ?string
    {
        return $this->singleMedia($collection)?->url();
    }

    /**
     * Replace whatever is currently in a single-image slot with a new
     * upload. Deletes the old file from disk first, so slots never
     * accumulate orphaned files.
     */
    public function setSingleMedia(string $collection, UploadedFile $file): Media
    {
        $this->clearMedia($collection);

        return $this->storeMediaFile($file, $collection, true, 0);
    }

    /**
     * Remove every media item in a collection, deleting the underlying
     * files from disk as well as the database rows.
     */
    public function clearMedia(string $collection): void
    {
        $this->allMedia()->where('collection', $collection)->get()
            ->each(fn (Media $media) => $media->deleteWithFile());
    }

    /**
     * Add one image to a multi-image collection (e.g. 'gallery').
     * The first image added to an empty collection becomes primary.
     */
    public function addGalleryMedia(UploadedFile $file, string $collection = 'gallery'): Media
    {
        $hasExistingPrimary = $this->allMedia()
            ->where('collection', $collection)
            ->where('is_primary', true)
            ->exists();

        $nextOrder = $this->allMedia()->where('collection', $collection)->count();

        return $this->storeMediaFile($file, $collection, ! $hasExistingPrimary && $nextOrder === 0, $nextOrder);
    }

    protected function storeMediaFile(UploadedFile $file, string $collection, bool $isPrimary, int $sortOrder): Media
    {
        $folder = Str::snake(class_basename($this)).'/'.$this->getKey();
        $path = $file->store($folder, 'public');

        return $this->allMedia()->create([
            'collection' => $collection,
            'disk' => 'public',
            'path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder,
        ]);
    }
}
