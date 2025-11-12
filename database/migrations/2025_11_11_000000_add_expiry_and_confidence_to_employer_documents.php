<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employer_documents', function (Blueprint $table) {
            $table->date('permit_expiry_date')->nullable()->after('raw_text');
            $table->integer('confidence_score')->default(0)->after('permit_expiry_date');
        });
    }

    public function down()
    {
        Schema::table('employer_documents', function (Blueprint $table) {
            $table->dropColumn(['permit_expiry_date','confidence_score']);
        });
    }
};
