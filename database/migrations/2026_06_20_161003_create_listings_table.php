<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            // One table, every listing type. Keeps booking/calendar logic uniform.
            $table->enum('type', [
                'dormitory', 'house', 'pad', 'hotel', 'inn', 'motel', 'restaurant',
            ]);

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Accommodation-specific (null for restaurants)
            $table->unsignedInteger('max_guests')->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();

            // Restaurant-specific (null for accommodations)
            $table->unsignedInteger('seating_capacity')->nullable();
            $table->string('cuisine_type')->nullable();

            // Pricing: accommodations price per night, restaurants may price per reservation/cover
            $table->decimal('base_price', 10, 2);
            $table->enum('price_unit', ['per_night', 'per_reservation', 'per_person'])->default('per_night');
            $table->string('currency', 3)->default('PHP');

            $table->json('amenities')->nullable();
            $table->json('design_settings')->nullable();

            $table->enum('status', ['draft', 'pending_review', 'published', 'rejected', 'suspended'])
                ->default('draft');
            $table->text('rejection_reason')->nullable();

            $table->boolean('is_active')->default(true);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status', 'is_active']);
            $table->index(['city', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};