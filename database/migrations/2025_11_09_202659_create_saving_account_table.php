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
        Schema::create('saving_accounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('member_id');
            $table->string('account_number');
            $table->float('current_balance');
            $table->float('initial_deposite');
            $table->unsignedBigInteger('account_type_id');
            $table->string('NIC_No');
            $table->string('phone_number');
            $table->string('email_address');
            $table->string('address');

            // ✅ Add these columns before defining foreign keys
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();

            // ✅ Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('bank_id')->references('id')->on('bank_profiles')->onDelete('cascade');

            $table->string('status')->nullable()->default('draft');
            $table->text('NIC_doc')->nullable();
            $table->text('proof_of_address')->nullable();
            $table->text('deposit_slip')->nullable();
            $table->text('application_form')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_account');
    }
};
