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
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_id');
            $table->string('type_name');
            $table->string('type_description');
            $table->float('interest_rate', 5,2);
            $table->float('penalty_rate', 5,2);
            $table->decimal('penalty_amount');
            $table->float('tax_rate',5,2);
            $table->integer('penalty_duration');
            $table->integer('age_limit');
            $table->integer('duration');
            $table->decimal('minimum_balance');
            $table->decimal('maximum_balance');
            $table->decimal('minimum_amount');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
