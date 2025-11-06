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
        Schema::table('document_validations', function (Blueprint $table) {
            $table->string('permit_number')->nullable()->after('file_hash');
            $table->index('permit_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_validations', function (Blueprint $table) {
            $table->dropIndex(['permit_number']);
            $table->dropColumn('permit_number');
        });
    }
};
