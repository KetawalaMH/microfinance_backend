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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('NIC')->unique();
            $table->string('full_name');
            $table->string('email')->unique()->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('address');
            $table->date('dob')->nullable();
            $table->string('occupation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('member_type_id')->constrained('member_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
