<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectContractVehicleItemDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('project_contract_vehicle_item_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_contract_pricing_id');
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('vehicle_license_plate')->nullable();
            $table->string('refrigeration_model')->nullable();
            $table->date('date_of_install')->nullable();
            $table->boolean('standby_unit')->default(false);
            $table->timestamps();

            $table->foreign('project_contract_pricing_id', 'pcv_item_pricing_fk')
                ->references('id')
                ->on('project_contract_pricing')
                ->onDelete('cascade');

            $table->foreign('vehicle_id', 'pcv_item_vehicle_fk')->references('id')->on('vehicles')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contract_vehicle_item_details');
    }
}
