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
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('done_by')->constrained('users');
            $table->string('title');
            $table->string('description');
            $table->foreignId('account_id')->nullable()->constrained('saving_accounts');
            $table->foreignId('member_id')->nullable()->constrained('members');
            $table->foreignId('loan_id')->nullable()->constrained('loans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};
