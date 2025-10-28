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
            //  $table->string('first_name')->nullable();
            // $table->string('last_name')->nullable();
            //  $table->string('phone_number')->nullable();
            //  $table->date('date_of_birth')->nullable();
            //  $table->string('education_level')->nullable();
            //  $table->text('skills')->nullable();
            // $table->integer('years_of_experience')->nullable();
            //  $table->string('location')->nullable();
            //  $table->string('user_type')->nullable();

            // Add only new columns
            $table->string('profile_picture')->nullable()->after('email');
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
                'user_type',
            ]);
        });
    }
};
