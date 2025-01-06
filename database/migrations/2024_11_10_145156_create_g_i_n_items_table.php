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
        Schema::create('gin_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gin_id');
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('requisition_item_id');
            $table->float('order_qty')->nullable();
            $table->float('issued_qty')->nullable();
            $table->float('returned_qty')->nullable();
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
        Schema::dropIfExists('gin_items');
    }
};
