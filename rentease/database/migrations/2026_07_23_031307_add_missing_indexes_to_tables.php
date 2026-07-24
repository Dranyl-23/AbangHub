<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->index('status');
            $table->index('is_banned');
            $table->index('city');
            $table->index('property_type');
            $table->index('monthly_rent');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('user_type');
            $table->index('is_banned');
            $table->index('is_verified');
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['is_banned']);
            $table->dropIndex(['city']);
            $table->dropIndex(['property_type']);
            $table->dropIndex(['monthly_rent']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_type']);
            $table->dropIndex(['is_banned']);
            $table->dropIndex(['is_verified']);
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['due_date']);
        });
    }
};
