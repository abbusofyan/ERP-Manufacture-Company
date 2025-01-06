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
        Schema::create('engineering_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id'); //linked to refrigration sale table
            $table->unsignedBigInteger('sales_order_id'); // linked to sales order eco table
            $table->unsignedBigInteger('production_order_id');
            $table->datetime('start_date')->nullable();
            $table->datetime('completion_date')->nullable();
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
        Schema::dropIfExists('production_orders');
    }
};
