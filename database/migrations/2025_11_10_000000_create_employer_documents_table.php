<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employer_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->nullable();
            $table->string('document_type')->nullable();
            $table->json('fields')->nullable();
            $table->boolean('has_signature')->default(false);
            $table->string('status')->default('REVIEW_BY_ADMIN');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employer_documents');
    }
};
