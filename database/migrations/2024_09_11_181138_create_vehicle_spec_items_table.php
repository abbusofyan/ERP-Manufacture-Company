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
        Schema::create('vehicle_spec_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_spec_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('assembly_id')->nullable();
            $table->integer('qty');
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
        Schema::dropIfExists('vehicle_spec_items');
    }
};
