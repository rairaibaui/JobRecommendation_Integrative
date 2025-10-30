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
        Schema::create('application_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_seeker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_posting_id')->nullable()->constrained('job_postings')->onDelete('set null');
            
            $table->string('job_title');
            $table->string('company_name')->nullable();
            $table->enum('decision', ['hired', 'rejected']); // Final decision
            $table->text('rejection_reason')->nullable(); // Why rejected (if applicable)
            $table->json('applicant_snapshot')->nullable(); // Store applicant data at time of decision
            $table->json('job_snapshot')->nullable(); // Store job data at time of decision
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
        Schema::dropIfExists('application_history');
    }
};
