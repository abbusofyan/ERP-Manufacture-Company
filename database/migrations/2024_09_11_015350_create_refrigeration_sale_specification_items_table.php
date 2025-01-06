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
        Schema::create('refrigeration_sale_specification_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('rs_spec_id')->nullable(); // rerigration sale specificaion table
            $table->unsignedBigInteger('product_id');
            $table->string('planned_qty');
            $table->string('issued_qty')->nullable();
            $table->string('returned_qty')->nullable();
            $table->string('discount')->nullable();
            $table->string('foc')->nullable();
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
        Schema::dropIfExists('refrigration_sale_specification_items');
    }
};
