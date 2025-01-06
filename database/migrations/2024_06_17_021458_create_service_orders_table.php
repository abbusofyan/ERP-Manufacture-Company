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
        Schema::create('service_quotations', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_appointment_id')->nullable();
            $table->unsignedBigInteger('service_quotation_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('pic_first_name')->nullable();
            $table->string('pic_last_name')->nullable();
            $table->string('pic_title')->nullable();
            $table->string('pic_country_code')->nullable();
            $table->string('pic_phone_number')->nullable();
            $table->string('pic_email')->nullable();
            $table->string('service_order_type')->nullable();
            $table->boolean('in_warranty')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('service_appointment_id')->references('id')->on('service_appointments');
            $table->foreign('service_quotation_id')->references('id')->on('service_quotations');
            $table->foreign('technician_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_orders');
    }
};
