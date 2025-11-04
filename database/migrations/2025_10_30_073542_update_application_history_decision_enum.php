<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN for enums, so we need to recreate the table
        
        // 1. Drop old table and recreate with updated enum
        Schema::dropIfExists('application_history');
        
        // 2. Create new table with updated enum
        Schema::create('application_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_seeker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_posting_id')->nullable()->constrained('job_postings')->onDelete('set null');
            
            $table->string('job_title');
            $table->string('company_name')->nullable();
            $table->enum('decision', ['hired', 'rejected', 'terminated', 'resigned']); // Updated enum
            $table->text('rejection_reason')->nullable();
            $table->json('applicant_snapshot')->nullable();
            $table->json('job_snapshot')->nullable();
            $table->timestamp('decision_date');
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['employer_id', 'decision']);
            $table->index(['job_seeker_id', 'decision']);
            $table->index('decision_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the process - drop and recreate with original enum
        Schema::dropIfExists('application_history');
        
        Schema::create('application_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_seeker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_posting_id')->nullable()->constrained('job_postings')->onDelete('set null');
            
            $table->string('job_title');
            $table->string('company_name')->nullable();
            $table->enum('decision', ['hired', 'rejected']); // Original enum
            $table->text('rejection_reason')->nullable();
            $table->json('applicant_snapshot')->nullable();
            $table->json('job_snapshot')->nullable();
            $table->timestamp('decision_date');
            
            $table->timestamps();
            
            $table->index(['employer_id', 'decision']);
            $table->index(['job_seeker_id', 'decision']);
            $table->index('decision_date');
        });
    }
};
