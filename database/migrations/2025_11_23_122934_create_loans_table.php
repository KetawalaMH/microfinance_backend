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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('borrower_id')->constrained('members');
            $table->foreignId('loan_type_id')->constrained('loan_types');
            $table->float('amount');
            $table->integer('duration');
            $table->integer('interest');
            $table->enum('status', ['draft', 'guranter_pending', 'pending', 'approved', 'rejected', 'due', 'paid'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->string('purpose');
            $table->date('start_date');
            $table->integer('number_of_installments');
            $table->integer('installment_amount');
            $table->foreignId('guranter1_id')->nullable()->constrained('members');
            $table->foreignId('guranter2_id')->nullable()->constrained('members');
            $table->date('next_installment_date')->nullable();
            $table->date('last_installment_date')->nullable();
            $table->integer('remaining_installments');
            $table->float('paid_amount');
            $table->float('remaining_amount');
            $table->foreignId('submitted_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('approved_date')->nullable();
            $table->date('repayment_start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
