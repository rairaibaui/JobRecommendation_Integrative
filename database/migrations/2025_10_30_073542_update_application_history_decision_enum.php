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
        
        // 1. Rename old table
        Schema::rename('application_history', 'application_history_old');
        
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
        
        // 3. Copy data from old table
        DB::statement('INSERT INTO application_history SELECT * FROM application_history_old');
        
        // 4. Drop old table
        Schema::dropIfExists('application_history_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the process
        Schema::rename('application_history', 'application_history_new');
        
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
        
        // Copy back only hired and rejected records
        DB::statement("INSERT INTO application_history SELECT * FROM application_history_new WHERE decision IN ('hired', 'rejected')");
        
        Schema::dropIfExists('application_history_new');
    }
};
