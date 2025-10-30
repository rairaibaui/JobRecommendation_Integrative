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
        Schema::table('users', function (Blueprint $table) {
            // For job seekers: track if they are currently employed
            $table->enum('employment_status', ['unemployed', 'employed'])->default('unemployed')->after('user_type');
            $table->string('hired_by_company')->nullable()->after('employment_status');
            $table->timestamp('hired_date')->nullable()->after('hired_by_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employment_status', 'hired_by_company', 'hired_date']);
        });
    }
};
