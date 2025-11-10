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
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('phone_number');
            $table->index('user_type');
            $table->index(['user_type', 'location']); // Composite for filtering by type and location
            $table->index(['user_type', 'created_at']); // For recent users by type
        });

        // Applications table indexes
        Schema::table('applications', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('job_posting_id');
            $table->index('employer_id');
            $table->index('status');
            $table->index(['status', 'created_at']); // For status filtering with date ordering
            $table->index(['employer_id', 'status']); // For employer dashboard queries
            $table->index(['user_id', 'status']); // For job seeker application status
        });

        // Job postings table indexes
        Schema::table('job_postings', function (Blueprint $table) {
            $table->index('employer_id');
            $table->index('status');
            $table->index('location');
            $table->index(['status', 'created_at']); // For active jobs by date
            $table->index(['location', 'status']); // For location-based job searches
            $table->index(['employer_id', 'status']); // For employer job management
        });

        // Document validations table indexes (already has some, adding more)
        Schema::table('document_validations', function (Blueprint $table) {
            $table->index(['user_id', 'document_type']); // For user document queries
            $table->index(['validation_status', 'document_type']); // For admin validation queues
            $table->index(['is_valid', 'document_type']); // For valid document counts
            $table->index(['validated_at', 'validation_status']); // For recent validations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        Schema::table('document_validations', function (Blueprint $table) {
            $table->dropIndex(['validated_at', 'validation_status']);
            $table->dropIndex(['is_valid', 'document_type']);
            $table->dropIndex(['validation_status', 'document_type']);
            $table->dropIndex(['user_id', 'document_type']);
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex(['employer_id', 'status']);
            $table->dropIndex(['location', 'status']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex('location');
            $table->dropIndex('status');
            $table->dropIndex('employer_id');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['employer_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex('status');
            $table->dropIndex('employer_id');
            $table->dropIndex('job_posting_id');
            $table->dropIndex('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_type', 'created_at']);
            $table->dropIndex(['user_type', 'location']);
            $table->dropIndex('user_type');
            $table->dropIndex('phone_number');
            $table->dropIndex('email');
        });
    }
};
