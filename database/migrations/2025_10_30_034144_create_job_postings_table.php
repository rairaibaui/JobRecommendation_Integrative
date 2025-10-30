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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('company_name')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->default('Full-time'); // Full-time, Part-time, Contract, etc.
            $table->string('salary')->nullable();
            $table->text('description')->nullable();
            $table->json('skills')->nullable(); // Store skills as JSON array
            $table->enum('status', ['active', 'closed', 'draft'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
