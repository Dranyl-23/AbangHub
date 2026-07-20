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
        Schema::create('landlord_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('document_type'); // e.g., 'dti_sec', 'barangay_clearance', 'mayors_permit', 'bir_2303', 'fire_safety', 'sanitary_permit'
            $table->string('file_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved'); // Auto-approved for demo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlord_documents');
    }
};
