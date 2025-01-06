<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->unsignedBigInteger('vendor_id');
            $table->string('supplier_name');
            $table->text('supplier_address');
            $table->string('supplier_phone');
            $table->integer('status');
            $table->text('delivery_address');
            $table->date('edd'); // Expected Delivery Date
            $table->text('remark')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key relationship with the vendors table
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
