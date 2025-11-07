<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->string('resume_verification_status')->default('pending')->after('resume_file');
            // Status: pending, verified, needs_review, incomplete, rejected

            // $table->json('verification_flags')->nullable()->after('resume_verification_status');
            // Stores array of issues: missing_experience, missing_education, duplicate_content, etc.

            // $table->integer('verification_score')->default(0)->after('verification_flags');
            // Score 0-100 based on completeness and quality

            // $table->timestamp('verified_at')->nullable()->after('verification_score');

            // $table->text('verification_notes')->nullable()->after('verified_at');
            // Admin notes or AI feedback
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No columns were created in up(), so nothing to drop
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn([
        //         'resume_verification_status',
        //         'verification_flags',
        //         'verification_score',
        //         'verified_at',
        //         'verification_notes',
        //     ]);
        // });
    }
};
