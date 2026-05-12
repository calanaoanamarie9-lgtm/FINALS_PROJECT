<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_checkouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->json('payload');
            $table->decimal('total_amount', 12, 2);
            $table->string('paymongo_checkout_session_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_channel', 64)->nullable();
            $table->string('paymongo_payment_id', 64)->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'paid_at', 'booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_checkouts');
    }
};
