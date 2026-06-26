<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // customer
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete(); // denormalized for fast owner queries

            // For accommodations: check_in/check_out are the date range.
            // For restaurants: check_in holds the reservation date+time, check_out is null.
            $table->date('check_in');
            $table->date('check_out')->nullable();
            $table->time('reservation_time')->nullable(); // restaurants only
            $table->unsignedInteger('guests')->default(1);

            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity')->default(1); // nights, or 1 for a restaurant slot
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('PHP');

            $table->enum('status', [
                'pending', 'confirmed', 'cancelled', 'completed', 'no_show', 'rejected',
            ])->default('pending');

            $table->enum('payment_status', ['unpaid', 'paid', 'refunded', 'partially_refunded'])
                ->default('unpaid');

            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(['listing_unit_id', 'check_in', 'check_out']);
            $table->index(['business_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};