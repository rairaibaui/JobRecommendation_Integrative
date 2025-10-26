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
            $table->string('first_name')->after('name');
            $table->string('last_name')->after('first_name');
            $table->string('phone_number', 11)->unique()->after('email');
            $table->date('date_of_birth')->after('phone_number');
            $table->string('education_level')->nullable()->after('date_of_birth');
            $table->text('skills')->nullable()->after('education_level');
            $table->integer('years_of_experience')->nullable()->after('skills');
            $table->string('location')->after('years_of_experience');
            $table->enum('user_type', ['job_seeker', 'employer'])->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone_number',
                'date_of_birth',
                'education_level',
                'skills',
                'years_of_experience',
                'location',
                'user_type'
            ]);
        });
    }
};
