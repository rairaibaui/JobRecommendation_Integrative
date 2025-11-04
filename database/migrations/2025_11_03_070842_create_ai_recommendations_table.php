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
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->decimal('match_score', 5, 2); // 0.00 to 100.00
            $table->text('explanation')->nullable();
            $table->json('matching_skills')->nullable();
            $table->string('career_growth')->nullable();
            $table->integer('rank')->default(0); // Recommendation rank
            $table->boolean('viewed')->default(false);
            $table->boolean('applied')->default(false);
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('job_posting_id');
            $table->index(['user_id', 'match_score']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
    }
};
