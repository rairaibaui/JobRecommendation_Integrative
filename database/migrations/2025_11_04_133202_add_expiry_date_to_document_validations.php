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
        Schema::table('document_validations', function (Blueprint $table) {
            $table->date('permit_expiry_date')->nullable()->after('validated_at');
            $table->boolean('expiry_reminder_sent')->default(false)->after('permit_expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_validations', function (Blueprint $table) {
            $table->dropColumn(['permit_expiry_date', 'expiry_reminder_sent']);
        });
    }
};
