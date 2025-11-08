<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resume_verification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('resume_path')->nullable();

            // Extracted fields
            $table->string('extracted_full_name')->nullable();
            $table->string('extracted_email')->nullable();
            $table->string('extracted_phone')->nullable();
            $table->date('extracted_birthday')->nullable();

            // Match booleans
            $table->boolean('match_name')->default(false);
            $table->boolean('match_email')->default(false);
            $table->boolean('match_phone')->default(false);
            $table->boolean('match_birthday')->default(false);

            // Confidence scores (0-100)
            $table->unsignedTinyInteger('confidence_name')->default(0);
            $table->unsignedTinyInteger('confidence_email')->default(0);
            $table->unsignedTinyInteger('confidence_phone')->default(0);
            $table->unsignedTinyInteger('confidence_birthday')->default(0);

            // Overall status and notes
            $table->string('overall_status')->nullable();
            $table->text('notes')->nullable();

            // Raw AI response (safe to store as text)
            $table->longText('raw_ai_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resume_verification_logs');
    }
};
