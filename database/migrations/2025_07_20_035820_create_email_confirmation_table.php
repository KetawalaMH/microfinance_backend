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
        Schema::create('email_confirmations', function (Blueprint $table) {
            $table->id();
            $table->string('email_address');
            $table->string('otp');
            $table->integer('attempt_count');
            $table->integer('valid_count');
            $table->dateTime('attempt_release_time');
            $table->dateTime('otp_valid_time');
            $table->dateTime('otp_release_time')->nullable();
            $table->string('reference')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_confirmation');
    }
};
