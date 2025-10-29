<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('users', 'summary')) {
                $table->text('summary')->nullable();
            }
            if (!Schema::hasColumn('users', 'education')) {
                $table->json('education')->nullable();
            }
            if (!Schema::hasColumn('users', 'experiences')) {
                $table->json('experiences')->nullable();
            }
            if (!Schema::hasColumn('users', 'languages')) {
                $table->string('languages')->nullable();
            }
            if (!Schema::hasColumn('users', 'portfolio_links')) {
                $table->text('portfolio_links')->nullable();
            }
            if (!Schema::hasColumn('users', 'availability')) {
                $table->string('availability')->nullable();
            }
            if (!Schema::hasColumn('users', 'resume_file')) {
                $table->string('resume_file')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'summary',
                'education',
                'experiences',
                'languages',
                'portfolio_links',
                'availability',
                'resume_file'
            ]);
        });
    }
};