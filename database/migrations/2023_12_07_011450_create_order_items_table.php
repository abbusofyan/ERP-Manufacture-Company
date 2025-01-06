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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->text('remark')->nullable();
            $table->string('currency');
            $table->string('credit_term')->nullable();
            $table->decimal('tax', 8, 2)->nullable();
            $table->decimal('gst', 8, 2)->nullable();
            $table->string('nr')->nullable();
            $table->decimal('freight_charges', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key relationships with orders and products tables
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
