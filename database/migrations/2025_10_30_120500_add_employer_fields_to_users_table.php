<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('users', 'business_permit_path')) {
                $table->string('business_permit_path')->nullable()->after('profile_picture');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'company_name')) {
                $table->dropColumn('company_name');
            }
            if (Schema::hasColumn('users', 'job_title')) {
                $table->dropColumn('job_title');
            }
            if (Schema::hasColumn('users', 'business_permit_path')) {
                $table->dropColumn('business_permit_path');
            }
        });
    }
};
