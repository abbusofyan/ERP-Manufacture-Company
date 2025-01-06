<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('refrigeration_sale_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rs_id')->nullable();
            $table->foreign('rs_id')->references('id')->on('refrigeration_sales');
            $table->string('header_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->string('vehicle_license_plate')->nullable();
            $table->string('vehicle_chassis_no')->nullable();
            $table->string('vehicle_voltage')->nullable();
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_class')->nullable();
            $table->string('vehicle_type_of_plate')->nullable();
            $table->string('vehicle_mileage')->nullable();
            $table->string('vehicle_running_hours')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('refrigeration_sale_headers');
    }
};
