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
        Schema::create('production_work_orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->foreign('sales_order_id')->references('id')->on('sales_orders_eco');

            $table->string('vehicle_class_wo')->nullable();
            $table->string('vehicle_voltage_wo')->nullable();
            $table->string('unit_model')->nullable();
            $table->decimal('box_dimension_mm_l', 10, 2)->nullable();
            $table->decimal('box_dimension_mm_w', 10, 2)->nullable();
            $table->decimal('box_dimension_mm_h', 10, 2)->nullable();;
            $table->string('pe_archie')->nullable();
            $table->string('pe_prema')->nullable();
            $table->string('addition_accessories')->nullable();
            $table->date('receipt_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('bracket')->nullable();
            $table->date('production_schedule')->nullable();
            $table->string('ibox')->nullable();
            $table->boolean('final_assy')->nullable();
            $table->boolean('testing')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->boolean('log_card')->nullable();
            $table->string('dummy_box')->nullable();
            $table->string('final_delivery')->nullable();

            $table->text('remark')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_work_orders');
    }
};
