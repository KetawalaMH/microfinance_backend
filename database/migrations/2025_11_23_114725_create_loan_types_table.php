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
        Schema::create('loan_types', function (Blueprint $table) {
            $table->id();
            $table->string('loan_type');
            $table->string('description');
            $table->boolean('is_active')->default(true);
            $table->float('min_interest_rate');
            $table->float('max_interest_rate');
            $table->float('panelty');
            $table->enum('panelty_type', ['percentage', 'amount']);
            $table->float('max_amount');
            $table->float('min_amount');
            $table->integer('duration');
            $table->enum('duration_type', ['days', 'weeks', 'months', 'years']);
            $table->float('processing_fee');
            $table->integer('number_of_gaurantors');
            $table->integer('min_installments');
            $table->integer('max_installments');
            $table->enum('term', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->integer('min_processing_dates');
            $table->integer('max_processing_dates');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_types');
    }
};
