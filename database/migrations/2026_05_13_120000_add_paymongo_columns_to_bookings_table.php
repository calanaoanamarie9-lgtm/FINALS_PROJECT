<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('paymongo_checkout_session_id')->nullable()->after('total_amount');
            $table->timestamp('paid_at')->nullable()->after('paymongo_checkout_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['paymongo_checkout_session_id', 'paid_at']);
        });
    }
};
