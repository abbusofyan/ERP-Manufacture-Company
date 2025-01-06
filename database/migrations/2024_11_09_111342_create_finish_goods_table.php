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
        Schema::dropIfExists('finish_goods');
        Schema::create('finish_goods', function (Blueprint $table) {
            $table->id();
            $table->string('convert_no')->unique();
            $table->string('code');
            $table->unsignedBigInteger('assembly_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('store_id');
            $table->float('selling_price');
            $table->float('convert_qty');
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('finish_goods');
    }
};
