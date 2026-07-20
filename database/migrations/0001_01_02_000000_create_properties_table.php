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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->enum('property_type', ['apartment', 'house', 'condo', 'room', 'boarding_house']);
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->decimal('floor_area', 8, 2)->nullable();
            $table->decimal('monthly_rent', 10, 2);
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->text('address');
            $table->string('city')->default('Digos City');
            $table->string('province')->default('Davao del Sur');
            $table->string('barangay')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available');
            $table->enum('furnishing_status', ['furnished', 'semi_furnished', 'unfurnished'])->default('unfurnished');
            $table->integer('parking_spaces')->default(0);
            $table->enum('pet_policy', ['allowed', 'not_allowed', 'negotiable'])->default('not_allowed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
