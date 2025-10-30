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
        Schema::table('applications', function (Blueprint $table) {
            // Add interview tracking fields
                if (!Schema::hasColumn('applications', 'interview_date')) {
                    $table->timestamp('interview_date')->nullable()->after('status_updated_at');
                }
                if (!Schema::hasColumn('applications', 'interview_notes')) {
                    $table->text('interview_notes')->nullable()->after('interview_date');
                }
                if (!Schema::hasColumn('applications', 'interview_location')) {
                    $table->string('interview_location')->nullable()->after('interview_notes');
                }
        });

            // For SQLite: We can't modify enum directly, so we'll handle it in the Application model validation
            // The status field will accept the new values: 'for_interview', 'interviewed'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['interview_date', 'interview_notes', 'interview_location']);
        });

    }
};
