<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_channel', 64)->nullable()->after('paymongo_checkout_session_id');
            $table->string('paymongo_payment_id', 64)->nullable()->after('payment_channel');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_channel', 'paymongo_payment_id']);
        });
    }
};
