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
        Schema::create('tsalud_vc_notify', function (Blueprint $table) {
            $table->integer('pc_eid')->primary();
            $table->string('vc_secret', 1024);
            $table->string('vc_medic_secret', 1024);
            $table->string('vc_status', 1024);
            $table->string('vc_medic_attendance_date', 1024);
            $table->string('vc_patient_attendance_date', 1024);
            $table->string('vc_start_date', 1024);
            $table->string('vc_finish_date', 1024);
            $table->string('vc_extra', 1024);
            $table->string('topic', 1024);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsalud_vc_notify');
    }
};
