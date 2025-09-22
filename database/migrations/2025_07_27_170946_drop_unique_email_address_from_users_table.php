<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_address_unique'); // Drop the unique constraint
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email_address'); // Add it back if you roll back
        });
    }
};
