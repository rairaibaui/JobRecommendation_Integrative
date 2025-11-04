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
        Schema::create('document_validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('document_type'); // 'business_permit', 'resume', etc.
            $table->string('file_path');
            $table->boolean('is_valid')->default(false);
            $table->integer('confidence_score')->default(0); // 0-100
            $table->string('validation_status'); // 'approved', 'rejected', 'pending_review'
            $table->text('reason')->nullable(); // AI's reason for decision
            $table->json('ai_analysis')->nullable(); // Full AI analysis data
            $table->string('validated_by')->nullable(); // 'ai' or 'manual' or admin user ID
            $table->timestamp('validated_at')->nullable();
            $table->text('admin_notes')->nullable(); // For manual review notes
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for common queries
            $table->index('user_id');
            $table->index('document_type');
            $table->index('validation_status');
            $table->index('is_valid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_validations');
    }
};
