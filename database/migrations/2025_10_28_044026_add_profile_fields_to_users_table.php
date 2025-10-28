<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name');
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 20)->unique();
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'education_level')) {
                $table->string('education_level')->nullable();
            }
            if (!Schema::hasColumn('users', 'skills')) {
                $table->string('skills')->nullable();
            }
            if (!Schema::hasColumn('users', 'years_of_experience')) {
                $table->integer('years_of_experience')->nullable();
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location');
            }
        });
    }

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
            ]);
        });
    }
};
