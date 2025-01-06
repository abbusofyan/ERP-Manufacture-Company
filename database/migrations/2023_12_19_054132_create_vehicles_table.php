<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate');
            $table->string('type');
            $table->foreignId('customer_id')->constrained(); // Chỉ sử dụng nếu bạn có bảng customers
            $table->string('end_user')->nullable();
            $table->string('vehicle_voltage')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->string('chassis_delivery')->nullable();
            $table->string('vehicle_make')->nullable();
            $table->string('model')->nullable();
            $table->string('vehicle_class')->nullable();
            $table->string('type_of_plate')->nullable();
            $table->string('refrigeration_serial_number')->nullable();
            $table->string('other_info')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};
