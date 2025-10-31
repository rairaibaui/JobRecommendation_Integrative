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
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->string('company')->nullable()->after('skills');
            $table->string('employer_name')->nullable()->after('company');
            $table->string('employer_email')->nullable()->after('employer_name');
            $table->string('employer_phone')->nullable()->after('employer_email');
            $table->string('posted_date')->nullable()->after('employer_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropColumn(['company', 'employer_name', 'employer_email', 'employer_phone', 'posted_date']);
        });
    }
};
