<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();

            // e.g. "Room 101", "Bed A - Dorm 3", "Table 5 (Window)"
            $table->string('name');
            $table->unsignedInteger('capacity')->default(1);
            $table->decimal('price_override', 10, 2)->nullable(); // null = use listing base_price
            $table->unsignedInteger('quantity')->default(1); // identical units available (e.g. 5 standard rooms)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_units');
    }
};