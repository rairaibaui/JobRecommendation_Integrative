<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResumeOutdatedAtToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('users', 'resume_outdated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('resume_outdated_at')->nullable()->after('resume_verification_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'resume_outdated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('resume_outdated_at');
            });
        }
    }
}
