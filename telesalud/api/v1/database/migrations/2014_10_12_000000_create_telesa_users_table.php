<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
<<<<<<< HEAD:telesalud/api/v1/database/migrations/2014_10_12_000000_create_telesa_users_table.php
        Schema::create('telesa_users', function (Blueprint $table) {
=======
        Schema::create('users', function (Blueprint $table) {
>>>>>>> develop:telesalud/api/database/migrations/2021_02_26_094144_create_users_table.php
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
<<<<<<< HEAD:telesalud/api/v1/database/migrations/2014_10_12_000000_create_telesa_users_table.php
        Schema::dropIfExists('telesa_users');
=======
        Schema::dropIfExists('users');
>>>>>>> develop:telesalud/api/database/migrations/2021_02_26_094144_create_users_table.php
    }
};
