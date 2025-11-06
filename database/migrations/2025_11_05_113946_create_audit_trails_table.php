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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('validation_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('admin_email')->nullable();
            $table->string('action'); // view, approve, reject, request_upload
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('validation_id')->references('id')->on('document_validations')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');

            $table->index('validation_id');
            $table->index('admin_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
