<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('refrigeration_sale_header_type_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rs_header_type_id')->nullable();
            $table->foreign('rs_header_type_id')->references('id')->on('refrigeration_sale_header_types');


            $table->tinyInteger('type')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->decimal('discount')->nullable();
            $table->boolean('is_foc')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('refrigeration_sale_header_type_items');
    }
};
