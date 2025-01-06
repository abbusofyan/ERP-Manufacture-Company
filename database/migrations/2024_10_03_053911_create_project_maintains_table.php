<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectMaintainsTable extends Migration
{
    public function up()
    {
        Schema::create('project_maintains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('service_appointment_id')->nullable();
            $table->unsignedBigInteger('project_contract_id');
            $table->date('date')->nullable();
            $table->string('status')->default('Pending Service');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_draft')->default(false);
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('service_appointment_id')->references('id')->on('service_appointments')->onDelete('set null');
            $table->foreign('project_contract_id')->references('id')->on('project_contracts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_maintains');
    }
}
