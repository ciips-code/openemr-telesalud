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
        Schema::create('tsalud_vc', function (Blueprint $table) {
            $table->integer('pc_eid')->primary();
            $table->boolean('success');
            $table->string('message', 1024);
            $table->string('data_id', 1024);
            $table->string('data_valid_from', 1024);
            $table->string('data_valid_to', 1024);
            $table->string('data_patient_url', 1024);
            $table->string('data_medic_url', 1024);
            $table->string('data_data_url', 1024);
            $table->string('medic_secret', 1024);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsalud_vc');
    }
};
