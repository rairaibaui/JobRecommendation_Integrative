<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employer_documents', function (Blueprint $table) {
            $table->string('review_reason')->nullable()->after('status');
            $table->boolean('reviewed_by_admin')->default(false)->after('review_reason');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by_admin');
        });
    }

    public function down()
    {
        Schema::table('employer_documents', function (Blueprint $table) {
            $table->dropColumn(['review_reason','reviewed_by_admin','reviewed_at']);
        });
    }
};
