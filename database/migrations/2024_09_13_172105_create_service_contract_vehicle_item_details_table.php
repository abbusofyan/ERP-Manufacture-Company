<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_contract_vehicle_item_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('service_contract_pricing_id');
            $table->foreign('service_contract_pricing_id', 'sc_pricing_fk')->references('id')->on('service_contract_pricing')->onDelete('cascade');

            $table->unsignedBigInteger('vehicle_id');
            $table->string('vehicle_license_plate')->nullable();
            $table->foreign('vehicle_id', 'vehicle_fk')->references('id')->on('vehicles')->onDelete('cascade');

            $table->string('refrigeration_model')->nullable();
            $table->date('date_of_install')->nullable();
            $table->boolean('standby_unit')->default(false);

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
        Schema::dropIfExists('service_contract_vehicle_item_details');
    }
};
