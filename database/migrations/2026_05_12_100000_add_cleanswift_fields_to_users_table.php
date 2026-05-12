<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('customer')->after('password');
            $table->string('phone', 32)->nullable()->after('role');
            $table->string('profile_photo_path')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('profile_photo_path');
            $table->string('staff_application_status', 32)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'profile_photo_path',
                'is_active',
                'staff_application_status',
            ]);
        });
    }
};
