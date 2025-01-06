<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_receipt_id');
            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts');

            $table->integer('product_id')->nullable();
            // $table->foreign('product_id')->references('id')->on('products');

            $table->integer('quantity');
            $table->integer('receive_quantity');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goods_receipt_items');
    }
};
