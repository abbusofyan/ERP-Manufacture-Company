<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('sales_orders_eco', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rs_id');
            $table->foreign('rs_id')->references('id')->on('refrigeration_sales');
            $table->text('schedule_comments')->nullable();
            $table->date('vehicle_receive_date')->nullable();
            $table->string('vehicle_id')->nullable();
            $table->json('engine_serial')->nullable();
            $table->json('evaporator_serial')->nullable();
            $table->string('refrigeration_unit_serial')->nullable();
            $table->string('nea_serial')->nullable();
            $table->json('condenser_serial')->nullable();
            $table->string('standby_unit')->nullable();
            $table->integer('status')->default(1)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales_orders_eco');
    }
}
