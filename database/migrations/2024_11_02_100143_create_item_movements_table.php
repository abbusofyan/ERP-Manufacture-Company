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
        Schema::create('item_movements', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('store_id');
            $table->string('movement_type');
            $table->string('transaction_type');
            $table->unsignedBigInteger('transaction_id'); 
            $table->float('quantity');
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('item_movements');
    }
};
