<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employer_documents', function (Blueprint $table) {
            $table->text('raw_text')->nullable()->after('file_path');
        });
    }

    public function down()
    {
        Schema::table('employer_documents', function (Blueprint $table) {
            $table->dropColumn('raw_text');
        });
    }
};
