<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            // Polymorphic owner: User (avatar), Business (logo/cover),
            // Listing (gallery) - any model can attach media this way.
            $table->morphs('mediable'); // mediable_id, mediable_type

            // Which "slot" this image belongs to on its owner.
            // e.g. 'avatar', 'logo', 'cover', 'gallery'
            $table->string('collection')->default('default');

            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            // Only meaningful for multi-image collections like 'gallery'.
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id', 'collection']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};