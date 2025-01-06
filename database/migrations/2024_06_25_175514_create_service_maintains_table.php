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
        Schema::create('service_maintains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_appointment_id')->nullable();
            $table->unsignedBigInteger('service_contract_id')->nullable();

            $table->date('date');
            $table->unsignedInteger('status');
            $table->timestamps();

            $table->foreign('service_appointment_id')->references('id')->on('service_appointments')->onDelete('cascade');
            $table->foreign('service_contract_id')->references('id')->on('service_contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_maintains');
    }
};
