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
        Schema::create('tsalud_vc_config', function (Blueprint $table) {
            $table->string('mail_smtp', 255);
            $table->integer('mail_port');
            $table->string('mail_username', 1024)->nullable();
            $table->string('mail_password', 1024)->nullable();
            $table->string('mail_smtp_secure', 32)->nullable();
            $table->string('mail_from_name', 1024);
            $table->string('mail_from_email', 1024);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsalud_vc_config');
    }
};
