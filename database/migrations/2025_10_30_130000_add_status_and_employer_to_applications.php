<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'status')) {
                $table->enum('status', ['pending', 'reviewing', 'accepted', 'rejected'])
                    ->default('pending')
                    ->after('job_title');
            }
            if (!Schema::hasColumn('applications', 'employer_id')) {
                $table->foreignId('employer_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('applications', 'company_name')) {
                $table->string('company_name')->nullable()->after('job_title');
            }
            if (!Schema::hasColumn('applications', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('applications', 'status_updated_at')) {
                $table->timestamp('status_updated_at')->nullable()->after('updated_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['status', 'employer_id', 'company_name', 'updated_by', 'status_updated_at']);
        });
    }
};
