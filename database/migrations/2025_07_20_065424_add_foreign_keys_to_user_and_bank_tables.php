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
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('bank_id')->references('id')->on('bank_profiles')->onDelete('set null');
        });

        Schema::table('bank_profiles', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['bank_id']); // Drop FK from users -> bank_profiles
        });

        Schema::table('bank_profiles', function (Blueprint $table) {
            $table->dropForeign(['owner_id']); // Drop FK from bank_profiles -> users
        });
    }
};
