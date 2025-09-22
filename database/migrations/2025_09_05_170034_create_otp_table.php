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
        Schema::create(table: 'otps', callback: function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'email')->index();
            $table->string(column: 'otp');
            $table->timestamp(column: 'expires_at'); // will be 5 minutes from now
            $table->timestamps();
            $table->boolean(column: 'is_active')->default(value: true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'otp');
    }
};
