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
        Schema::table('saving_accounts', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'pending', 'darft'])->default('darft');
            $table->boolean('is_active')->default(true)->change();
        });//////
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saving_accounts', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->boolean('is_active')->default(false)->change();
        });
    }
};
