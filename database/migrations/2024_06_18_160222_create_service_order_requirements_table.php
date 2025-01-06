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
        Schema::create('service_order_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_order_id');
            $table->unsignedBigInteger('storage_item_id');
            $table->integer('quantity');
            $table->timestamps();

            // Add foreign key constraints if necessary
             $table->foreign('service_order_id')->references('id')->on('service_orders')->onDelete('cascade');
             $table->foreign('storage_item_id')->references('id')->on('storage_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_order_requirements');
    }
};
